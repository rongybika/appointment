<?php

//Redirect if not logged in
if (!$auth->isLogged()) {
    echo $twig->render('login.html.twig', ['message' => 'Please LogIn']);
    exit;
}

echo $twig->render('settings.html.twig', ['islogged' => $auth->isLogged()]);
exit;
