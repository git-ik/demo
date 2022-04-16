<?php

require_once('./core/core.php');

if (empty($_SESSION['auth'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$isSaved = false;
$errorFields = [
    'title' => [],
    'description' => [],
    'parent_id' => []
];
$object = [];

if (!empty($_POST['save'])) {

    //validation
    if (!is_string($_POST['title']) || mb_strlen($_POST['title']) > 250) {
        $errorFields['title'][] = 'Слишком длинное название объекта';
    }
    if (!is_string($_POST['description'])) {
        $errorFields['description'][] = 'Ошибка значения';
    }
    if (!is_numeric($_POST['parent_id'])) {
        $errorFields['parent_id'][] = 'Ошибка значения';
    }

    if ((int)$_POST['parent_id'] !== 0) {
        $sth = $db->prepare('SELECT * FROM objects WHERE id = :parent_id LIMIT 1');
        $sth->bindValue(':parent_id', (int)$_POST['parent_id']);
        $sth->execute();
        $checkObject = $sth->fetch();
        if (empty($checkObject)) {
            $errorFields['parent_id'][] = 'Родительского объекта с таким id не существует, укажите в поле значение 0 если требуется добавить корневой элемент';
        }
    }

    if (empty($errorFields['title']) && empty($errorFields['description']) && empty($errorFields['parent_id'])) {
        $sth = $db->prepare('INSERT INTO objects SET title = :title, description = :description, parent_id = :parent_id');
        $sth->bindValue(':title', $_POST['title']);
        $sth->bindValue(':description', $_POST['description']);
        $sth->bindValue(':parent_id', (int)$_POST['parent_id']);
        if ($sth->execute()) {
            if (!empty((int)$_POST['parent_id'])) {
                updateChildsStatus((int)$_POST['parent_id'], $db);
            }
            $isSaved = true;
        }
    }

    $object['title'] = $_POST['title'];
    $object['description'] = $_POST['description'];
    $object['parent_id'] = $_POST['parent_id'];
}

$sth = $db->prepare('SELECT * FROM objects');
$sth->execute();
$objectsList = $sth->fetchAll();

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?=$appTitle?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="/public/main.css">
        <script src="/public/main.js"></script>
    </head>
    <body>
        <header class="align-center">
            <?php if (!empty($_SESSION['auth'])) { ?>
                <form method="POST">
                    <button class="logout" title="Разлогиниться" id="unauthorize" name="unauthorize" type="submit" value="1"><img alt="logout" src="./public/logout.png"></button>
                </form>
            <?php } ?>
            <h1><?=$appName?></h1>
        </header>
        <div class="main">
            <div class="container">
                <a class="btn" href="./admin.php">&lt;&lt; назад</a>
            </div>
            <br>
            <div class="container">
                <form method="POST">
                    <table class="edit-table">
                        <tr>
                            <th colspan="2">Добавление объекта</th>
                        </tr>
                        <tr>
                            <td colspan="2"><br></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="title"><b>Название объекта</b></label></td>
                            <td>
                                <input id="title" class="<?= empty($errorFields['title']) ? '' : 'error' ?>" type="text" placeholder="" name="title" required value="<?= @$object['title'] ?>">
                                <?php foreach ($errorFields['title'] as $message) { ?>
                                    <div class="message error"><?php echo $message; ?></div>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="description"><b>Описание объекта</b></label></td>
                            <td>
                                <textarea id="description" name="description" class="<?= empty($errorFields['description']) ? '' : 'error' ?>" required><?= @$object['description'] ?></textarea>
                                <?php foreach ($errorFields['description'] as $message) { ?>
                                    <div class="message error"><?php echo $message; ?></div>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="parentId"><b>id родительского объекта</b></label></td>
                            <td>
                                <select id="parentId" name="parent_id" class="<?= empty($errorFields['parent_id']) ? '' : 'error' ?>">
                                    <option value="0">Нет</option>
                                    <?php foreach ($objectsList as $objectItem) { ?>
                                        <option value="<?=$objectItem['id']?>"><?=$objectItem['title']?></option>
                                    <?php } ?>
                                </select>
                                <?php foreach ($errorFields['parent_id'] as $message) { ?>
                                    <div class="message error"><?php echo $message; ?></div>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <button class="save" name="save" type="submit" value="1">Сохранить</button>
                    <br>
                    <?php if ($isSaved) { ?>
                        <div class="message">Сохранено</div>
                    <?php } ?>
                </form>
            </div>
        </div>
        <footer>
            <?php foreach ($errors as $error) { ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php } ?>
            <?php foreach ($messages as $message) { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>
        </footer>
    </body>
</html>