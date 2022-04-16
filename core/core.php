<?php

require_once('config.php');
require_once('functions.php');

$errors = [];
$messages = [];

try {
    $db = new PDO('mysql:host=' . $dbserver . ';dbname=' . $dbname, $dbuser, $dbpassword);
    $db->exec("SET NAMES " . $dbcharset);
} catch (PDOException $e) {
    $errors[] = "Ошибка соединения с БД";
    $errors[] = $e->getMessage();
}

if (!isset($db) && $_SERVER['REQUEST_URI'] !== '/') {
    if (isset($_SESSION['auth'])) {
        unset($_SESSION['auth']);
    }
    header("HTTP/1.0 404 Not Found");
    exit;
}

session_start();

if (!empty($_REQUEST['unauthorize'])) {
    unset($_SESSION['auth']);
    header('Location: ./index.php');
    exit;
}