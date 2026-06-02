<?php
$subdomain     = 'global57design'; // поддомен AmoCRM
$client_secret = 'hlu18gySkcwtozhL9oMhrhUPhHY1GlHeYYqRiP6sIsCoXubEf2x9f7FaYt6MfroB'; // Секретный ключ
$client_id     = '0ef37079-f08a-406c-a99d-48d44c5b74c1'; // ID интеграции
$redirect_uri  = 'https://maverickframe.com/wp-content/themes/maverickframe/forms/amo.php';
$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImIxZTZiNTc4ZWI5MmRiNmUxMjEyNzBjNTMzMjU0MTZhMTM3OTIyZTFkMDUzNzM0NjcxNDJjMWQ2YmU2NmNhOGQ0Y2FhNjU0MzJkY2M4NzliIn0.eyJhdWQiOiIwZWYzNzA3OS1mMDhhLTQwNmMtYTk5ZC00OGQ0NGM1Yjc0YzEiLCJqdGkiOiJiMWU2YjU3OGViOTJkYjZlMTIxMjcwYzUzMzI1NDE2YTEzNzkyMmUxZDA1MzczNDY3MTQyYzFkNmJlNjZjYThkNGNhYTY1NDMyZGNjODc5YiIsImlhdCI6MTcyMTkzMjUxNiwibmJmIjoxNzIxOTMyNTE2LCJleHAiOjE4Nzk2MzIwMDAsInN1YiI6IjEwNjc4MTA2IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxODU3NzQyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiOWI5ODgzYzItNWMyYi00ODc2LWE5ZGItOTg5YjE4MzE2NjliIn0.gzSJqWMUFT-1dZ2B-SV8aNNg8DS7kL4K7xm0dQrrcx9tCkFfEun2R17zr7tV1EuQwXkAyaprQSAfu4J1C46okzrAFKt2pWEyY7XSX68SC2Mo02mxZqX2v7gAYeMGDsaDWeMPOwDTu0C7ao07I5nQq3UFbC_fDyMcmgpO-JEzQELP1nU0yB0Ccb-0wQ6IxRhkszccQhiF6YZ6WKoTnb4PevGIUWpN3AkmCUq7gOHFHp6Gh90xTsTzR3rOZwaF7cCKHIV29iIAk4hA-hT3OJXvv36CtjBu6YtHpVRirL360p51Z-Yp9PT7m-EbPGJrWZqjXbUXHjuH8IUbFEBiLurnCQ';

$name = htmlspecialchars($_POST['Name'] ?? '', ENT_NOQUOTES, 'UTF-8');
$phone = htmlspecialchars($_POST['Phone'] ?? $_POST['WhatsApp'] ?? '', ENT_NOQUOTES, 'UTF-8');
$email = htmlspecialchars($_POST['Email'] ?? '', ENT_NOQUOTES,'UTF-8');
$target = htmlspecialchars($_POST['title'] ?? '', ENT_NOQUOTES,'UTF-8');
$message = htmlspecialchars($_POST['Message'] ?? '', ENT_NOQUOTES,'UTF-8');
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

echo "Success";