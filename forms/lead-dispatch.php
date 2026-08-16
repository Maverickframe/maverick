<?php
/**
 * Lead dispatch — what happens to a lead AFTER the visitor has been answered.
 *
 * Replaces forms/hubspot.php (2026-08-16). HubSpot was retired: its portal stopped
 * accepting contacts on 2026-08-15 and every call it made from here was logging a
 * 402. The names went with it — nothing on this site is "hs" any more.
 *
 * Order inside the shutdown hook, and it is deliberate:
 *   1) mfs_lead_notify()  — on-disk journal (wp-content/mfs-leads.jsonl) FIRST,
 *                           then the email to the studio inbox. Local, no network
 *                           for the journal, so a lead survives everything else.
 *   2) mfs_crm_send()     — POST to our own CRM. Last on purpose: it is the
 *                           newest link in the chain and the least proven one.
 *
 * Everything here runs AFTER the response is closed (see mfs_finish_request —
 * this host is LiteSpeed, not php-fpm), so the visitor is never held by it.
 * Fire-and-forget: nothing in this file may throw into the request.
 */

// Layer 1: journal + studio email. Does not depend on any external service.
require_once __DIR__ . '/notify.php';
// Layer 2: our own CRM.
require_once __DIR__ . '/crm.php';

// Durable log lives in wp-content/ (this file is themes/<theme>/forms/).
if (!defined('MFS_LEAD_LOG_FILE')) define('MFS_LEAD_LOG_FILE', __DIR__ . '/../../../mfs-lead-dispatch.log');

/** Append one line to the dispatch log (best-effort, never throws). */
function mfs_lead_log($msg) {
    @error_log('[' . gmdate('d-M-Y H:i:s') . ' UTC] [MFS lead] ' . $msg . "\n", 3, MFS_LEAD_LOG_FILE);
}

/**
 * Идентификатор заявки. One per request, reused by every layer:
 * the journal line, the email and the CRM (as its idempotency key), so one
 * submission can be followed across all three without guessing.
 */
function mfs_lead_id() {
    static $id = null;
    if ($id === null) {
        $seed = ($_POST['Email'] ?? '') . '|' . ($_POST['Phone'] ?? '') . '|' . microtime(true) . '|' . mt_rand();
        $id = gmdate('Ymd-His') . '-' . substr(sha1($seed), 0, 10);
    }
    return $id;
}

/** GA client_id from the _ga cookie (GA1.1.XXX.YYY -> XXX.YYY). */
function mfs_lead_ga_client_id() {
    $ga = $_COOKIE['_ga'] ?? '';
    if ($ga && preg_match('/GA\d\.\d\.(\d+\.\d+)/', $ga, $m)) { return $m[1]; }
    return '';
}

/** GCLID — posted field first, else parse the _gcl_aw cookie (GCL.ts.GCLID). */
function mfs_lead_gclid() {
    if (!empty($_POST['gclid'])) { return trim((string) $_POST['gclid']); }
    $g = $_COOKIE['_gcl_aw'] ?? '';
    if ($g && preg_match('/GCL\.\d+\.(.+)$/', $g, $m)) { return $m[1]; }
    return '';
}

/**
 * The shared property map: one shape of a lead for every consumer (the email,
 * the journal, the CRM). Was mfs_hs_props(); the name was the only HubSpot thing
 * about it — the fields are ours and the attribution is Google's.
 *
 * UTM is read straight from $_POST (contacts.js resolves URL → first-touch cookie
 * and posts the five fields), so this works no matter which handler called us.
 */
function mfs_lead_props(array $contact) {
    return [
        'email'        => trim((string) ($contact['email'] ?? '')),
        'firstname'    => trim((string) ($contact['firstname'] ?? '')),
        'phone'        => trim((string) ($contact['phone'] ?? '')),
        'message'      => trim((string) ($contact['message'] ?? '')),
        'form_name'    => trim((string) ($contact['form_name'] ?? '')),
        'form_page'    => trim((string) ($contact['form_page'] ?? '')),
        'lead_event'   => trim((string) ($contact['lead_event'] ?? '')),
        'utm_source'   => trim((string) ($_POST['utm_source']   ?? ($contact['utm_source']   ?? ''))),
        'utm_medium'   => trim((string) ($_POST['utm_medium']   ?? ($contact['utm_medium']   ?? ''))),
        'utm_campaign' => trim((string) ($_POST['utm_campaign'] ?? ($contact['utm_campaign'] ?? ''))),
        'utm_term'     => trim((string) ($_POST['utm_term']     ?? ($contact['utm_term']     ?? ''))),
        'utm_content'  => trim((string) ($_POST['utm_content']  ?? ($contact['utm_content']  ?? ''))),
        'ga_client_id' => trim((string) ($_POST['ga_client_id'] ?? mfs_lead_ga_client_id())),
        'gclid'        => trim((string) mfs_lead_gclid()),
    ];
}

/**
 * Ends the HTTP response so the background dispatch cannot hold the visitor.
 *
 * PHP-FPM exposes fastcgi_finish_request(); LiteSpeed (lsphp) does NOT — its
 * equivalent is litespeed_finish_request(). Checking only for the FPM one meant
 * that on this host the guard silently did nothing and the visitor sat through
 * the whole "background" dispatch (measured 15.07.2026: a form submit answered
 * in 7.2 s). Returns which mechanism was used, so the log answers it for good.
 */
function mfs_finish_request() {
    if (function_exists('fastcgi_finish_request'))   { @fastcgi_finish_request();   return 'fastcgi'; }
    if (function_exists('litespeed_finish_request')) { @litespeed_finish_request(); return 'litespeed'; }
    return 'none';
}

/**
 * Entry point for every form on the site (was mfs_hubspot_submit).
 * Defers the whole dispatch to request shutdown and ends the response first.
 * Fire-and-forget: returns true as soon as the work is queued.
 */
function mfs_lead_submit(array $contact) {
    $email = trim((string) ($contact['email'] ?? ''));
    $phone = trim((string) ($contact['phone'] ?? ''));
    if ($email === '' && $phone === '') { return false; }

    register_shutdown_function(function () use ($contact) {
        $how = mfs_finish_request();
        @ignore_user_abort(true);
        @set_time_limit(45);
        if ($how === 'none') {
            mfs_lead_log('WARN: no finish_request() on this host - visitor is waiting for the dispatch');
        }

        // 1. Journal + studio email. Never skipped, never conditional.
        if (function_exists('mfs_lead_notify')) { mfs_lead_notify($contact); }

        // 2. Our CRM. Deliberately last: a lead that reached the journal and the
        //    inbox is already safe, and this call must not be able to change that.
        if (function_exists('mfs_crm_send')) { mfs_crm_send($contact); }
    });
    return true;
}
