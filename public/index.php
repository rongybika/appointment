<?php

if (session_id() == '') {
    session_start();
};

if (!isset($_SESSION['csrf']) || $_SESSION['csrf'] == '') {
    $length = 32;
    $_SESSION['csrf'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length);
}

require __DIR__ . '/../vendor/autoload.php';

//use Auth module
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

// Tell PHP that we're using UTF-8 strings until the end of the script
mb_internal_encoding('UTF-8');

// Tell PHP that we'll be outputting UTF-8 to the browser
mb_http_output('UTF-8');

//include config information for database
include(__DIR__ . '/../config/config.php');

$loader = new \Twig\Loader\FilesystemLoader('../templates');

//$loader->addPath($templateDir3);
$twig = new \Twig\Environment($loader);

$dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);

$config = new PHPAuthConfig($dbh, 'appointment_phpauth_config', '');
$auth = new PHPAuth($dbh, $config);

if (isset($_GET['url'])) {
    switch ($_GET['url']) {
        case '':
            require __DIR__ . '/../src/controller/home.php';
            break;
        case 'home':
            require __DIR__ . '/../src/controller/home.php';
            break;
        case 'contact':
            require __DIR__ . '/../src/controller/contact.php';
            break;
        case 'login':
            require __DIR__ . '/../src/controller/auth/login.php';
            break;
        case 'register':
            require __DIR__ . '/../src/controller/auth/register.php';
            break;
        case 'myappointments':
            require __DIR__ . '/../src/controller/myappointments.php';
            break;
        case 'logout':
            require __DIR__ . '/../src/controller/logout.php';
            break;
        case 'newappointment':
            require __DIR__ . '/../src/controller/newappointment.php';
            break;
        case 'addappointment':
            require __DIR__ . '/../src/ext/addappointment.php';
            break;
        case 'addappointment-temp':
            require __DIR__ . '/../src/controller/addappointment_temp.php';
            break;
        case 'settings':
            require __DIR__ . '/../src/controller/settings.php';
            break;
        case 'availabilities':
            require __DIR__ . '/../src/ext/availabilities.php';
            break;
        case 'providers':
            require __DIR__ . '/../src/ext/providers.php';
            break;
        case 'services':
            require __DIR__ . '/../src/ext/services.php';
            break;
        default:
            require __DIR__ . '/../src/controller/404.php';
    }
} else {
    require __DIR__ . '/../src/controller/home.php';
}
