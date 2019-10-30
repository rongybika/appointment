<?php

//Redirect if not logged in
if (!$auth->isLogged()) {
    echo $twig->render('login.html.twig', ['message' => 'Please LogIn']);
    exit;
}

//read and destroy session message
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_POST['message'])) {
    $message .= $_POST['message'];
}

//get current user infos
$currentUser = $auth->getCurrentUser();

//retrieve user appointments
$sth = $dbh->prepare('SELECT customer.*, ea_appointments.*, ea_services.*, provider.* FROM ea_appointments 
    LEFT JOIN ea_users AS customer ON customer.id=ea_appointments.id_users_customer 
    LEFT JOIN ea_services ON ea_services.id=ea_appointments.id_services 
    LEFT JOIN ea_users AS provider ON provider.id=ea_appointments.id_users_provider 
    WHERE ea_appointments.id_users_customer=?  ORDER BY `ea_appointments`.`start_datetime` DESC');

$sth->execute([$currentUser['app_id']]);
$result = $sth->fetchAll();

//redirect and display appointments
echo $twig->render('myappointments.html.twig', ['message' => $message, 'appointments' => $result, 'islogged' => $auth->isLogged()]);
exit;
