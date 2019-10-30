<?php

//get available hours
$providerId = $_POST['providerid'];
$serviceId = $_POST['serviceid'];
$date = $_POST['date'];

//check csrf token
if ($_POST['csrf'] == $_SESSION['csrf']) {
    //check if valid data was transfered
    if (
        filter_var($providerId, FILTER_VALIDATE_INT) && filter_var($serviceId, FILTER_VALIDATE_INT)
        && DateTime::createFromFormat('Y-m-d', $date)
    ) {
        $responseArray['status'] = 'success';

        //build API url to get available hours
        $api_url = 'http://appointment.site/service/index.php/api/v1/availabilities?providerId=' . $providerId .
            '&serviceId=' . $serviceId . '&date=' . $date;

        $context = stream_context_create(array(
            'http' => array(
                'header' => 'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
            ),
        ));

        $result = file_get_contents($api_url, false, $context);

        $resultDecoded = json_decode($result);

        $responseArray['data'] = $resultDecoded;
    } else {
        $responseArray['status'] = 'error';
    }
}

echo json_encode($responseArray);
