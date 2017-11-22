<?php

require 'Redsms/RedsmsApiSimple.php';

echo "redsms.ru api test \n";

$login = 'igorsux';
$apiKey = 'testapikey';
$testNumber = '+79296643352';

$smsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
$lastMessageUuid = '';
try {

    echo "Client info: \n";
    print_r($smsApi->clientInfo());

    echo "Send sms message: \n";
    $sendResult = $smsApi->sendSMS($testNumber, 'It is test!', 'REDSMS.RU');
    if ($messages = ($sendResult['items'] ?? [])) {
        foreach ($messages as $message) {
            echo $message['to'].":".$message['uuid']."\n";
            $lastMessageUuid = $message['uuid'];
        }
    }

    if ($lastMessageUuid) {
        echo "Get message info: \n";
        print_r($smsApi->messageInfo($lastMessageUuid));
        echo "wait 10 sec... \n";
        sleep(10);
        print_r($smsApi->messageInfo($lastMessageUuid));
    }

} catch (\Exception $e) {
    echo "error code: ".$e->getCode()."\n";
    echo "error message: ".$e->getMessage()."\n";
}

echo "complete.\n";