<?php

require 'Redsms/RedsmsApiSimple.php';

echo "redsms.ru api test viber \n";

$authData = include 'authData.php';
$login = $authData['login'];
$apiKey = $authData['apiKey'];

$testNumber = 'you_number';
$viberSenderName = 'you_SenderName';

$redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
$lastMessageUuid = '';
try {
    $path = "./data/image/REDSMS.png";
    $files = $redsmsApi->uploadFile($path);

    $file = array_shift($files['items']);
    $buttonText = "Кнопка";
    $buttonUrl = "https://cp.redsms.ru/";
    $textViber = "Тестовое сообщение";

    if(isset($file)) {
        $imageUrl = $file['url'];
    } else {
        $imageUrl = "";
    }

    $lastMessageUuid = '';
    $data = [
        'to' => $testNumber,
        'text' => $textViber,
        'from' => $viberSenderName,
        'route' => \Redsms\RedsmsApiSimple::VIBER_TYPE,
        'viber.btnText' => $buttonText,
        'viber.btnUrl' => $buttonUrl,
        'viber.imageUrl' => $imageUrl,
    ];
    $sendResult = $redsmsApi->sendMessage($data);

    if (!empty($sendResult['items']) && $messages = $sendResult['items'] ) {
        foreach ($messages as $message) {
            echo $message['to'].":".$message['uuid']."\n";
            $lastMessageUuid = $message['uuid'];
        }
    }
    if ($lastMessageUuid) {
        echo "Get message info: \n";
        echo json_encode($redsmsApi->messageInfo($lastMessageUuid)).PHP_EOL;
        echo "wait 10 sec... \n";
        sleep(10);
        echo json_encode($redsmsApi->messageInfo($lastMessageUuid)).PHP_EOL;
    }
} catch (\Exception $e) {
    echo "error code: ".$e->getCode()."\n";
    echo "error message: ".$e->getMessage()."\n";
}

echo "complete.\n";
