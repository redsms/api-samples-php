<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU viber resend'.PHP_EOL;;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new  \Redsms\RedsmsApiSimple($login, $apiKey);

$testNumber = $config['phone'];
$viberSenderName = $config['viberSenderName'];

try {
    $textSms = 'It is test for sms!';
    $textViber = 'It is test for viber!';

    $data = [
        'to' => $testNumber,
        'text' => $textViber,
        'from' => $viberSenderName,
        'route' => \Redsms\RedsmsApiSimple::RESEND_TYPE,
        'sms.text' => $textSms,
        'viber.btnText' => 'text',
        'viber.btnUrl' => 'https://cp.redsms.ru/reports/details',
        'viber.validity' => 10
    ];
    $sendResult = $redsmsApi->sendMessage($data);
    echo json_encode($sendResult).PHP_EOL;
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    echo 'error message: '.$e->getMessage().PHP_EOL;
}
