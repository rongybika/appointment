<?php

//Render Home page
echo $twig->render('home.html.twig', ['islogged' => $auth->isLogged()]);
exit;
