<?php

namespace Appointment\Utils;

class ConnectWithService
{

    public function __construct()
    { }

    public function makeConnection(string $fname, string $lname, string $email, string $phone)
    {
        //API Url
        $url = 'http://appointment.site/service/index.php/api/v1/customers';

        //Initiate cURL.
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        //include config informations
        include(__DIR__ . '/../config/config.php');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        //prepare data to transfer
        $jsonData = array(
            'firstName' => $fname,
            'lastName' => $lname,
            'email' => $email,
            'phone' => $phone,
            'address' => '',
            'city' => '',
            'zip' => '',
            'notes' => ''
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

        return $resultDecoded['id'];
    }
}
