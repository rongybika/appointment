<?php

//get all available services
$api_url = 'http://appointment.site/service/index.php/api/v1/services';

$context = stream_context_create(array(
    'http' => array(
        'header' => 'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
    ),
));

$result = file_get_contents($api_url, false, $context);

$resultDecoded = json_decode($result);
