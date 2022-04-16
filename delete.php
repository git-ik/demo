<?php

require_once('./core/core.php');

if (empty($_SESSION['auth'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    header("HTTP/1.0 404 Not Found");
    die;
}

$isDeleted = false;
$deleteResults = deleteRecursive($id, $db);
if ($deleteResults['success']) {
    $isDeleted = true;
} else {
    $errors[] = 'Объект не найден';
}

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
            <div class="container align-center">
                <h2>Удаление объекта [#<?=$_REQUEST['id']?>]</h2>
                <?php if ($isDeleted) { ?>
                    <span class="message">Объект удален</span>
                <?php } ?>
                <?php foreach ($errors as $error) { ?>
                    <span class="message error"><?php echo $error; ?></span>
                <?php } ?>
                <?php foreach ($messages as $message) { ?>
                    <span class="message"><?php echo $message; ?></span>
                <?php } ?>
            </div>
        </div>
        <footer></footer>
    </body>
</html>