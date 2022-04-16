<?php

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
            $str .= ' <span title="Развернуть\свернуть дерево элементов" class="btn" onclick="toggleTreeElement(' . $item['id'] . ', this);">[+]</span>';
        }
        $str .= '<span><span class="title" onclick="ajaxLoadDescription(' . $item['id'] . ');">' . $item['title'] . '</span> <span id="descr' . $item['id'] . '" class="invisible">' . $item['description'] . '</span></span><br><br>';
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
    $result = [
        'success' => false
    ];

    $sth = $db->prepare('SELECT * FROM objects WHERE id = :id');
    $sth->bindValue(':id', $id);
    $sth->execute();
    $object = $sth->fetch();
    if (empty($object)) {
        return $result;
    }

    $sth = $db->prepare('DELETE FROM objects WHERE id = :id');
    $sth->bindValue(':id', $id);
    if ($sth->execute()) {
        $result['success'] = true;
    }

    if (!empty($object)) {
        $sth = $db->prepare('SELECT * FROM objects WHERE parent_id = :id');
        $sth->bindValue(':id', $id);
        $sth->execute();
        $objects = $sth->fetchAll();

        if (!empty($objects)) {
            foreach ($objects as $obj) {
                deleteRecursive($obj['id'], $db);
            }
        }
    }

    return $result;
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
    $sth = $db->prepare('SELECT * FROM objects WHERE parent_id = :id');
    $sth->bindValue(':id', $id);
    $sth->execute();
    $result = $sth->fetch();
    if (!empty($result)) {
        $sth = $db->prepare('UPDATE objects SET has_childs = true WHERE id = :id');
        $sth->bindValue(':id', $id);
        if ($sth->execute()) {
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
    $sth = $db->prepare('SELECT * FROM objects WHERE parent_id = :id');
    $sth->bindValue(':id', $id);
    $sth->execute();
    $objects = $sth->fetchAll();
    if (!empty($objects)) {
        foreach ($objects as $object) {
            $idList = array_merge($idList, getChildsRecursive($object['id'], $db));
            $idList[] = $object['id'];
        }
    }
    return $idList;
}
