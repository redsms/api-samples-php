<?php

require 'Redsms/RedsmsApiSimple.php';

echo "redsms.ru client information \n";

$authData = include 'authData.php';
$login = $authData['login'];
$apiKey = $authData['apiKey'];

$smsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
echo "Client info: \n";
echo json_encode($smsApi->clientInfo());
echo PHP_EOL;