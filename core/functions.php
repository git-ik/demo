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
