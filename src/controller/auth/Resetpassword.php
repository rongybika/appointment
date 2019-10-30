<?php

//Redirect if not logged in
if (!$auth->isLogged()) {
    echo $twig->render('login.html.twig', ['message' => 'Please LogIn']);
    exit;
}

$result = false;

if (isset($_POST['submit'])) {
    //retireve current user info
    $currentUser = $auth->getCurrentUser();
    $resetRequest = $auth->requestReset($currentUser['email'], false);

    //retrieve token
    $sth = $dbh->prepare('SELECT token FROM phpauth_requests WHERE uid =?');
    $sth->execute([$currentUser['id']]);

    if ($tokenResult = $sth->fetchAll()) {
        $key = $tokenResult[0]['token'];
    }
    $password = $_POST['pssw'];
    $confirm_password = $_POST['psswc'];

    //attempt to modify password
    $result = $auth->resetPass($key, $password, $confirm_password);

    if ($result['error'] === true) {
        echo $twig->render('settings.html.twig', ['message' => $result['message'], 'islogged' => $auth->isLogged()]);
        exit;
    } else {
        echo $twig->render('settings.html.twig', ['confirm' => 'Pasword was modified', 'islogged' => $auth->isLogged()]);
        exit;
    }
}
