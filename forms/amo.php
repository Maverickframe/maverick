<?php
/**
 * The lead endpoint for EVERY form on the site. HubSpot is the only CRM.
 *
 * ⚠️ Why is it still called amo.php? amoCRM was retired on 2026-07-15 and none of
 * its code is left in here — but this path is baked into shipped JS
 * (src/js/components/contacts.js, quiz.js, calculator.js) AND into the bundles
 * already cached in visitors' browsers. Renaming the file would silently drop
 * live leads. The name is legacy; the behaviour is not.
 *
 * Contract — do not break, the whole funnel hangs off it:
 *  - reads CAPITALIZED $_POST keys: Name, Phone / WhatsApp, Email, title, Message.
 *  - any field NOT in $knownKeys is appended to the message verbatim, so a new
 *    lead magnet needs zero handler changes — just post extra fields.
 *  - echoes exactly "Success"; contacts.js turns that into the success UI and the
 *    dataLayer `lead_form` push, which IS the Google Ads conversion.
 *  - UTM / gclid / hubspotutk are read from $_POST and cookies inside hubspot.php.
 */

require_once __DIR__ . '/hubspot.php';

$email      = trim((string) ($_POST['Email'] ?? ''));
$phone      = trim((string) ($_POST['Phone'] ?? $_POST['WhatsApp'] ?? ''));
$messageRaw = (string) ($_POST['Message'] ?? '');

// Generic capture: append any extra form fields (Company, Role, Budget, plan link,
// quiz answers, calculator selections, …) to the message, so new forms need NO
// per-form handler code.
$knownKeys = ['Name','Phone','WhatsApp','Email','title','tag','Message','utm_source','utm_content','utm_medium','utm_campaign','utm_term','referrerLast','action','_wpnonce','lead_event','form_name','form_type','hubspotutk','ga_client_id','gclid'];
$extraLines = [];
foreach ($_POST as $fieldKey => $fieldVal) {
    if (in_array($fieldKey, $knownKeys, true)) { continue; }
    if (is_array($fieldVal)) { $fieldVal = implode(', ', $fieldVal); }
    $fieldVal = trim((string) $fieldVal);
    if ($fieldVal === '') { continue; }
    $extraLines[] = $fieldKey . ': ' . mb_substr($fieldVal, 0, 500);
}
if ($extraLines) {
    $messageRaw = trim($messageRaw . "\n\n" . implode("\n", $extraLines));
}

// No email and no phone = nothing to send. contacts.js treats anything other than
// "Success" as a failure and does not fire the conversion.
if ($email === '' && $phone === '') {
    echo "Error";
    return;
}

// Fire-and-forget: the dispatch defers to shutdown and ends the response first
// (see mfs_hs_finish_request in hubspot.php), so the visitor is never held by it.
mfs_hubspot_submit([
    'email'      => $email,
    'phone'      => $phone,
    'firstname'  => trim((string) ($_POST['Name'] ?? '')),
    'message'    => $messageRaw,
    'form_name'  => trim((string) ($_POST['form_name'] ?? '')),
    'lead_event' => trim((string) ($_POST['lead_event'] ?? '')),
    'form_page'  => trim((string) ($_POST['title'] ?? '')),
]);

echo "Success";
