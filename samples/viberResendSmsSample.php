<?php

require 'Redsms/RedsmsApiSimple.php';

echo "viber resend \n";

$authData = include 'authData.php';
$login = $authData['login'];
$apiKey = $authData['apiKey'];

$testNumber = 'you_number';
$viberSenderName = 'you_SenderName';

$smsApi = new  \Redsms\RedsmsApiSimple($login, $apiKey);

try {
    $textSms = 'It is test for sms!';
    $textViber = 'It is test for viber!';

    $body = [
        'to' => $testNumber,
        'text' => $textViber,
        'from' => $viberSenderName,
        'route' => \Redsms\RedsmsApiSimple::RESEND_TYPE,
        'sms.text' => $textSms,
        'viber.btnText' => 'text',
        'viber.btnUrl' => 'https://cp.redsms.ru/reports/details',
        'viber.validity' => 10
    ];
    $sendResult = $smsApi->sendMessage($body);
    echo json_encode($sendResult).PHP_EOL;
} catch (\Exception $e) {
    echo "error code: ".$e->getCode()."\n";
    echo "error message: ".$e->getMessage()."\n";
}
