<?php
/**
 * HubSpot Forms API v3 — parallel lead capture (mirrors forms/amo.php).
 *
 * Sends every site lead to a SINGLE HubSpot "receiver" form, in PARALLEL with
 * amoCRM. We do NOT use HubSpot's own rendered forms (own HTML forms, PageSpeed);
 * this just POSTs server-side to the Forms v3 integration submit endpoint.
 *
 * Lead type is carried in the form_name / lead_event / form_page FIELDS — not in
 * separate forms — so new site forms need zero HubSpot setup. Workflows branch on
 * those field values.
 *
 * Fire-and-forget: this never throws and must never break the amoCRM flow.
 * EU data centre portal -> api-eu1.hsforms.com.
 *
 * CONFIG: fill MFS_HS_FORM_GUID with the receiver-form GUID from HubSpot UI.
 * While the GUID is empty the helper is a no-op (safe to ship inert).
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

/**
 * GA client_id from the _ga cookie (GA1.1.XXXXXXX.YYYYYYY -> XXXXXXX.YYYYYYY).
 */
function mfs_hs_ga_client_id() {
    $ga = $_COOKIE['_ga'] ?? '';
    if ($ga && preg_match('/GA\d\.\d\.(\d+\.\d+)/', $ga, $m)) {
        return $m[1];
    }
    return '';
}

/**
 * GCLID — prefer an explicit posted field (captured from URL on landing),
 * else parse the Google Ads _gcl_aw cookie (GCL.timestamp.GCLID).
 */
function mfs_hs_gclid() {
    if (!empty($_POST['gclid'])) {
        return trim((string) $_POST['gclid']);
    }
    $g = $_COOKIE['_gcl_aw'] ?? '';
    if ($g && preg_match('/GCL\.\d+\.(.+)$/', $g, $m)) {
        return $m[1];
    }
    return '';
}

/**
 * Submit one lead to HubSpot. Returns true on HTTP 2xx, false otherwise.
 * Never throws — any failure is logged and swallowed.
 *
 * $contact keys (all optional except an identifier):
 *   email, firstname, phone, message, form_name, form_page, lead_event
 * Attribution (ga_client_id, gclid, hubspotutk) is pulled from POST/cookies.
 * Internal: the Forms API path. Use mfs_hubspot_submit() as the entry point.
 */
function mfs_hubspot_forms_submit(array $contact) {
    if (MFS_HS_FORM_GUID === '' || MFS_HS_PORTAL_ID === '') {
        return false; // not configured yet — inert
    }

    $email = trim((string) ($contact['email'] ?? ''));
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($email === '' && $phone === '') {
        return false; // nothing to identify the contact by
    }

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

    // Context lets HubSpot stitch the submission to the tracked visitor session.
    $context = [];
    if (!empty($_COOKIE['hubspotutk'])) {
        $context['hutk'] = $_COOKIE['hubspotutk'];
    }
    $pageUri = trim((string) ($contact['page_uri'] ?? ($_SERVER['HTTP_REFERER'] ?? '')));
    if ($pageUri !== '') { $context['pageUri'] = $pageUri; }
    if (!empty($contact['form_page'])) { $context['pageName'] = $contact['form_page']; }
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $ip = trim(explode(',', $ip)[0]);
        $context['ipAddress'] = $ip;
    }

    $payload = ['fields' => $fields];
    if ($context) { $payload['context'] = $context; }

    $host = 'https://api' . (MFS_HS_REGION === 'eu1' ? '-eu1' : '') . '.hsforms.com';
    $url  = $host . '/submissions/v3/integration/submit/'
          . MFS_HS_PORTAL_ID . '/' . MFS_HS_FORM_GUID;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $out  = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code < 200 || $code > 299) {
        error_log('[MFS HubSpot] submit failed HTTP ' . $code . ' :: ' . substr((string) $out, 0, 300));
        return false;
    }
    return true;
}


/**
 * CRM Contacts API fallback — creates a contact for PHONE-ONLY leads, which the
 * Forms API silently drops (no email = no contact). Requires a private-app token
 * (MFS_HS_PRIVATE_TOKEN). Inert + logged if the token is empty. Never throws.
 */
function mfs_hubspot_crm_create(array $contact) {
    if (MFS_HS_PRIVATE_TOKEN === '') {
        error_log('[MFS HubSpot] phone-only lead but MFS_HS_PRIVATE_TOKEN empty — CRM create skipped');
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

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => MFS_HS_CRM_HOST . '/crm/v3/objects/contacts',
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => json_encode(['properties' => $props]),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . MFS_HS_PRIVATE_TOKEN,
        ],
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $out  = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code < 200 || $code > 299) {
        error_log('[MFS HubSpot] CRM create failed HTTP ' . $code . ' :: ' . substr((string) $out, 0, 300));
        return false;
    }
    return true;
}

/**
 * Entry point. Routes the lead so BOTH email and phone-only leads reach HubSpot:
 *   - email present  -> Forms API (form-submission event + hutk attribution)
 *   - phone-only      -> CRM Contacts API (creates an email-less contact)
 * Fire-and-forget; never throws, never blocks the amoCRM flow.
 */
function mfs_hubspot_submit(array $contact) {
    $email = trim((string) ($contact['email'] ?? ''));
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($email === '' && $phone === '') {
        return false; // nothing to identify the contact by
    }
    if ($email !== '') {
        return mfs_hubspot_forms_submit($contact);
    }
    return mfs_hubspot_crm_create($contact);
}
