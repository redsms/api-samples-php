<?php

require 'Redsms/RedsmsApiSimple.php';

echo "redsms.ru api test \n";

$authData = include 'authData.php';
$login = $authData['login'];
$apiKey = $authData['apiKey'];

$testNumber = 'you_number';
$smsSenderName = 'you_SenderName';

$smsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
$lastMessageUuid = '';
try {
    echo "Send sms message: \n";
    $sendResult = $smsApi->sendSMS($testNumber, 'It is test!', $smsSenderName);
    if (!empty($sendResult['items']) && $messages = $sendResult['items'] ) {
        foreach ($messages as $message) {
            echo $message['to'].":".$message['uuid']."\n";
            $lastMessageUuid = $message['uuid'];
        }
    }

    if ($lastMessageUuid) {
        echo "Get message info: \n";
        echo json_encode($smsApi->messageInfo($lastMessageUuid)).PHP_EOL;
        echo "wait 10 sec... \n";
        sleep(10);
        echo json_encode($smsApi->messageInfo($lastMessageUuid)).PHP_EOL;
    }

} catch (\Exception $e) {
    echo "error code: ".$e->getCode()."\n";
    echo "error message: ".$e->getMessage()."\n";
}

echo "complete.\n";