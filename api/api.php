<?php
////////////////////////////////
////// SIMPLE API EXAMPLE //////
////////////////////////////////

$response = [
    'success' => false,
    'errors' => []
];

if (!checkApiAuthorization()) {
    $counter = 1;
    if (isset($_SESSION['u-access-counter'])) {
        $counter = $_SESSION['u-access-counter'] + 1;
    }
    $_SESSION['u-access-counter'] = $counter;

    $response['description'] = '<span class="error">[информация об объекте не загружена]:</span><br>Системой зарегистрировано ' . declWord($counter, ['попытка', 'попытки', 'попыток']) . ' получения данных неавторизованным пользователем. Пожалуйста, авторизуйтесь.';
    $response['errors'][] = 'Access denied';

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);

    exit;
}

/////////////////////////////////
///// GET/MESSAGE ///////////////
/////////////////////////////////

if (isset($_REQUEST['message-send-update-counter'])) {
    $dbq = $db->prepare('UPDATE demo_settings SET value = value + 1 WHERE name = \'messages-send-counter\'');
    if ($dbq->execute()) {
        $response['success'] = true;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);

    exit;
}

if (isset($_REQUEST['message-recieved-update-counter'])) {
    $dbq = $db->prepare('UPDATE demo_settings SET value = value + 1 WHERE name = \'messages-recieved-counter\'');
    if ($dbq->execute()) {
        $response['success'] = true;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);

    exit;
}

/////////////////////////////////
////// OBJECTS DATA /////////////
/////////////////////////////////
if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    $response['errors']['id'] = 'required parameter';

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);

    exit;
}

//simple token verification
$dbq = $db->prepare('SELECT description FROM demo_objects WHERE id = :id');
$dbq->bindValue(':id', $id);
$dbq->execute();
$object = $dbq->fetch();
if (!empty($object)) {
    $response['success'] = true;
    $response['description'] = '<span class="success">[информация об объекте загружена]:</span>' . $object['description'];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);

    exit;
} else {
    $response['description'] = 'Информация об объекте отсутствует';
    $response['errors'][] = 'requested data not found';

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    
    exit;
}


/////////////
// helpers //
/////////////

/* Склонение существительных
 * @int $value
 * @array $words
 * @return array
 * 
 * Usage:
 * declWord(1, ['попытка', 'попытки', 'попыток']);
 */
function declWord($value, $words = [])
{
    $num = $value % 100;
    if ($num > 19) {
        $num = $num % 10;
    }

    switch ($num) {
        case 1:
            $value = $value . ' ' . $words[0];
            break;
        case 2:
        case 3:
        case 4:
            $value = $value . ' ' . $words[1];
            break;
        default:
            $value = $value . ' ' . $words[2];
            break;
    }

    return $value;
}
