<?php

if (!checkAuthorization()) {
    error403();
}

if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    header("HTTP/1.0 404 Not Found");
    die;
}

$dbq = $db->prepare('SELECT * FROM demo_objects WHERE id = :id');
$dbq->bindValue(':id', $id);
$dbq->execute();
$object = $dbq->fetch();

if (empty($object)) {
    header("HTTP/1.0 404 Not Found");
    die;
}

$errors['form']['fields']['title'] = [];
$errors['form']['fields']['description'] = [];
$errors['form']['fields']['parent_id'] = [];
$values['form']['title'] = $object['title'];
$values['form']['description'] = $object['description'];
$values['form']['parent_id'] = $object['parent_id'];
$values['form']['success'] = false;

if (!empty($_POST['save'])) {

    $values['form']['title'] = $_POST['title'];
    $values['form']['description'] = $_POST['description'];
    $values['form']['parent_id'] = $_POST['parent_id'];

    //validation
    if (!is_string($_POST['title']) || mb_strlen($_POST['title']) > 250) {
        $errors['form']['fields']['title'][] = 'Слишком длинное название объекта';
    }
    if (!is_string($_POST['description'])) {
        $errors['form']['fields']['description'][] = 'Ошибка значения';
    }
    if (!is_numeric($_POST['parent_id'])) {
        $errors['form']['fields']['parent_id'][] = 'Ошибка значения';
    }
    if ($id == (int)$_POST['parent_id']) {
        $errors['form']['fields']['parent_id'][] = 'Неверно выбран родительский объект';
    }
    if (in_array((int)$_POST['parent_id'], getChildsRecursive($id, $db))) {
        $errors['form']['fields']['parent_id'][] = 'Выбранный объект находится ниже в структуре дерева объектов';
    }

    if (empty($errors['form']['fields']['title']) && empty($errors['form']['fields']['description']) && empty($errors['form']['fields']['parent_id'])) {
        $dbq = $db->prepare('UPDATE demo_objects SET title = :title, description = :description, parent_id = :parent_id WHERE id = :id');
        $dbq->bindValue(':id', $id);
        $dbq->bindValue(':title', $_POST['title']);
        $dbq->bindValue(':description', $_POST['description']);
        $dbq->bindValue(':parent_id', (int)$_POST['parent_id']);
        if ($dbq->execute()) {
            if (!empty($object['parent_id'])) {
                updateChildsStatus($object['parent_id'], $db);
            }
            if (!empty((int)$_POST['parent_id'])) {
                updateChildsStatus((int)$_POST['parent_id'], $db);
            }
            $values['form']['success'] = true;
        }
    }
}

$dbq = $db->prepare('SELECT * FROM demo_objects');
$dbq->execute();
$objectsList = $dbq->fetchAll();

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?= $appTitle ?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="/public/main.css">
        <script src="/public/main.js"></script>
    </head>
    <body>
        <div>
            <header>
                <div class="header-box">
                    <?php if (!empty($_SESSION['auth'])) { ?>
                        <div class="logout">
                            <form method="POST">
                                <button title="Разлогиниться" id="unauthorize" name="unauthorize" type="submit" value="1">
                                    <img alt="logout" src="./public/images/logout.png">
                                </button>
                                <span></span>
                            </form>
                        </div>
                    <?php } ?>
                    <div class="app-title">
                        <h1 id="h1"><?= $appName ?></h1>
                    </div>
                </div>
            </header>
            <div id="technologies">
                <span id="t1">PHP8</span>
                <span id="t2">JS</span>
                <span id="t3">MySQL</span>
                <span id="t4">GIT</span>
                <span id="t5">GIMP</span>
            </div>
            <div class="main">
                <div class="container">
                    <a class="btn" href="./admin">&lt;&lt; Назад</a>
                </div>
                <div class="container">
                    <form method="POST">
                        <table class="edit-table">
                            <tr>
                                <th colspan="2">Редактирование объекта</th>
                            </tr>
                            <tr>
                                <td colspan="2"><br></td>
                            </tr>
                            <tr>
                                <td><label for="title"><b>Название объекта</b></label></td>
                                <td>
                                    <input id="title" class="<?= empty($errors['form']['fields']['title']) ? '' : 'error' ?>" type="text" placeholder="" name="title" required value="<?= $values['form']['title'] ?>">
                                    <?php foreach ($errors['form']['fields']['title'] as $message) { ?>
                                        <div class="message error"><?php echo $message; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="description"><b>Описание объекта</b></label></td>
                                <td>
                                    <textarea id="description" name="description" class="<?= empty($errors['form']['fields']['description']) ? '' : 'error' ?>" required><?= $values['form']['description'] ?></textarea>
                                    <?php foreach ($errors['form']['fields']['description'] as $message) { ?>
                                        <div class="message error"><?php echo $message; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="parentId"><b>id родительского объекта</b></label></td>
                                <td>
                                    <select id="parentId" name="parent_id" class="<?= empty($errors['form']['fields']['parent_id']) ? '' : 'error' ?>">
                                        <option value="0">Нет</option>
                                        <?php foreach ($objectsList as $objectItem) { ?>
                                            <option <?= ($values['form']['parent_id'] == $objectItem['id']) ? 'selected' : '' ?> value="<?= $objectItem['id'] ?>"><?= $objectItem['title'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php foreach ($errors['form']['fields']['parent_id'] as $message) { ?>
                                        <div class="message error"><?php echo $message; ?></div>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <button title="Сохранить" class="save" name="save" type="submit" value="1">Сохранить</button>
                        <br>
                        <?php if ($values['form']['success']) { ?>
                            <div class="message">Сохранено</div>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <footer>
                <div>
                    <div class="footer-icon">
                        <div class="squares">
                            <div id="square1"></div>
                            <div id="square2"></div>
                            <div id="square3"></div>
                        </div>
                        <img class="demo-guy" alt="demo-guy" src="/public/images/demo-guy.png" />
                        <br>
                        <img id="demoGuyWater" class="demo-guy-water" alt="demo-guy-water" src="/public/images/demo-guy-water.png" />
                    </div>
                    <span>© Kartoshkin "DEMO"</span>
                    <br>
                    <a href="mailto:iksoc@vk.com">iksoc@vk.com</a>
                </div>
                <?php foreach ($errors['system'] as $error) { ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php } ?>
                <?php foreach ($messages['sysinfo'] as $message) { ?>
                    <div class="message"><?php echo $message; ?></div>
                <?php } ?>
            </footer>
        </div>
    </body>
</html>