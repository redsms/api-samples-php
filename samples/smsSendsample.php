<?php

require __DIR__.'/../Redsms/RedsmsApiSimple.php';

$config = include __DIR__.'/../config.php';

echo 'REDSMS.RU sms send test '.PHP_EOL;

$login = $config['login'];
$apiKey = $config['apiKey'];

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);

$testNumber = $config['phone'];
$smsSenderName = $config['smsSenderName'];

$lastMessageUuid = '';
try {
    echo 'Send sms message: '.PHP_EOL;
    $sendResult = $redsmsApi->sendSMS($testNumber, 'It is test!', $smsSenderName);
    if (!empty($sendResult['items']) && $messages = $sendResult['items'] ) {
        foreach ($messages as $message) {
            echo $message['to'].':'.$message['uuid'].PHP_EOL;
            $lastMessageUuid = $message['uuid'];
        }
    }

    if ($lastMessageUuid) {
        echo 'Get message info: '.PHP_EOL;
        echo json_encode($redsmsApi->messageInfo($lastMessageUuid)).PHP_EOL;
        echo 'wait 10 sec... '.PHP_EOL;
        sleep(10);
        echo json_encode($redsmsApi->messageInfo($lastMessageUuid)).PHP_EOL;
    }

} catch (\Exception $e) {
    echo 'error code: '.$e->getCode().PHP_EOL;
    echo 'error message: '.$e->getMessage().PHP_EOL;
}

echo 'complete.'.PHP_EOL;