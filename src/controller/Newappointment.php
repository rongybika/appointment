<?php

//Redirect if not logged in
if (!$auth->isLogged()) {
    echo $twig->render('login.html.twig', ['message' => 'Please LogIn']);
    exit;
}

echo $twig->render('newappointment.html.twig', ['islogged' => $auth->isLogged(), 'csrf' => $_SESSION['csrf']]);
exit;
