<?php
////////////////////////
// Параметры БД mysql //
////////////////////////

$dbserver   = "127.0.0.1";      // адрес сервера mysql
$dbname     = "test";           // название базы данных
$dbuser     = "test";           // имя пользователя mysql
$dbpassword = "test";           // пароль пользователя mysql
$dbcharset  = "utf8mb4";        // кодировка БД

/////////////////////////
////// Настройки ////////
/////////////////////////

$appName = '他看了使用说明 DEMO 他看了使用说明';  // название приложения
$appTitle = 'DEMO';                             // заголовок приложения (title)

//Пользователи
$users = [];
$users['admin']['id'] = 1;                      // id
$users['admin']['login'] = 'admin';             // логин
$users['admin']['name'] = 'Администратор';      // имя
$users['admin']['m_name'] = '';                 // отчество
$users['admin']['l_name'] = '';                 // фамилия
$users['admin']['password'] = '12345678';       // пароль

//Сервисы
$serviceMsgUrl = "http://j92506e7.beget.tech/"; // сервис доставки сообщений

//Страницы
$pagesList = [
    '/' => './pages/index.php',
    '/admin' => './pages/admin.php',
    '/add' => './pages/add.php',
    '/edit' => './pages/edit.php',
    '/delete' => './pages/delete.php',

    '/404' => './pages/service/404.php',
    '/403' => './pages/service/403.php',

    '/api/data' => './api/api.php'
];