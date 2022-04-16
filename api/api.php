<?php

require_once('../core/core.php');

///////////////////////////////
////// SIMPLE API EXAMPLE /////
///////////////////////////////

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'errors' => []
];

if (!checkApiAuthorization()) {
    $response['description'] = 'Вы не авторизованы для получения данных об описании объектов';
    $response['errors'][] = 'access denied';
    echo json_encode($response);
    exit;
}

if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    $response['errors']['id'] = 'required parameter';
    echo json_encode($response);
    exit;
}

//simple token verification
$dbq = $db->prepare('SELECT description FROM objects WHERE id = :id');
$dbq->bindValue(':id', $id);
$dbq->execute();
$object = $dbq->fetch();
if (!empty($object)) {
    $response['success'] = true;
    $response['description'] = $object['description'];
    echo json_encode($response);
    exit;
} else {
    $response['description'] = 'Информация об объекте отсутствует';
    $response['errors'][] = 'requested data not found';
    echo json_encode($response);
    exit;
}
