<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU create dispatch '.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey, $config['apiUrl']);

$smsSenderName = $config['smsSenderName'];
$viberSenderName = $config['viberSenderName'];
$testNumber = $config['phone'];

$textViber = 'It is test for viber!';
$textSms = 'It is test for sms!';
$dispatchName = 'It is test dispatch api';

$data = [
    'route' => \Redsms\RedsmsApiSimple::RESEND_TYPE,
    'from' => $viberSenderName,
    'sms.from' => $smsSenderName,
    'to' => $testNumber,
    'name' => $dispatchName,
    'text' => $textViber,
    'sms.text' => $textSms,
    'sms.validity' => \Redsms\RedsmsApiSimple::VALIDITY_PERIOD_MAX,
    'validity' => \Redsms\RedsmsApiSimple::VALIDITY_PERIOD_MIN,
];

try {
    if ($response = $redsmsApi->createDispatch($data)) {
        $dispatch = $response['item'];
        $id = $dispatch['id'];
        $name = $dispatch['name'];
        $status = $dispatch['status'];
        if ($status == 'accept') {
            echo 'dispatch with id - '.$id.' started'.PHP_EOL;
        } else {
            echo 'status'.$status.PHP_EOL;
        }
    };
} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    echo 'error message: '.$e->getMessage().PHP_EOL;
}

echo 'complete.'.PHP_EOL;
