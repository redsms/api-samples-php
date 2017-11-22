<?php

require 'Redsms/RedsmsApiSimple.php';

echo "viber resend \n";

$login = 'you_login';
$apiKey = 'you_api_key';
$testNumber = 'you_number';

$smsApi = new  \Redsms\RedsmsApiSimple($login, $apiKey);

try {
    $textSms = 'It is test for sms!';
    $textViber = 'It is test for viber!';

    $body = [
        'to' => $testNumber,
        'text' => $textViber,
        'from' => 'REDSMS.RU',
        'route' => \Redsms\RedsmsApiSimple::RESEND_TYPE,
        'sms.text' => $textSms,
        'viber.btnText' => 'text',
        'viber.btnUrl' => 'https://cp.redsms.ru/reports/details',
        'viber.validity' => 10
    ];
    $sendResult = $smsApi->sendMessage($body);
    print_r($sendResult);
} catch (\Exception $e) {
    echo "error code: ".$e->getCode()."\n";
    echo "error message: ".$e->getMessage()."\n";
}
