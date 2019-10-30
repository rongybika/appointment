<?php

//Render Contact page
echo $twig->render('contact.html.twig', ['islogged' => $auth->isLogged()]);
exit;
