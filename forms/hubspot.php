<?php
/**
 * HubSpot Forms API v3 — parallel lead capture (mirrors forms/amo.php).
 *
 * Sends every site lead to a SINGLE HubSpot "receiver" form, in PARALLEL with
 * amoCRM. Lead type is carried in form_name / lead_event / form_page FIELDS,
 * so new site forms need zero HubSpot setup.
 *
 * Hardened 2026-06-24:
 *  - ASYNC: dispatch runs after fastcgi_finish_request(), so the form/amoCRM
 *    response is flushed to the visitor first; HubSpot retries never block.
 *  - RETRIES: up to MFS_HS_MAX_ATTEMPTS on TRANSIENT failures (network/timeout/
 *    HTTP 429/5xx). A 4xx (bad payload) is logged once and NOT retried.
 *  - DURABLE LOG: every attempt -> wp-content/mfs-hubspot.log with email +
 *    HTTP code, so any miss is diagnosable.
 *
 * Fire-and-forget: never throws, never breaks the amoCRM flow.
 * EU data centre portal -> api-eu1.hsforms.com.
 */

if (!defined('MFS_HS_PORTAL_ID')) define('MFS_HS_PORTAL_ID', '148670517');
if (!defined('MFS_HS_FORM_GUID')) define('MFS_HS_FORM_GUID', '20711051-4f6d-4bb9-a179-125689472af3'); // "Website Lead (amoCRM mirror)"
if (!defined('MFS_HS_REGION'))    define('MFS_HS_REGION', 'eu1');  // EU portal

// CRM Contacts API — fallback for PHONE-ONLY leads (Forms API can't create a
// contact without email). Token from a private app (crm.objects.contacts.write).
$mfs_hs_creds = @include __DIR__ . '/hubspot-credentials.php';
if (!defined('MFS_HS_PRIVATE_TOKEN')) {
    define('MFS_HS_PRIVATE_TOKEN', is_array($mfs_hs_creds) ? trim((string) ($mfs_hs_creds['private_token'] ?? '')) : '');
}
if (!defined('MFS_HS_CRM_HOST')) define('MFS_HS_CRM_HOST', 'https://api.hubapi.com');

// --- Hardening config -------------------------------------------------------
// Durable log lives in wp-content/ (this file is themes/<theme>/forms/).
if (!defined('MFS_HS_LOG_FILE'))        define('MFS_HS_LOG_FILE', __DIR__ . '/../../../mfs-hubspot.log');
if (!defined('MFS_HS_MAX_ATTEMPTS'))    define('MFS_HS_MAX_ATTEMPTS', 3);
if (!defined('MFS_HS_CONNECT_TIMEOUT')) define('MFS_HS_CONNECT_TIMEOUT', 3);
if (!defined('MFS_HS_TIMEOUT'))         define('MFS_HS_TIMEOUT', 6);
// Seconds to wait BETWEEN attempts: after try 1 -> 2s, after try 2 -> 4s.
$GLOBALS['mfs_hs_backoff'] = [2, 4];

/** Append one line to the durable HubSpot log (best-effort, never throws). */
function mfs_hs_log($msg) {
    @error_log('[' . gmdate('d-M-Y H:i:s') . ' UTC] [MFS HubSpot] ' . $msg . "\n", 3, MFS_HS_LOG_FILE);
}

/** GA client_id from the _ga cookie (GA1.1.XXX.YYY -> XXX.YYY). */
function mfs_hs_ga_client_id() {
    $ga = $_COOKIE['_ga'] ?? '';
    if ($ga && preg_match('/GA\d\.\d\.(\d+\.\d+)/', $ga, $m)) { return $m[1]; }
    return '';
}

/** GCLID — posted field first, else parse _gcl_aw cookie (GCL.ts.GCLID). */
function mfs_hs_gclid() {
    if (!empty($_POST['gclid'])) { return trim((string) $_POST['gclid']); }
    $g = $_COOKIE['_gcl_aw'] ?? '';
    if ($g && preg_match('/GCL\.\d+\.(.+)$/', $g, $m)) { return $m[1]; }
    return '';
}

/**
 * POST JSON to HubSpot with retries. Returns true on the first 2xx.
 * Retries ONLY transient failures (curl error / HTTP 0 / 429 / 5xx); a 4xx
 * (bad payload) is logged once and NOT retried. Every attempt is logged.
 */
function mfs_hs_http_send($url, array $payload, array $extraHeaders, $label, $email) {
    $headers  = array_merge(['Content-Type: application/json'], $extraHeaders);
    $attempts = (int) MFS_HS_MAX_ATTEMPTS;
    $backoff  = $GLOBALS['mfs_hs_backoff'] ?? [2, 4];
    $body     = json_encode($payload);

    for ($i = 1; $i <= $attempts; $i++) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => (int) MFS_HS_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => (int) MFS_HS_CONNECT_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);
        $out   = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code >= 200 && $code <= 299) {
            mfs_hs_log(sprintf('%s OK HTTP %d (try %d/%d) email=%s', $label, $code, $i, $attempts, $email));
            return true;
        }

        $detail    = $err !== '' ? $err : substr((string) $out, 0, 200);
        $transient = ($errno !== 0) || ($code === 0) || ($code === 429) || ($code >= 500);
        mfs_hs_log(sprintf('%s FAIL HTTP %d errno=%d (try %d/%d) email=%s :: %s',
            $label, $code, $errno, $i, $attempts, $email, $detail));

        if (!$transient) { return false; }            // 4xx — won't fix on retry
        if ($i < $attempts) { sleep((int) ($backoff[$i - 1] ?? 4)); }
    }
    mfs_hs_log(sprintf('%s GAVE UP after %d tries email=%s', $label, $attempts, $email));
    return false;
}

/**
 * Forms API path. Builds the field/context payload and sends (with retries).
 */
function mfs_hubspot_forms_submit(array $contact) {
    if (MFS_HS_FORM_GUID === '' || MFS_HS_PORTAL_ID === '') {
        mfs_hs_log('forms SKIP — GUID/portal not configured');
        return false;
    }
    $email = trim((string) ($contact['email'] ?? ''));
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($email === '' && $phone === '') { return false; }

    $fieldMap = [
        'email'        => $email,
        'firstname'    => $contact['firstname'] ?? '',
        'phone'        => $phone,
        'message'      => $contact['message'] ?? '',
        'form_name'    => $contact['form_name'] ?? '',
        'form_page'    => $contact['form_page'] ?? '',
        'lead_event'   => $contact['lead_event'] ?? '',
        'ga_client_id' => $_POST['ga_client_id'] ?? mfs_hs_ga_client_id(),
        'gclid'        => mfs_hs_gclid(),
    ];
    $fields = [];
    foreach ($fieldMap as $name => $value) {
        $value = trim((string) $value);
        if ($value === '') { continue; }
        $fields[] = ['name' => $name, 'value' => mb_substr($value, 0, 4000)];
    }

    $context = [];
    if (!empty($_COOKIE['hubspotutk'])) { $context['hutk'] = $_COOKIE['hubspotutk']; }
    $pageUri = trim((string) ($contact['page_uri'] ?? ($_SERVER['HTTP_REFERER'] ?? '')));
    if ($pageUri !== '') { $context['pageUri'] = $pageUri; }
    if (!empty($contact['form_page'])) { $context['pageName'] = $contact['form_page']; }
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') { $context['ipAddress'] = trim(explode(',', $ip)[0]); }

    $payload = ['fields' => $fields];
    if ($context) { $payload['context'] = $context; }

    $host = 'https://api' . (MFS_HS_REGION === 'eu1' ? '-eu1' : '') . '.hsforms.com';
    $url  = $host . '/submissions/v3/integration/submit/' . MFS_HS_PORTAL_ID . '/' . MFS_HS_FORM_GUID;

    return mfs_hs_http_send($url, $payload, [], 'forms', $email !== '' ? $email : $phone);
}

/**
 * CRM Contacts API fallback — creates a contact for PHONE-ONLY leads, which the
 * Forms API silently drops (no email = no contact). Needs MFS_HS_PRIVATE_TOKEN.
 */
function mfs_hubspot_crm_create(array $contact) {
    if (MFS_HS_PRIVATE_TOKEN === '') {
        mfs_hs_log('crm SKIP — MFS_HS_PRIVATE_TOKEN empty (phone-only lead dropped)');
        return false;
    }
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($phone === '') { return false; }

    $propMap = [
        'phone'        => $phone,
        'firstname'    => $contact['firstname'] ?? '',
        'email'        => $contact['email'] ?? '',
        'message'      => $contact['message'] ?? '',
        'form_name'    => $contact['form_name'] ?? '',
        'form_page'    => $contact['form_page'] ?? '',
        'lead_event'   => $contact['lead_event'] ?? '',
        'ga_client_id' => $_POST['ga_client_id'] ?? mfs_hs_ga_client_id(),
        'gclid'        => mfs_hs_gclid(),
    ];
    $props = [];
    foreach ($propMap as $k => $v) {
        $v = trim((string) $v);
        if ($v !== '') { $props[$k] = mb_substr($v, 0, 4000); }
    }

    return mfs_hs_http_send(
        MFS_HS_CRM_HOST . '/crm/v3/objects/contacts',
        ['properties' => $props],
        ['Authorization: Bearer ' . MFS_HS_PRIVATE_TOKEN],
        'crm',
        $phone
    );
}

/**
 * Synchronous routing core (runs in the background at request shutdown):
 *   email present -> Forms API ; phone-only -> CRM Contacts API.
 */
function mfs_hubspot_do_submit(array $contact) {
    $email = trim((string) ($contact['email'] ?? ''));
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($email === '' && $phone === '') { return false; }
    return $email !== '' ? mfs_hubspot_forms_submit($contact) : mfs_hubspot_crm_create($contact);
}

/**
 * Entry point (unchanged signature). Defers the HubSpot dispatch to request
 * shutdown and flushes the response first (fastcgi_finish_request), so the
 * retries never block the visitor or the amoCRM call. Fire-and-forget.
 */
function mfs_hubspot_submit(array $contact) {
    $email = trim((string) ($contact['email'] ?? ''));
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($email === '' && $phone === '') { return false; }

    register_shutdown_function(function () use ($contact) {
        if (function_exists('fastcgi_finish_request')) { @fastcgi_finish_request(); }
        @ignore_user_abort(true);
        @set_time_limit(45);
        mfs_hubspot_do_submit($contact);
    });
    return true;
}
