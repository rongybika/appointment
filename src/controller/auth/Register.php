<?php

use Appointment\Utils\ConnectWithService;

//attempt to regist the user
if (isset($_POST['uemail']) && isset($_POST['psw'])) {

    //register the user
    $params = array('first_name' => $_POST['fname'], 'last_name' => $_POST['lname'], 'phone_number' => $_POST['phone']);
    $registeredUser = $auth->register($_POST['uemail'], $_POST['psw'], $_POST['psw-repeat'], $params, null, null);

    //if error occurred, display error message
    if ($registeredUser['error']) {
        echo $twig->render('register.html.twig', ['message' => $registeredUser['message']]);
    } else {

        //attempt to logIn after registration
        $loggedUser = $auth->login($_POST['uemail'], $_POST['psw'], 0, null);

        //if usser not logged in return to login page with error message
        if (!$auth->isLogged()) {
            echo $twig->render('login.html.twig', ['message' => $loggedUser['message']]);
        } else {

            //get current user infos
            $currentUser = $auth->getCurrentUser();

            //connect customer with service
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email = $_POST['uemail'];
            $phone = $_POST['phone'];
            $conn = new ConnectWithService();
            $resultId = $conn->makeConnection($fname, $lname, $email, $phone);

            if (!empty($resultId)) {
                //update customer infos
                $sth = $dbh->prepare('UPDATE appointment_phpauth_users SET app_id=? WHERE id =?');
                $sth->execute([$resultId, $currentUser['id']]);

                echo $twig->render('home.html.twig', ['islogged' => $auth->isLogged(), 'message' => 'Welcome!']);
                exit;
            } else {
                //if error occurred delete customer
                $sth = $dbh->prepare('DELETE FROM appointment_phpauth_users WHERE id =?');
                $sth->execute([$currentUser['id']]);

                //redirect with error message
                echo $twig->render('home.html.twig', ['message' => 'Some error occured, please try again later!']);
                exit;
            }
        }
    }
} else {

    //Render Registration page
    echo $twig->render('register.html.twig');
    exit;
}
