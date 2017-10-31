<?php

require 'Redsms/RedsmsApiSimple.php';

echo "redsms.ru api test viber \n";

$login = 'you_login';
$apiKey = 'you_api_key';
$testNumber = 'you_number';

$smsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
$lastMessageUuid = '';
try {

    echo "Client info: \n";
    print_r($smsApi->clientInfo());

    $path = "/var/run/data/image/REDSMS.png";
    $files = $smsApi->uploadFile($path);

    echo "Файлов отправлено" . count($files)  . PHP_EOL;

    $file = array_shift($files['items']);
    $buttonText = "Кнопка";
    $buttonUrl = "https://cp.redsms.ru/";
    $text = "Тестовое сообщение";
    $imageUrl = "";
    if(isset($file)) {
        $imageUrl = $file['url'];
    }

    $lastMessageUuid = '';
    $sendResult = $smsApi->sendViber($testNumber, $text, 'REDSMS.RU', $buttonText, $buttonUrl, $imageUrl);
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
