<?php

require_once('config.php');
require_once('functions.php');

session_start();

///////////////////////////////
/// DEFINE SYSTEM VARIABLES ///
///////////////////////////////

//errors array
$errors = [];
$errors['system'] = [];
$errors['configuration'] = [];
$errors['form']['fields'] = [];

//messages array
$messages = [];
$messages['sysinfo'] = []; //pls dont ask me for what this param ;)
$messages['success'] = [];
$messages['fails'] = [];

//values
$values['form'] = [];

///////////////////////////
/// DATABASE CONNECTION ///
///////////////////////////
try {
    $db = new PDO('mysql:host=' . $dbserver . ';dbname=' . $dbname, $dbuser, $dbpassword); // MYSQL
    $db->exec("SET NAMES " . $dbcharset);
} catch (PDOException $e) {
    $errors['configuration'][] = 'Перед началом работы с приложением необходимо посмотреть и отредактировать переменные в файле /core/config.php для соединения с MYSQL';
    $errors['configuration'][] = 'Убедитесь в том что база данных и пользователь MYSQL созданы';
    $errors['configuration'][] = $e->getMessage();
    $errors['system'][] = 'Ошибка соединения с БД';
}

/////////////////////////////
///// DEFAULT FUNCTIONS /////
/////////////////////////////

//PATH MANAGEMENT
$pd = strpos($_SERVER['REQUEST_URI'], '?');
if ($pd) {
    $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
} else {
    $path = $_SERVER['REQUEST_URI'];
}

if (!isset($db)) { //without db connection
    logout();
    if (!in_array($path, ['/'])) {
        header("HTTP/1.0 404 Not Found");
        header('Location: /');
        exit;
    }
}

//Logout
if (!empty($_REQUEST['unauthorize'])) {
    logout();
    header('Location: /index.php');
    exit;
}

$pathsList = [
    '/' => './pages/index.php',
    '/admin' => './pages/admin.php',
    '/add' => './pages/add.php',
    '/edit' => './pages/edit.php',
    '/delete' => './pages/delete.php'
];

if (in_array($path, array_keys($pathsList))) {
    require_once($pathsList[$path]);
} else {
    header("HTTP/1.0 404 Not Found");
    header('Location: /');
    exit;
}