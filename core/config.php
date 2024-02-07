<?php
////////////////////////
// Параметры БД mysql //
////////////////////////

$dbserver   = "127.0.0.1";        // адрес сервера mysql
$dbname     = "demo";             // название базы данных
$dbuser     = "demo_db_user";        // имя пользователя mysql
$dbpassword = "demo_db_password"; // пароль пользователя mysql
$dbcharset  = "utf8mb4";          // кодировка БД

///////////////////////////////
////// Общие настройки ////////
///////////////////////////////

$appName = 'DEMO';  // название приложения
$appTitle = 'DEMO';                             // заголовок приложения (title)

////////////////////
/// Пользователи ///
////////////////////
$users = [];
$users['admin']['id'] = 1;                              // id
$users['admin']['login'] = 'admin';                     // логин
$users['admin']['name'] = 'Администратор системы';      // имя
$users['admin']['m_name'] = '';                         // отчество
$users['admin']['l_name'] = '';                         // фамилия
$users['admin']['password'] = '12345678';               // пароль

$users['system-user']['id'] = 2;                        // id
$users['system-user']['login'] = 'system-user';         // логин
$users['system-user']['name'] = 'Пользователь системы'; // имя
$users['system-user']['m_name'] = '';                   // отчество
$users['system-user']['l_name'] = '';                   // фамилия
$users['system-user']['password'] = '12345678';         // пароль

///////////////
/// Сервисы ///
///////////////
$serviceMsgUrl = "http://j92506e7.beget.tech/api.php"; // сервис доставки сообщений

////////////////
/// Страницы ///
////////////////
$pagesList = [
    '/' => './pages/index.php',
    '/admin' => './pages/admin.php',
    '/add' => './pages/add.php',
    '/edit' => './pages/edit.php',
    '/delete' => './pages/delete.php',
    '/examples' => './pages/examples.php',

    '/404' => './pages/service/404.php',
    '/403' => './pages/service/403.php',

    '/api/data' => './api/api.php'
];