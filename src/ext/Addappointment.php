<?php

date_default_timezone_set('UTC');
$book = new DateTime(date("Y-m-d H:i:s"));
$book->add(new DateInterval('PT3H'));
$stringToTime = $_POST['selectedDate'] . ' ' . $_POST['selectedTime'];
$start = DateTime::createFromFormat('Y-m-d H:i', $stringToTime);
$hash = bin2hex(random_bytes(10));
$providerId = $_POST['providerId'];
$serviceId = $_POST['serviceId'];

//check csrf token match
if ($_POST['csrf'] == $_SESSION['csrf']) {
    //check if valid data was transfered
    if (
        filter_var($providerId, FILTER_VALIDATE_INT) && filter_var($serviceId, FILTER_VALIDATE_INT)
        && DateTime::createFromFormat('Y-m-d H:i', $stringToTime)
    ) {
        //build services request API url
        $api_url = 'http://appointment.site/service/index.php/api/v1/services/' . $serviceId;
        //use authorization
        $context = stream_context_create(array(
            'http' => array(
                'header' => 'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
            ),
        ));

        $result = file_get_contents($api_url, false, $context);
        $resultDecoded = json_decode($result);

        $duration = $resultDecoded->duration;

        //calculate service end time
        $end = new DateTime($stringToTime);
        $end->add(new DateInterval('PT' . $duration . 'M'));

        //get current user infos
        $currentUser = $auth->getCurrentUser();

        //get appointments API url
        $url = 'http://appointment.site/service/index.php/api/v1/appointments';

        //Initiate cURL.
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        //setup json data to transfer
        $jsonData = array(
            'book' => date_format($book, 'Y-m-d H:i:s'),
            'start' => date_format($start, 'Y-m-d H:i:s'),
            'end' => date_format($end, 'Y-m-d H:i:s'),
            'hash' => $hash,
            'notes' => '',
            'customerId' => $currentUser['app_id'],
            'providerId' => $providerId,
            'serviceId' => $serviceId
        );

        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);

        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);

        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

        //Execute the request
        $result = curl_exec($ch);

        $resultDecoded = json_decode($result, true);
        if (isset($resultDecoded['id'])) {
            $responseArray['status'] = 'success';
        }
    } else {
        $responseArray['status'] = 'error';
    }
} else {
    $responseArray['status'] = 'error';
}

echo json_encode($responseArray);
