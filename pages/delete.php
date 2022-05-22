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

$dbq = $db->prepare('SELECT * FROM objects WHERE id = :id');
$dbq->bindValue(':id', $id);
$dbq->execute();
$object = $dbq->fetch();

if (empty($object)) {
    header("HTTP/1.0 404 Not Found");
    die;
}

if (deleteRecursive($id, $db)) {
    $messages['success'][] = 'Обект удален';
} else {
    $messages['fails'][] = 'Объект не найден';
}

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
                <div class="box">
                    <?php if (!empty($_SESSION['auth'])) { ?>
                        <form method="POST">
                            <button class="logout" title="Разлогиниться" id="unauthorize" name="unauthorize" type="submit" value="1"><img alt="logout" src="./public/logout.png"></button>
                        </form>
                    <?php } ?>
                    <div>
                        <h1><?= $appName ?></h1>
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
                    <a title="Назад" class="btn" href="./admin">&lt;&lt; Назад</a>
                </div>
                <div class="container align-center">
                    <h2>Удаление объекта [#<?= $_REQUEST['id'] ?>]</h2>
                    <?php foreach ($messages['success'] as $message) { ?>
                        <span class="message"><?php echo $message; ?></span>
                    <?php } ?>
                    <?php foreach ($messages['fails'] as $error) { ?>
                        <span class="message error"><?php echo $error; ?></span>
                    <?php } ?>
                </div>
            </div>
            <footer>
                <div>
                    <img alt="demo" src="/public/demo-guy.png" />
                    <br>
                    <span>© Kartoshkin "DEMO"</span>
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