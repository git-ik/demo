<?php
////////////////////////////////
//////// V1 API EXAMPLE ////////
////////////////////////////////

    $objectID = NULL;
    preg_match('/^\/api\/v1\/objects\/([0-9]+)?/u', $path, $matches);
    if (!empty($matches[1])) {
        $objectID = $matches[1];
    }

    $response = [
        'success' => false,
        'errors' => []
    ];

    //1) GET /api/v1/objects/ - получение списка объектов (с постраничной навигацией)
    if (empty($objectID) && $_SERVER['REQUEST_METHOD'] == 'GET') {
        if (empty($_GET['page'])) {
            $page = 0;
        } else {
            if (!is_numeric($_GET['page'])) {
                $response['errors']['page'][] = 'Ошибка, параметр page должен быть числовым';
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($response);
            
                exit;
            }
            $page = (int)$_GET['page'] - 1;
        }
        $offset = $page * 5;
        $dbq = $db->prepare('SELECT COUNT(id) FROM demo_objects');
        $dbq->execute();
        $pagesCount = $dbq->fetch();
        $pagesCount = ceil($pagesCount[0] / 5);

        $dbq = $db->prepare('SELECT * FROM demo_objects LIMIT 5 OFFSET ' . $offset);
        $dbq->execute();
        
        $objectsList = $dbq->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($objectsList)) {
            $response['success'] = true;
            $response['max-page'] = $pagesCount;
            $response['list'] = $objectsList;
        }

    }

    //2) POST /api/v1/objects/ - добавление объекта
    if (empty($objectID) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        //validation
        if (!isset($_POST['title'])) {
            $response['errors']['title'][] = 'Ошибка значения, обязательное поле title не определено';
        }
        if (!isset($_POST['description'])) {
            $response['errors']['title'][] = 'Ошибка значения, обязательное поле description не определено';
        }
        if (!isset($_POST['parent_id'])) {
            $response['errors']['parent_id'][] = 'Ошибка значения, обязательное поле parent_id не определено';
        }

        if (!empty($response['errors'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
        
            exit;
        }

        if (mb_strlen($_POST['title']) > 250) {
            $response['errors']['title'][] = 'Слишком длинное название объекта, максимальная длинна 250 символов';
        }
        if (mb_strlen($_POST['description']) > 65535) {
            $response['errors']['description'][] = 'Слишком длинное описание объекта, максимальная длинна поля description 65535 символов';
        }
        if (!is_numeric($_POST['parent_id'])) {
            $response['errors']['parent_id'][] = 'Ошибка значения, поле parent_id должно быть целым числом';
        }

        if (isset($_POST['created_at'])) {
            if (!preg_match('/^((19|20)\d\d)-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])$/u', $_POST['created_at'])) {
                $response['errors']['created_at'][] = 'Ошибка значения, поле created_at должно быть формата YYYY.mm.dd';
            }
        } else {
            $_POST['created_at'] = NULL;
        }

        if (!empty($_FILES['image'])) {
            $pathInfo = pathinfo(basename($_FILES['image']['name']));
            if (!in_array($pathInfo['extension'],['jpeg','JPEG','JPG','jpg','gif','GIF','png','PNG'])) {
                $response['errors']['image'][] = 'Изображение имеет неправильный формат';
            }
        } else {
            $pathInfo['extension'] = NULL;
        }

        if ((int)$_POST['parent_id'] !== 0) {
            $dbq = $db->prepare('SELECT * FROM demo_objects WHERE id = :parent_id LIMIT 1');
            $dbq->bindValue(':parent_id', (int)$_POST['parent_id']);
            $dbq->execute();
            $checkObject = $dbq->fetch();
            if (empty($checkObject)) {
                $response['errors']['parent_id'][] = 'Родительского объекта с таким id не существует, укажите в поле значение 0 если требуется добавить корневой элемент';
            }
        }

        if (empty($response['errors'])) {
            $dbq = $db->prepare('INSERT INTO demo_objects SET title = :title, description = :description, parent_id = :parent_id, image = :image, created_at = :created_at');
            $dbq->bindValue(':title', $_POST['title']);
            $dbq->bindValue(':description', $_POST['description']);
            $dbq->bindValue(':parent_id', (int)$_POST['parent_id']);
            $dbq->bindValue(':image', $pathInfo['extension']);
            $dbq->bindValue(':created_at', $_POST['created_at']);
            if ($dbq->execute()) {
                if (!empty((int)$_POST['parent_id'])) {
                    updateChildsStatus((int)$_POST['parent_id'], $db);
                }

                $lastInsertId = $db->lastInsertId();
                if (!empty($_FILES['image'])) {
                    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/objects/';
                    $uploadfile = $uploaddir . $lastInsertId . '.' . $pathInfo['extension'];
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                        $response['errors']['image'][] = 'Ошибка записи файла';
                    }
                }

                $response['success'] = true;
            }
        }

    }

    //3) GET /api/v1/objects/<id>/ - получение данных объекта
    if (!empty($objectID) && $_SERVER['REQUEST_METHOD'] == 'GET') {
        $dbq = $db->prepare('SELECT * FROM demo_objects WHERE id = :id');
        $dbq->bindValue(':id', $objectID);
        $dbq->execute();
        
        $object = $dbq->fetch(PDO::FETCH_ASSOC);
        if (!empty($object)) {
            $response['success'] = true;
            $response['object'] = $object;
        } else {
            $response['errors'][] = 'Объект не найден';
        }

    }

    //4) POST /api/v1/objects/<id>/ - обновление данных объекта
    if (!empty($objectID) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        //validation
        if (!isset($_POST['title'])) {
            $response['errors']['title'][] = 'Ошибка значения, обязательное поле title не определено';
        }
        if (!isset($_POST['description'])) {
            $response['errors']['description'][] = 'Ошибка значения, обязательное поле description не определено';
        }
        if (!isset($_POST['parent_id'])) {
            $response['errors']['parent_id'][] = 'Ошибка значения, обязательное поле parent_id не определено';
        }

        if (!empty($response['errors'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
        
            exit;
        }

        if (mb_strlen($_POST['title']) > 250) {
            $response['errors']['title'][] = 'Слишком длинное название объекта, максимальная длинна 250 символов';
        }
        if (mb_strlen($_POST['description']) > 65535) {
            $response['errors']['description'][] = 'Слишком длинное описание объекта, максимальная длинна 65535 символов';
        }
        if (!is_numeric($_POST['parent_id'])) {
            $response['errors']['parent_id'][] = 'Ошибка значения, поле должно быть целым числом';
        }

        if (isset($_POST['created_at'])) {
            if (!preg_match('/^((19|20)\d\d)-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])$/u', $_POST['created_at'])) {
                $response['errors']['created_at'][] = 'Ошибка значения, поле created_at должно быть формата YYYY.mm.dd';
            }
        } else {
            $_POST['created_at'] = NULL;
        }

        if (!empty($_FILES['image'])) {
            $pathInfo = pathinfo(basename($_FILES['image']['name']));
            if (!in_array($pathInfo['extension'],['jpeg','JPEG','JPG','jpg','gif','GIF','png','PNG'])) {
                $response['errors']['image'][] = 'Изображение имеет неправильный формат';
            }
        }

        if ((int)$_POST['parent_id'] !== 0) {
            $dbq = $db->prepare('SELECT * FROM demo_objects WHERE id = :parent_id');
            $dbq->bindValue(':parent_id', (int)$_POST['parent_id']);
            $dbq->execute();
            $checkObject = $dbq->fetch();
            if (empty($checkObject)) {
                $response['errors']['parent_id'][] = 'Родительского объекта с таким id не существует, укажите в поле значение 0 если требуется добавить корневой элемент';
            }
        }

        //check object exists
        $dbq = $db->prepare('SELECT id FROM demo_objects WHERE id = :id');
        $dbq->bindValue(':id', $objectID);
        $dbq->execute();
        $checkObject = $dbq->fetch();
        if (empty($checkObject)) {
            $response['errors']['id'][] = 'Объект не найден';
        }

        if (empty($response['errors'])) {
            $sqlUpdateStr = '';
            if (!empty($_FILES['image'])) {
                $sqlUpdateStr.= ', image = :image';
            }
            if (isset($_POST['created_at'])) {
                $sqlUpdateStr.= ', created_at = :created_at';
            }
            $sql = 'UPDATE demo_objects SET title = :title, description = :description, parent_id = :parent_id ' . $sqlUpdateStr . ' WHERE id = :id';

            $dbq = $db->prepare($sql);
            $dbq->bindValue(':id', $objectID);
            $dbq->bindValue(':title', $_POST['title']);
            $dbq->bindValue(':description', $_POST['description']);
            $dbq->bindValue(':parent_id', (int)$_POST['parent_id']);
            if (!empty($_FILES['image'])) {
                $dbq->bindValue(':image', $pathInfo['extension']);
            }
            if (isset($_POST['created_at'])) {
                $dbq->bindValue(':created_at', $_POST['created_at']);
            }
            if ($dbq->execute()) {
                if (!empty((int)$_POST['parent_id'])) {
                    updateChildsStatus((int)$_POST['parent_id'], $db);
                }

                if (!empty($_FILES['image'])) {
                    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/objects/';
                    $uploadfile = $uploaddir . $objectID . '.' . $pathInfo['extension'];
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                        $response['errors']['image'][] = 'Ошибка записи файла';
                    }
                }

                $response['success'] = true;
            }
        }

    }

    //5) DELETE /api/v1/objects/<id>/ - удаление объекта
    if (!empty($objectID) && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $dbq = $db->prepare('SELECT * FROM demo_objects WHERE id = :id');
        $dbq->bindValue(':id', $objectID);
        $dbq->execute();
        $object = $dbq->fetch();
        if (!empty($object)) {

            //check object exists
            $dbq = $db->prepare('SELECT id,image FROM demo_objects WHERE id = :id');
            $dbq->bindValue(':id', $objectID);
            $dbq->execute();
            $checkObject = $dbq->fetch(PDO::FETCH_ASSOC);
            if (!empty($checkObject)) {
                @unlink($_SERVER['DOCUMENT_ROOT'] . '/public/images/objects/' . $objectID . '.' . $checkObject['image']);

                $dbq = $db->prepare('DELETE FROM demo_objects WHERE id = :id');
                $dbq->bindValue(':id', $objectID);
                if ($dbq->execute()) {
                    $response['success'] = true;
                }
            } else {
                $response['errors']['id'][] = 'Объект не найден';
            }
        } else {
            $response['errors'][] = 'Объект не найден';
        }
        
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);

    exit;

    