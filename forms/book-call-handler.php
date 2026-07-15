<?php
/**
 * Book-a-call CALENDAR — server handler (admin-ajax).
 * Action: mfs_book_call. Creates an amoCRM deal, emails the visitor an
 * .ics calendar invite, and notifies the studio. Slots are all "free"
 * for now (no real availability check — stage decision).
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

    $studioEmail = get_field('footer_email', 'options') ?: 'sale@maverickframe.com';
    $organizer   = 'sale@maverickframe.com';

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

    // HubSpot — the CRM. Fire-and-forget: hubspot.php defers to shutdown and ends
    // the response first, so the booking reply is not held by it.
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

    // 2) email the visitor an invite
    $icsPath = trailingslashit(get_temp_dir()) . 'mfs-invite-' . wp_generate_password(8, false) . '.ics';
    @file_put_contents($icsPath, $ics);

    $fromName = 'Maverick Frame Studio';
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $fromName . ' <' . $organizer . '>',
        'Reply-To: ' . $organizer,
    ];
    $clientBody =
        '<p>Hi ' . esc_html($name ?: 'there') . ',</p>' .
        '<p>Your call with Maverick Frame Studio is booked for <strong>' . esc_html($slotSummary) . '</strong>.</p>' .
        '<p>The calendar invite is attached. We will send a meeting link before the call. ' .
        'Need to reschedule? Just reply to this email.</p>' .
        '<p>— Maverick Frame Studio</p>';
    $sent = wp_mail($email, 'Your call with Maverick Frame Studio is booked', $clientBody, $headers,
        file_exists($icsPath) ? [$icsPath] : []);

    // 3) notify the studio
    $studioBody =
        '<p><strong>New call booking</strong></p>' .
        '<p>When (client): ' . esc_html($whenClient) . ($tz ? ' (' . esc_html($tz) . ')' : '') . '<br>' .
        ($studioStr ? 'When (studio): ' . esc_html($studioStr) . '<br>' : '') .
        'Name: ' . esc_html($name) . '<br>' .
        'Email: ' . esc_html($email) . '<br>' .
        'WhatsApp: ' . esc_html($whatsapp) . '<br>' .
        'Duration: ' . $duration . ' min<br>' .
        ($pageUrl ? 'Page: <a href="' . esc_url($pageUrl) . '">' . esc_html($pageUrl) . '</a>' : '') . '</p>';
    wp_mail($studioEmail, 'New call booking — ' . ($name ?: $email), $studioBody, $headers);

    if (file_exists($icsPath)) @unlink($icsPath);

    wp_send_json_success(['message' => 'Booked', 'when' => $slotSummary]);
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

