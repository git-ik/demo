<?php

/**
 * Authorization
 * 
 * @return bool
 */
function authorization($login, $password, $db): bool
{
    $dbq = $db->prepare('SELECT * FROM users WHERE login = :login');
    $dbq->bindValue(':login', $login);
    $dbq->execute();
    $user = $dbq->fetch();

    if (!empty($user) && password_verify($password, $user['password'])) {
        $_SESSION['auth'] = true;
        $_SESSION['api_token'] = md5(rand(0, 100));
        return true;
    } else {
        return false;
    }
}

/**
 * Check user authorization
 * 
 * @return bool
 */
function checkAuthorization(): bool
{
    if (isset($_SESSION['auth'])) {
        return true;
    }
    return false;
}

/**
 * Check api authorization
 */
function checkApiAuthorization(): bool
{
    if (!empty($_SESSION['api_token'])) {
        return true;
    }
    return false;
}

/**
 * Check service users
 */
function checkServiceUsers($db, $serviceUsers)
{
    $dbq = $db->prepare('SELECT * FROM users WHERE id = :id AND login = :login');
    $dbq->bindValue(':id', $serviceUsers['admin']['id']);
    $dbq->bindValue(':login', $serviceUsers['admin']['login']);
    $dbq->execute();
    $user = $dbq->fetch();
    if (empty($user)) {
        $dbq = $db->prepare('DELETE FROM users WHERE id = :id OR login = :login');
        $dbq->bindValue(':id', $serviceUsers['admin']['id']);
        $dbq->bindValue(':login', $serviceUsers['admin']['login']);
        $dbq->execute();

        $dbq = $db->prepare('INSERT INTO users SET id = 1, login = :login, password = :password, name = :name, m_name = :m_name, l_name = :l_name');
        $dbq->bindValue(':login', $serviceUsers['admin']['login']);
        $dbq->bindValue(':name', $serviceUsers['admin']['name']);
        $dbq->bindValue(':m_name', $serviceUsers['admin']['m_name']);
        $dbq->bindValue(':l_name', $serviceUsers['admin']['l_name']);
        $dbq->bindValue(':password', password_hash($serviceUsers['admin']['password'], PASSWORD_BCRYPT));
        $dbq->execute();
    }
}

/**
 * Check config params
 */
function checkConfig($serviceUsers)
{
    if (empty($serviceUsers['admin'])) {
        echo 'Ошибка конфига 1';
        die;
    }
    if (empty($serviceUsers['admin']['id'])) {
        echo 'Ошибка конфига 2';
        die;
    }
    if (empty($serviceUsers['admin']['login'])) {
        echo 'Ошибка конфига 3';
        die;
    }
    if (empty($serviceUsers['admin']['password'])) {
        echo 'Ошибка конфига 4';
        die;
    }
    if (!isset($serviceUsers['admin']['name'])) {
        echo 'Ошибка конфига 5';
        die;
    }
    if (!isset($serviceUsers['admin']['m_name'])) {
        echo 'Ошибка конфига 6';
        die;
    }
    if (!isset($serviceUsers['admin']['l_name'])) {
        echo 'Ошибка конфига 7';
        die;
    }
}

/**
 * Handle 403 error
 */
function error403()
{
    header('Location: /403');
    die;
}

/**
 * Logout
 * 
 * @return void
 */
function logout(): void
{
    unset($_SESSION['auth']);
    unset($_SESSION['api_token']);
}

/**
 * Prepare objects tree data
 * 
 * @param array $objectsList - array of objects (sql list)
 * @param int $parentId - id object
 * 
 * @return array
 */
function makeTreeArrayRecursive(&$objectsList, $parentId = 0)
{
    $treeArray = [];
    foreach ($objectsList as $key => $item) {
        if ($item['parent_id'] == $parentId) {
            if ($item['has_childs']) {
                $item['childs'] = makeTreeArrayRecursive($objectsList, $item['id']);
            }
            $treeArray[] = $item;
            unset($objectsList[$key]);
        }
    }
    return $treeArray;
}

/**
 * Render objects tree recursive
 * 
 * @param array $array - array of objects in tree present
 * @param int $parentId - id object
 * 
 * @return string
 */
function renderTreeRecursive($array, $parentId = 0)
{
    $str = '';
    foreach ($array as $item) {
        $str .= '<div class="object" id="' . $item['id'] . '">';
        if (!empty($item['childs'])) {
            $str .= ' <button title="Развернуть\свернуть дерево элементов" class="btn" onclick="toggleTreeElement(' . $item['id'] . ', this);">[+]</button>';
        }
        $str .= '<div><span class="title" onclick="ajaxLoadDescription(' . $item['id'] . ');">' . $item['title'] . '</span></div>';
        if ($item['parent_id'] != $parentId) {
            continue;
        }
        if (!empty($item['childs'])) {
            $str .= '<div class="invisible" id="invisible' . $item['id'] . '">' . renderTreeRecursive($item['childs'], $item['id']) . '</div>';
        }
        $str .= '</div>';
    }
    return $str;
}

/**
 * Delete objects recursive
 * @param int $id - id объекта
 * @param object $db - PDO
 * 
 * @return array
 */
function deleteRecursive($id, $db)
{
    $dbq = $db->prepare('DELETE FROM objects WHERE id = :id');
    $dbq->bindValue(':id', $id);
    if ($dbq->execute()) {
        $isDeleted = true;
    }

    if (!empty($object)) {
        $dbq = $db->prepare('SELECT * FROM objects WHERE parent_id = :id');
        $dbq->bindValue(':id', $id);
        $dbq->execute();
        $objects = $dbq->fetchAll();
        if (!empty($objects)) {
            foreach ($objects as $obj) {
                deleteRecursive($obj['id'], $db);
            }
        }
    }

    return $isDeleted;
}

/**
 * Update objects has_child status
 * 
 * @param int $id - id объекта
 * @param object $db - PDO
 * 
 * @return array
 */
function updateChildsStatus($id, $db)
{
    $dbq = $db->prepare('SELECT * FROM objects WHERE parent_id = :id');
    $dbq->bindValue(':id', $id);
    $dbq->execute();
    $result = $dbq->fetch();
    if (!empty($result)) {
        $dbq = $db->prepare('UPDATE objects SET has_childs = true WHERE id = :id');
        $dbq->bindValue(':id', $id);
        if ($dbq->execute()) {
            $result['success'] = true;
        } else {
            $result['success'] = false;
        }
    }
}

/**
 * Get object childs
 * 
 * @param int $id - id объекта
 * @param object $db - PDO
 * 
 * @return array
 */
function getChildsRecursive($id, $db)
{
    $idList = [];
    $dbq = $db->prepare('SELECT * FROM objects WHERE parent_id = :id');
    $dbq->bindValue(':id', $id);
    $dbq->execute();
    $objects = $dbq->fetchAll();
    if (!empty($objects)) {
        foreach ($objects as $object) {
            $idList = array_merge($idList, getChildsRecursive($object['id'], $db));
            $idList[] = $object['id'];
        }
    }
    return $idList;
}
