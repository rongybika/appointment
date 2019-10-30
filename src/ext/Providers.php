<?php

//get all providers and infos
$api_url = 'http://appointment.site/service/index.php/api/v1/providers';

$context = stream_context_create(array(
    'http' => array(
        'header' => 'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
    ),
));

$result = file_get_contents($api_url, false, $context);
$resultDecoded = json_decode($result);
$array = array();
$services = array();

//get all services and infos
$api_url = 'http://appointment.site/service/index.php/api/v1/services';
$servResult = file_get_contents($api_url, false, $context);
$servResultDecoded = json_decode($servResult);

$resultObj = new StdClass();

//construct non working days
foreach ($resultDecoded as $item) {
    $services = array();
    $disabledDays = array();
    $index = 0;
    foreach ($item->settings->workingPlan as $key => $value) {
        if ($value === null) {
            array_push($disabledDays, $index);
        }
        $index++;
    }

    //assign services to it's provider
    foreach ($servResultDecoded as $servItem) {
        if (in_array($servItem->id, $item->services)) {
            $services[] = array('id' => $servItem->id, 'name' => $servItem->name, 'duration' => $servItem->duration);
        }
    }

    $resultObj->id = $item->id;
    $resultObj->firstName = $item->firstName;
    $resultObj->lastName = $item->lastName;
    $resultObj->services = $services;
    $resultObj->disabledDays = $disabledDays;
    array_push($array, $resultObj);
}

echo json_encode($array);
