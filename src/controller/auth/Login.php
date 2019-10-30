<?php

//attempt to login the user
if (isset($_POST['uemail']) && isset($_POST['psw'])) {
    $loggedUser = $auth->login($_POST['uemail'], $_POST['psw'], 0, null);

    if (!$auth->isLogged()) {
        //if not logged in return to login page
        echo $twig->render('login.html.twig', ['message' => $loggedUser['message']]);
    } else {
        //if logged in welcome message
        echo $twig->render('home.html.twig', ['islogged' => $auth->isLogged(), 'message' => 'Welcome!']);
    }
} else {

    //Render login page
    echo $twig->render('login.html.twig');
    exit;
}
