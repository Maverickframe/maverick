<?php
require_once __DIR__ . '/hubspot.php';

/**
 * amoCRM retired 2026-07-15 — HubSpot is the only CRM now.
 *
 * This endpoint still serves EVERY form on the site: it fires
 * mfs_hubspot_submit() and echoes "Success", which is what drives the success UI
 * and the lead_form dataLayer push (= the Google Ads conversion). So we keep the
 * endpoint and skip ONLY the amoCRM dispatch. Set to true to re-enable.
 *
 * Side benefit: amoCRM can no longer take the forms down with it — the old code
 * die()'d on a non-2xx from their API, before "Success" was ever echoed, so an
 * expired token would have broken every form and stopped Ads conversions.
 */
if (!defined('MFS_AMO_ENABLED')) define('MFS_AMO_ENABLED', false);

// Optional now: the endpoint must not fatal once these credentials are removed.
$creds = @include __DIR__ . '/amo-credentials.php';
if (!is_array($creds)) { $creds = []; }
$subdomain     = $creds['subdomain']     ?? '';
$client_secret = $creds['client_secret'] ?? '';
$client_id     = $creds['client_id']     ?? '';
$redirect_uri  = $creds['redirect_uri']  ?? '';
$access_token  = $creds['access_token']  ?? '';

$name = htmlspecialchars($_POST['Name'] ?? '', ENT_NOQUOTES, 'UTF-8');
$phone = htmlspecialchars($_POST['Phone'] ?? $_POST['WhatsApp'] ?? '', ENT_NOQUOTES, 'UTF-8');
$email = htmlspecialchars($_POST['Email'] ?? '', ENT_NOQUOTES,'UTF-8');
$target = htmlspecialchars($_POST['title'] ?? '', ENT_NOQUOTES,'UTF-8');
$messageRaw = (string) ($_POST['Message'] ?? '');

// Generic capture: append any extra form fields (Company, Role, Budget, Files link, etc.)
// to the message so new lead magnets need NO per-form handler code — just pass extra fields.
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

$message = htmlspecialchars($messageRaw, ENT_NOQUOTES,'UTF-8');

// HubSpot (parallel to amoCRM, fire-and-forget). Uses RAW values, runs
// independently so it fires even if the amo path below dies.
mfs_hubspot_submit([
    'email'      => trim((string) ($_POST['Email'] ?? '')),
    'phone'      => trim((string) ($_POST['Phone'] ?? $_POST['WhatsApp'] ?? '')),
    'firstname'  => trim((string) ($_POST['Name'] ?? '')),
    'message'    => $messageRaw,
    'form_name'  => trim((string) ($_POST['form_name'] ?? '')),
    'lead_event' => trim((string) ($_POST['lead_event'] ?? '')),
    'form_page'  => trim((string) ($_POST['title'] ?? '')),
]);

$dealName = 'maverickframe.com ' . $target . ' – ' . $name . ' '. $phone . ' '.  $email; //Название создаваемой сделки
$dealTags = 'maverickframecom';  //Теги для сделки

if (!$phone && !$email) { 
    echo "Error";
    return;
}

$utm_source = trim($_POST["utm_source"] ?? '');
$utm_content = trim($_POST["utm_content"] ?? '');
$utm_medium = trim($_POST["utm_medium"] ?? '');
$utm_campaign = trim($_POST["utm_campaign"] ?? '');
$utm_term = trim($_POST["utm_term"] ?? '');
$referrerLast = trim($_POST["referrerLast"] ?? '');
$utm_referrer = $referrerLast;

$domain = 'maverickframe.com';
$pipeline_id = 8408538;
$contactFields = [];

if ($email) {
    $contactFields[] = [
        "field_code" => "EMAIL",
        "values" => [
            [
                "enum_code" => "WORK",
                "value" => $email
            ]
        ]
    ];
}

if ($phone) {
    $contactFields[] = [
        "field_code" => "PHONE",
        "values" => [
            [
                "enum_code" => "WORK",
                "value" => $phone
            ]
        ]
    ];
}

if ($message) {
    $contactFields[] = [
        "field_id" => 879083,
        "values" => [
          [
            "value" => $message
          ]
        ]
    ];
}

$data = [
    [
        'name'=> $dealName,
        'tags' => $dealTags,
        "pipeline_id" => (int) $pipeline_id,
        "_embedded" => [
            "metadata" => [
                "category" => "forms",
                "form_id" => 1,
                "form_name" => "Форма на сайте",
                "form_page" => $target,
                "form_sent_at" => strtotime(date("Y-m-d H:i:s")),
                "referer" => $domain
            ],
            "contacts" => [
                [
                    "first_name" => $name,
                    "custom_fields_values" => $contactFields
                ]
            ],
        ],
        "custom_fields_values" => [
            [
                "field_code" => 'UTM_SOURCE',
                "values" => [
                    [
                        "value" => $utm_source
                    ]
                ]
            ],
            [
                "field_code" => 'UTM_CONTENT',
                "values" => [
                    [
                        "value" => $utm_content
                    ]
                ]
            ],
            [
                "field_code" => 'UTM_MEDIUM',
                "values" => [
                    [
                        "value" => $utm_medium
                    ]
                ]
            ],
            [
                "field_code" => 'UTM_CAMPAIGN',
                "values" => [
                    [
                        "value" => $utm_campaign
                    ]
                ]
            ],
            [
                "field_code" => 'UTM_TERM',
                "values" => [
                    [
                        "value" => $utm_term
                    ]
                ]
            ],
            [
                "field_code" => 'UTM_REFERRER',
                "values" => [
                    [
                        "value" => $utm_referrer
                    ]
                ]
            ],
        ],
    ]
];

if (MFS_AMO_ENABLED) {
    $method = "/api/v4/leads/complex";

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token,
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, "https://$subdomain.amocrm.ru".$method);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, 'amo/cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEJAR, 'amo/cookie.txt');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $out = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $code = (int) $code;
    $errors = [
        301 => 'Moved permanently.',
        400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
        401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
        403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
        404 => 'Not found.',
        500 => 'Internal server error.',
        502 => 'Bad gateway.',
        503 => 'Service unavailable.'
    ];

    if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );


    $Response = json_decode($out, true);
    $Response = array_key_exists('_embedded', $Response) ? $Response['_embedded']['items'] : null;
    // $output = 'ID добавленных элементов списков:' . PHP_EOL;
    // foreach ($Response as $v)
    //     if (is_array($v))
    //         $output .= $v['id'] . PHP_EOL;
    // return $output;
}

echo "Success";