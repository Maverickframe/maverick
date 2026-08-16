<?php
/**
 * Mirrors EVERY site lead to the studio inbox and to a local journal.
 *
 * Why: HubSpot stays the CRM, but a lead must not depend on any external
 * system — not on its subscription, not on its uptime. Two independent layers:
 *   1) wp-content/mfs-leads.jsonl — written FIRST, local, needs no network.
 *      This is the real guarantee: it survives an outage of both mail and
 *      HubSpot, and every field is stored verbatim.
 *   2) an email to the studio inbox over Google Workspace SMTP.
 *
 * ⚠️ Why SMTP and not mail() / wp_mail(): the domain's MX is Google Workspace
 * and SPF is "v=spf1 include:_spf.google.com ~all". Mail sent straight from the
 * host fails SPF, and the recipient here is Google itself, which is strict about
 * its own SPF. Sending through smtp.gmail.com with authentication produces valid
 * SPF and DKIM, so the message lands in the inbox instead of spam.
 *
 * ⚠️ Why PHPMailer from core and not wp_mail(): forms/amo.php is a standalone
 * endpoint — it does NOT boot WordPress, so wp_mail() does not exist there. The
 * PHPMailer classes are self-contained and load directly from wp-includes.
 *
 * ⚠️ The subject always carries the lead's name. Gmail collapses identical
 * subjects into one thread — with a generic subject every lead would pile into
 * a single conversation and become indistinguishable.
 *
 * ⚠️ English only in everything the recipient can see. These emails get
 * forwarded, and a forwarded message must be readable by anyone on the thread.
 *
 * Called from hubspot.php inside the shutdown function, i.e. AFTER the response
 * to the visitor has already been flushed. It never delays the visitor and never
 * throws: every error is caught and written to mfs-notify.log.
 */

if (!defined('MFS_NOTIFY_LOG'))     define('MFS_NOTIFY_LOG', __DIR__ . '/../../../mfs-leads.jsonl');
if (!defined('MFS_NOTIFY_ERRLOG'))  define('MFS_NOTIFY_ERRLOG', __DIR__ . '/../../../mfs-notify.log');
// forms -> maverickframe -> themes -> wp-content -> public_html
if (!defined('MFS_NOTIFY_WP_ROOT')) define('MFS_NOTIFY_WP_ROOT', dirname(__DIR__, 4));

// SMTP credentials. Out of git (same as hubspot-credentials.php), placed on the
// server by hand.
$mfs_notify_creds = @include __DIR__ . '/notify-credentials.php';
if (!is_array($mfs_notify_creds)) { $mfs_notify_creds = []; }
if (!defined('MFS_NOTIFY_SMTP_USER'))   define('MFS_NOTIFY_SMTP_USER',   trim((string) ($mfs_notify_creds['smtp_user']   ?? '')));
if (!defined('MFS_NOTIFY_SMTP_PASS'))   define('MFS_NOTIFY_SMTP_PASS',   trim((string) ($mfs_notify_creds['smtp_pass']   ?? '')));
if (!defined('MFS_NOTIFY_SMTP_HOST'))   define('MFS_NOTIFY_SMTP_HOST',   trim((string) ($mfs_notify_creds['smtp_host']   ?? 'smtp.gmail.com')));
if (!defined('MFS_NOTIFY_SMTP_PORT'))   define('MFS_NOTIFY_SMTP_PORT',   (int)  ($mfs_notify_creds['smtp_port']   ?? 587));
if (!defined('MFS_NOTIFY_SMTP_SECURE')) define('MFS_NOTIFY_SMTP_SECURE', trim((string) ($mfs_notify_creds['smtp_secure'] ?? 'tls')));

// Recipients. Defaults live here (in git, visible in the repo) but are overridden
// by notify_to / notify_cc in notify-credentials.php — so the address list can be
// changed on the server without deploying the theme. Comma-separated for several.
if (!defined('MFS_NOTIFY_TO')) define('MFS_NOTIFY_TO', trim((string) ($mfs_notify_creds['notify_to'] ?? 'team@maverickframe.com')));
if (!defined('MFS_NOTIFY_CC')) define('MFS_NOTIFY_CC', trim((string) ($mfs_notify_creds['notify_cc'] ?? 'kuzmenkodmitry@gmail.com')));

// Local time zone used for the human-readable timestamp next to UTC.
if (!defined('MFS_NOTIFY_TZ')) define('MFS_NOTIFY_TZ', 'Europe/Minsk');

/** Splits a comma-separated list into an array of valid addresses. */
function mfs_notify_addresses($list) {
    $out = [];
    foreach (explode(',', (string) $list) as $addr) {
        $addr = trim($addr);
        if ($addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL)) { $out[] = $addr; }
    }
    return $out;
}

/** One line into the notifier's own log (best-effort, never throws). */
function mfs_notify_log($msg) {
    @error_log('[' . gmdate('d-M-Y H:i:s') . ' UTC] [MFS notify] ' . $msg . "\n", 3, MFS_NOTIFY_ERRLOG);
}

/**
 * Everything known about the lead, in three groups.
 *
 * The point of the raw group: amo.php appends unknown form fields into the
 * message body, which means a new lead magnet needs no handler changes — but it
 * also means the original field names are lost by the time we see them. So we
 * capture $_POST verbatim as well. Whatever a new form starts posting shows up
 * in the email on its own, with no code change here.
 */
function mfs_notify_fields(array $lead) {
    // Mapped properties — the same set HubSpot receives (UTM, _ga, _gcl_aw included).
    $mapped = function_exists('mfs_hs_props') ? mfs_hs_props($lead) : $lead;

    // Raw POST, minus purely technical keys that carry no information.
    $skip = ['action', '_wpnonce', 'nonce', 'dry_run'];
    $raw  = [];
    foreach ($_POST as $key => $value) {
        if (in_array($key, $skip, true)) { continue; }
        if (is_array($value)) { $value = implode(', ', $value); }
        $value = trim((string) $value);
        if ($value === '') { continue; }
        $raw[(string) $key] = mb_substr($value, 0, 2000);
    }

    // Request context: where the lead came from and what it can be stitched to.
    $ts    = time();
    $utc   = gmdate('Y-m-d H:i:s', $ts) . ' UTC';
    $local = $utc;
    try {
        $dt = new DateTime('@' . $ts);
        $dt->setTimezone(new DateTimeZone(MFS_NOTIFY_TZ));
        $local = $dt->format('Y-m-d H:i:s') . ' ' . MFS_NOTIFY_TZ;
    } catch (\Throwable $e) { /* keep UTC */ }

    $meta = [
        'page_url'       => trim((string) ($lead['page_uri'] ?? ($_SERVER['HTTP_REFERER'] ?? ''))),
        'received_local' => $local,
        'received_utc'   => $utc,
        'ip'             => trim(explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? ''))[0]),
        'user_agent'     => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
        'language'       => mb_substr((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), 0, 100),
        // Tracking cookies: what the attribution is actually stitched to. Worth
        // having in writing — when a lead shows up in HubSpot with the wrong
        // source, this is the first thing to check.
        'hubspotutk'     => trim((string) ($_COOKIE['hubspotutk'] ?? '')),
        '_ga'            => trim((string) ($_COOKIE['_ga'] ?? '')),
        '_gcl_aw'        => trim((string) ($_COOKIE['_gcl_aw'] ?? '')),
    ];

    return ['mapped' => $mapped, 'raw' => $raw, 'meta' => $meta];
}

/**
 * Layer 1 — the on-disk journal. One lead per line of JSON, everything verbatim.
 * Written before any network call, so it survives an outage of both mail and
 * HubSpot. Read with: tail -n 20 wp-content/mfs-leads.jsonl
 */
function mfs_notify_record(array $all) {
    $row = json_encode($all, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($row === false) { return false; }
    return (bool) @file_put_contents(MFS_NOTIFY_LOG, $row . "\n", FILE_APPEND | LOCK_EX);
}

/** Human-readable handle for the subject line. */
function mfs_notify_label(array $m) {
    foreach (['firstname', 'email', 'phone'] as $k) {
        $v = trim((string) ($m[$k] ?? ''));
        if ($v !== '') { return mb_substr($v, 0, 60); }
    }
    return 'no contact';
}

/** One table of label/value rows; empty values are skipped. */
function mfs_notify_table($title, array $rows, array $labels = [], array $links = []) {
    $body = '';
    $keys = $labels ? array_keys($labels) : array_keys($rows);
    foreach ($keys as $key) {
        $val = trim((string) ($rows[$key] ?? ''));
        if ($val === '') { continue; }
        $shown = nl2br(htmlspecialchars($val, ENT_QUOTES, 'UTF-8'));
        if (isset($links[$key])) {
            $href  = ($links[$key] === 'mailto' ? 'mailto:' : '') . htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
            $shown = '<a href="' . $href . '">' . $shown . '</a>';
        }
        $label = htmlspecialchars((string) ($labels[$key] ?? $key), ENT_QUOTES, 'UTF-8');
        $body .= '<tr>'
            . '<td style="padding:5px 14px 5px 0;color:#777;white-space:nowrap;vertical-align:top;font-size:13px">' . $label . '</td>'
            . '<td style="padding:5px 0;vertical-align:top;word-break:break-word">' . $shown . '</td>'
            . '</tr>';
    }
    if ($body === '') { return ''; }
    return '<p style="margin:22px 0 6px;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:#999">'
        . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse">' . $body . '</table>';
}

/** Full email body: the lead, its source, the raw payload, the request context. */
function mfs_notify_body(array $all) {
    $m = $all['mapped'];
    $r = $all['raw'];
    $x = $all['meta'];

    $html = '<div style="font:14px/1.55 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#222;max-width:680px">'
        . '<p style="margin:0 0 4px;font-size:17px"><strong>New lead from maverickframe.com</strong></p>';

    $html .= mfs_notify_table('Contact', $m, [
        'firstname' => 'Name',
        'email'     => 'Email',
        'phone'     => 'Phone / WhatsApp',
        'message'   => 'Message',
    ], ['email' => 'mailto']);

    $html .= mfs_notify_table('Source', array_merge($x, $m), [
        'form_name'    => 'Form',
        'lead_event'   => 'Event',
        'form_page'    => 'Page title',
        'page_url'     => 'Page URL',
        'utm_source'   => 'utm_source',
        'utm_medium'   => 'utm_medium',
        'utm_campaign' => 'utm_campaign',
        'utm_term'     => 'utm_term',
        'utm_content'  => 'utm_content',
        'gclid'        => 'gclid',
        'ga_client_id' => 'GA client_id',
    ], ['page_url' => 'url']);

    // No label map: prints whatever the form actually posted, field names as-is.
    $html .= mfs_notify_table('All submitted fields', $r);

    $html .= mfs_notify_table('Request', $x, [
        'received_local' => 'Received',
        'received_utc'   => 'Received (UTC)',
        'ip'             => 'IP',
        'language'       => 'Browser language',
        'user_agent'     => 'User agent',
        'hubspotutk'     => 'hubspotutk cookie',
        '_ga'            => '_ga cookie',
        '_gcl_aw'        => '_gcl_aw cookie',
    ]);

    $html .= '<p style="margin:22px 0 0;color:#999;font-size:12px">'
        . 'Replying to this email goes straight to the client.</p></div>';

    return $html;
}

/**
 * Layer 2 — email over Google Workspace SMTP.
 * Returns true once the message is accepted. Never throws.
 */
function mfs_notify_send(array $all) {
    if (MFS_NOTIFY_SMTP_USER === '' || MFS_NOTIFY_SMTP_PASS === '') {
        mfs_notify_log('SKIP - no SMTP credentials (forms/notify-credentials.php); lead is in the journal only');
        return false;
    }

    // Each file is guarded by ITS OWN class. Guarding all three on PHPMailer
    // alone silently skipped SMTP.php once PHPMailer.php was loaded, and the
    // send then died on "Class PHPMailer\PHPMailer\SMTP not found".
    $base = MFS_NOTIFY_WP_ROOT . '/wp-includes/PHPMailer/';
    $needed = [
        'Exception.php' => '\\PHPMailer\\PHPMailer\\Exception',
        'PHPMailer.php' => '\\PHPMailer\\PHPMailer\\PHPMailer',
        'SMTP.php'      => '\\PHPMailer\\PHPMailer\\SMTP',
    ];
    foreach ($needed as $file => $class) {
        if (!class_exists($class, false) && is_readable($base . $file)) {
            require_once $base . $file;
        }
    }
    if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
        mfs_notify_log('FAIL - PHPMailer not found in ' . $base);
        return false;
    }

    $m       = $all['mapped'];
    $label   = mfs_notify_label($m);
    $form    = trim((string) ($m['form_name'] ?? '')) ?: 'form';
    $replyTo = trim((string) ($m['email'] ?? ''));

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MFS_NOTIFY_SMTP_HOST;
        $mail->Port       = MFS_NOTIFY_SMTP_PORT;
        $mail->SMTPAuth   = true;
        $mail->Username   = MFS_NOTIFY_SMTP_USER;
        $mail->Password   = MFS_NOTIFY_SMTP_PASS;
        $mail->SMTPSecure = MFS_NOTIFY_SMTP_SECURE;   // tls (587) or ssl (465)
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 12;

        // From must match the authenticated mailbox, otherwise Gmail rewrites the
        // address and DKIM alignment breaks.
        $mail->setFrom(MFS_NOTIFY_SMTP_USER, 'Maverick Frame Leads');

        $to = mfs_notify_addresses(MFS_NOTIFY_TO);
        $cc = mfs_notify_addresses(MFS_NOTIFY_CC);
        if (!$to && !$cc) { mfs_notify_log('SKIP - no recipient configured'); return false; }
        // If the main mailbox is somehow unset, the copy becomes the primary
        // recipient: Gmail will not accept a message with no To at all.
        if (!$to) { $to = $cc; $cc = []; }
        foreach ($to as $addr) { $mail->addAddress($addr); }
        foreach ($cc as $addr) { if (!in_array($addr, $to, true)) { $mail->addCC($addr); } }

        if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyTo, trim((string) ($m['firstname'] ?? '')) ?: $replyTo);
        }

        $html = mfs_notify_body($all);
        $mail->isHTML(true);
        $mail->Subject = 'New lead: ' . $label . ' - ' . $form;
        $mail->Body    = $html;
        $mail->AltBody = trim(preg_replace('/\n{3,}/', "\n\n",
            html_entity_decode(strip_tags(str_replace(['</tr>', '</td>'], ["\n", ': '], $html)), ENT_QUOTES, 'UTF-8')));

        $mail->send();
        mfs_notify_log('OK sent :: ' . $label . ' / ' . $form
            . ' :: to=' . implode(',', $to) . ($cc ? ' cc=' . implode(',', $cc) : ''));
        return true;
    } catch (\Throwable $e) {
        mfs_notify_log('FAIL send :: ' . $label . ' :: ' . $e->getMessage());
        return false;
    }
}

/**
 * Entry point. Journal FIRST (it is local and cannot fail on the network),
 * email second. Fire-and-forget, nothing escapes.
 */
function mfs_lead_notify(array $lead) {
    try {
        $all = mfs_notify_fields($lead);
        mfs_notify_record($all);
        return mfs_notify_send($all);
    } catch (\Throwable $e) {
        mfs_notify_log('FATAL :: ' . $e->getMessage());
        return false;
    }
}
