<?php
/**
 * Delivery of a site lead into our own CRM (crm.maverickframe.com).
 *
 * Added 2026-08-16, in the same change that removed HubSpot. The CRM endpoint is
 * POST /api/intake/lead — see the CRM repo, docs/api-intake.md.
 *
 * Rules this file obeys, all of them learned the hard way:
 *
 *  - It runs LAST in the shutdown hook, after the journal and the email. A lead
 *    that reached wp-content/mfs-leads.jsonl is already safe; nothing here may
 *    change that.
 *  - It never throws and never echoes. The visitor has been answered long ago.
 *  - Short timeouts and NO retries inside the request. HubSpot's retry cycle with
 *    sleep() is exactly what once held form submits for 7.2 seconds.
 *  - Idempotency key = mfs_lead_id(), the same id the journal line carries. If we
 *    ever add a retry, the CRM will recognise it and not create a second deal.
 *  - Missing token = silent skip, not an error. The site keeps working when the
 *    CRM is not configured (or is being rebuilt).
 */

// Credentials — OUT of git, placed on the server by hand, same idea as the SMTP
// password. Own file (crm-credentials.php) so that rotating or revoking the CRM
// token never touches the mail config: the two are edited by different people at
// different times, and one careless overwrite of notify-credentials.php would
// take the studio's lead emails down with it.
// Falls back to notify-credentials.php if someone put the keys there instead.
$mfs_crm_creds = @include __DIR__ . '/crm-credentials.php';
if (!is_array($mfs_crm_creds)) { $mfs_crm_creds = @include __DIR__ . '/notify-credentials.php'; }
if (!defined('MFS_CRM_INTAKE_URL')) {
    define('MFS_CRM_INTAKE_URL', trim((string) (
        is_array($mfs_crm_creds) ? ($mfs_crm_creds['crm_intake_url'] ?? '') : ''
    )) ?: 'https://crm.maverickframe.com/api/intake/lead');
}
if (!defined('MFS_CRM_TOKEN')) {
    define('MFS_CRM_TOKEN', trim((string) (
        is_array($mfs_crm_creds) ? ($mfs_crm_creds['crm_token'] ?? '') : ''
    )));
}
if (!defined('MFS_CRM_CONNECT_TIMEOUT')) define('MFS_CRM_CONNECT_TIMEOUT', 3);
if (!defined('MFS_CRM_TIMEOUT'))         define('MFS_CRM_TIMEOUT', 6);

/**
 * Lead source for the CRM.
 *
 * The CRM checks this against its lead_sources reference table and quietly falls
 * back to "Website / Direct" on anything unknown, so guessing here is cheap and
 * being wrong is harmless. A gclid is the one unambiguous signal we have.
 */
function mfs_crm_lead_source(array $p) {
    if (($p['gclid'] ?? '') !== '') { return 'Google Ads'; }
    $src = strtolower(trim((string) ($p['utm_source'] ?? '')));
    $med = strtolower(trim((string) ($p['utm_medium'] ?? '')));
    if ($src === 'google' && ($med === 'cpc' || $med === 'ppc' || $med === 'paid')) { return 'Google Ads'; }
    if ($src === 'linkedin') { return 'LinkedIn'; }
    if ($src === 'instagram') { return 'Instagram'; }
    if ($med === 'organic') { return 'Organic Search'; }
    return 'Website / Direct';
}

/**
 * Builds the JSON body. Everything the form posted goes in: the CRM keeps the
 * whole payload and prints it on the deal, the same way the studio email prints
 * "All submitted fields". Field names it does not recognise are not lost.
 */
function mfs_crm_payload(array $contact) {
    $p = function_exists('mfs_lead_props') ? mfs_lead_props($contact) : $contact;

    $payload = [
        'external_id' => function_exists('mfs_lead_id') ? mfs_lead_id() : '',
        'form_name'   => ($p['form_name'] ?? '') !== '' ? $p['form_name'] : 'website',
        'email'       => $p['email'] ?? '',
        'phone'       => $p['phone'] ?? '',
        'name'        => $p['firstname'] ?? '',
        'message'     => $p['message'] ?? '',
        'lead_source' => mfs_crm_lead_source(is_array($p) ? $p : []),
        'page_url'    => trim((string) ($contact['page_uri'] ?? ($_SERVER['HTTP_REFERER'] ?? ''))),
        'submitted_at'=> gmdate('Y-m-d H:i:s') . ' UTC',
    ];

    // Attribution, for the record on the deal.
    foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'ga_client_id'] as $k) {
        if (!empty($p[$k])) { $payload[$k] = $p[$k]; }
    }

    // Raw form fields last, so they cannot overwrite anything mapped above.
    $skip = ['action', '_wpnonce', 'nonce', 'dry_run', 'hubspotutk'];
    foreach ($_POST as $key => $value) {
        $key = (string) $key;
        if (in_array($key, $skip, true) || isset($payload[$key])) { continue; }
        if (is_array($value)) { $value = implode(', ', $value); }
        $value = trim((string) $value);
        if ($value === '') { continue; }
        $payload[$key] = mb_substr($value, 0, 2000);
    }

    return $payload;
}

/**
 * Sends the lead. Returns true only on a 2xx from the CRM.
 * 201 = deal created, 200 = the CRM already had this external_id (a duplicate,
 * which is a success, not a problem).
 */
function mfs_crm_send(array $contact) {
    try {
        if (MFS_CRM_TOKEN === '') {
            mfs_lead_log('crm SKIP — no crm_token in notify-credentials.php');
            return false;
        }

        $payload = mfs_crm_payload($contact);
        $body    = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            mfs_lead_log('crm SKIP — payload is not encodable');
            return false;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => MFS_CRM_INTAKE_URL,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . MFS_CRM_TOKEN,
            ],
            CURLOPT_TIMEOUT        => (int) MFS_CRM_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => (int) MFS_CRM_CONNECT_TIMEOUT,
        ]);
        $out   = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $who = ($payload['email'] ?? '') !== '' ? $payload['email'] : ($payload['phone'] ?? '');

        if ($code >= 200 && $code <= 299) {
            mfs_lead_log(sprintf('crm OK HTTP %d id=%s who=%s :: %s',
                $code, $payload['external_id'], $who, substr((string) $out, 0, 200)));
            return true;
        }

        mfs_lead_log(sprintf('crm FAIL HTTP %d errno=%d id=%s who=%s :: %s',
            $code, $errno, $payload['external_id'], $who,
            $err !== '' ? $err : substr((string) $out, 0, 200)));
        return false;
    } catch (\Throwable $e) {
        mfs_lead_log('crm FATAL :: ' . $e->getMessage());
        return false;
    }
}
