<?php

require_once('./core/core.php');

if (empty($_SESSION['auth'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

if (isset($_REQUEST['parent_id'])) {
    $parentId = (int)$_REQUEST['parent_id'];
} else {
    $parentId = 0;
}

$sth = $db->prepare('SELECT * FROM objects');
$sth->execute();
$objects = $sth->fetchAll();

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
                <a title="Вернуться на главную страницу" class="btn" href="./">&lt;&lt; на главную</a>
                <br>
            </div>
            <br>
            <div class="container">
                <h2>Структура данных:</h2>
                <a title="Добавить объект в дерево" class="btn btn-green" href="./add.php">Добавить</a>
                <br>
                <br>
                <table class="list-table">
                    <tr>
                        <th>id</th>
                        <th>Название</th>
                        <th>Родительский объект</th>
                        <th></th>
                    </tr>
                    <?php foreach ($objects as $item) { ?>
                        <tr>
                            <td>
                                <?=$item['id']?>
                            </td>
                            <td>
                                <a href="./edit.php?id=<?=$item['id']?>"><?=$item['title']?></a>
                            </td>
                            <td>
                                <? if (empty($item['parent_id'])) { ?>
                                    -
                                <? } else { ?>
                                    ID <?=$item['parent_id']?>
                                <? } ?>
                            </td>
                            <td>
                                <a title="Удалить объект" href="./delete.php?id=<?=$item['id']?>" class="btn">удалить</a>
                                <a class="btn" href="./edit.php?id=<?=$item['id']?>">редактировать</a>
                            </td>
                        </tr>
                    <? } ?>
                    <tr>
                        <td><br></td>
                        <td><br></td>
                        <td><br></td>
                    </tr>
                </table>
            </div>
            <br>
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