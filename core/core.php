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

//define access parameters without connection
if (!isset($db)) {
    logout();
    //without connection to database user can open only index page
    if (!in_array($_SERVER['REQUEST_URI'], ['/', '/index.php'])) {
        header("HTTP/1.0 404 Not Found");
        header('Location: /');
        exit;
    }
}

/////////////////////////////
///// DEFAULT FUNCTIONS /////
/////////////////////////////

//Logout
if (!empty($_REQUEST['unauthorize'])) {
    logout();
}
