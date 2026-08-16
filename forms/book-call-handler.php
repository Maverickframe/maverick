<?php
/**
 * Book-a-call CALENDAR — server handler (admin-ajax).
 * Action: mfs_book_call. Emails the visitor an .ics calendar invite and hands
 * the booking to mfs_hubspot_submit(), which journals the lead and notifies the
 * studio. Slots are all "free" for now (no real availability check — stage
 * decision).
 *
 * ⚠️ Both emails go through mfs_notify_smtp() (forms/notify.php), never through
 * wp_mail(). The domain's MX is Google Workspace and its SPF only authorises
 * Google, so mail sent straight from the host fails SPF at a Google recipient.
 * This handler used wp_mail() from 2026-06 until 2026-08-16 and the invites
 * almost certainly never arrived.
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/hubspot.php';

add_action('wp_ajax_mfs_book_call', 'mfs_book_call_handler');
add_action('wp_ajax_nopriv_mfs_book_call', 'mfs_book_call_handler');

function mfs_book_call_handler() {
    check_ajax_referer('pld-ajax-nonce', 'nonce');

    $name      = sanitize_text_field($_POST['Name'] ?? '');
    $email     = sanitize_email($_POST['Email'] ?? '');
    $whatsapp  = sanitize_text_field($_POST['WhatsApp'] ?? '');
    $tz        = sanitize_text_field($_POST['tz'] ?? '');
    $duration  = max(15, min(120, (int) ($_POST['duration'] ?? 30)));
    $slotIso   = sanitize_text_field($_POST['slot_iso'] ?? '');     // UTC ISO
    $studioStr = sanitize_text_field($_POST['slot_studio'] ?? '');
    $clientStr = sanitize_text_field($_POST['slot_client'] ?? '');
    $pageUrl   = esc_url_raw($_POST['page_url'] ?? '');

    if (!$email || !is_email($email)) {
        wp_send_json_error(['message' => 'A valid email is required.']);
    }
    try {
        $start = new DateTime($slotIso, new DateTimeZone('UTC'));
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Invalid time slot.']);
    }
    $end = clone $start;
    $end->modify('+' . $duration . ' minutes');

    $whenStudio = $studioStr ?: $start->format('D, M j Y H:i') . ' UTC';
    $whenClient = $clientStr ?: $whenStudio;
    $slotSummary = $whenClient . ($tz ? ' (' . $tz . ')' : '');

    // ORGANIZER has to be the mailbox the invite is actually sent from. Point it
    // at sale@ while the message leaves through team@ and Gmail reads the invite
    // as spoofed: no RSVP buttons, and the replies bounce.
    $organizer = (defined('MFS_NOTIFY_SMTP_USER') && MFS_NOTIFY_SMTP_USER !== '')
        ? MFS_NOTIFY_SMTP_USER
        : 'team@maverickframe.com';

    $ics = mfs_build_ics($start, $end, 'Intro call — Maverick Frame Studio',
        'Online call with Maverick Frame Studio. We will send a meeting link before the call.',
        $organizer, $email);

    // Safe test path: skip CRM + email, return what would be sent.
    if (!empty($_POST['dry_run'])) {
        wp_send_json_success([
            'message'   => 'Dry run — nothing sent.',
            'whenClient'=> $whenClient,
            'whenStudio'=> $studioStr,
            'tz'        => $tz,
            'page'      => $pageUrl,
            'ics'       => $ics,
        ]);
    }

    // 1) Lead text for the CRM (HubSpot — the only one since amoCRM was retired
    //    on 2026-07-15).
    $crmText = "Requested call: {$slotSummary}";
    if ($studioStr) $crmText .= " | studio time: {$studioStr}";
    $crmText .= " | duration: {$duration} min";
    if ($pageUrl) $crmText .= " | page: {$pageUrl}";

    // 2) The visitor's invite — sent at shutdown, once the response is out.
    //
    // ⚠️ Registered BEFORE mfs_hubspot_submit(): shutdown callbacks run in
    //    registration order, and the HubSpot one waits ~6s before its own work
    //    (MFS_HS_FORMS_SETTLE_SECS) plus retries. The invite must not queue up
    //    behind that — a booking confirmation is expected within seconds.
    // ⚠️ Deferred and not sent inline because an SMTP handshake costs the visitor
    //    a second or two, and the booking screen has to answer instantly.
    register_shutdown_function(function () use ($email, $name, $slotSummary, $ics) {
        if (function_exists('mfs_hs_finish_request')) { mfs_hs_finish_request(); }
        @ignore_user_abort(true);
        @set_time_limit(45);
        mfs_book_call_send_invite($email, $name, $slotSummary, $ics);
    });

    // 3) HubSpot + the studio's own notification. Fire-and-forget: hubspot.php
    //    defers to shutdown and ends the response first, so the booking reply is
    //    not held by it. Inside that shutdown mfs_lead_notify() writes the lead
    //    to mfs-leads.jsonl and emails the studio inbox — slot, timezone,
    //    duration and page included, because that email prints every posted
    //    field. THAT is the studio's booking notification; this handler does not
    //    send a second one (it used to, over wp_mail, and it never arrived).
    mfs_hubspot_submit([
        'email'      => $email,
        'phone'      => $whatsapp,
        'firstname'  => $name,
        'message'    => $crmText,
        'form_name'  => 'book_call',
        'lead_event' => 'book_call',
        'form_page'  => $pageUrl,
        'page_uri'   => $pageUrl,
    ]);

    wp_send_json_success(['message' => 'Booked', 'when' => $slotSummary]);
}

/**
 * The visitor's confirmation, with the calendar invite attached.
 *
 * ⚠️ The sender is the authenticated mailbox (team@), NOT sale@. Gmail rewrites
 * a From it did not authenticate, which breaks DKIM alignment on the way out —
 * and an invite that fails alignment is exactly what a spoofed one looks like.
 * The client sees "Maverick Frame Studio", and their reply lands in the same
 * studio inbox that already receives every lead.
 *
 * ⚠️ The subject carries the slot: Gmail collapses identical subjects into one
 * thread, and two bookings by the same person must not merge.
 */
function mfs_book_call_send_invite($email, $name, $slotSummary, $ics) {
    if (!function_exists('mfs_notify_smtp')) { return false; }

    $html =
        '<p>Hi ' . esc_html($name ?: 'there') . ',</p>' .
        '<p>Your call with Maverick Frame Studio is booked for <strong>' . esc_html($slotSummary) . '</strong>.</p>' .
        '<p>The calendar invite is attached. We will send a meeting link before the call. ' .
        'Need to reschedule? Just reply to this email.</p>' .
        '<p>— Maverick Frame Studio</p>';

    return mfs_notify_smtp([
        'to'        => $email,
        'from_name' => 'Maverick Frame Studio',
        'subject'   => 'Your call with Maverick Frame Studio is booked - ' . $slotSummary,
        'html'      => $html,
        'ics'       => $ics,
        'log'       => 'book_call invite -> ' . $email,
    ]);
}

/**
 * Build a METHOD:REQUEST VEVENT so it lands as an invite in mail clients.
 */
function mfs_build_ics($start, $end, $summary, $description, $organizer, $attendee) {
    $uid = wp_generate_uuid4() . '@maverickframe.com';
    $fmt = function ($dt) { return $dt->format('Ymd\THis\Z'); };
    $esc = function ($s) { return preg_replace('/([,;\\\\])/', '\\\\$1', str_replace("\n", '\\n', $s)); };
    $lines = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Maverick Frame Studio//Book a call//EN',
        'METHOD:REQUEST',
        'BEGIN:VEVENT',
        'UID:' . $uid,
        'DTSTAMP:' . $fmt(new DateTime('now', new DateTimeZone('UTC'))),
        'DTSTART:' . $fmt($start),
        'DTEND:' . $fmt($end),
        'SUMMARY:' . $esc($summary),
        'DESCRIPTION:' . $esc($description),
        'ORGANIZER;CN=Maverick Frame Studio:mailto:' . $organizer,
        'ATTENDEE;CN=' . $esc($attendee) . ';RSVP=TRUE:mailto:' . $attendee,
        'STATUS:CONFIRMED',
        'END:VEVENT',
        'END:VCALENDAR',
    ];
    return implode("\r\n", $lines);
}

