<?php

//logout the user
$currentUser = $auth->getCurrentUser();
$auth->logoutAll($currentUser['id']);
echo $twig->render('home.html.twig');
