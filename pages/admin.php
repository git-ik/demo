<?php

if (!checkAuthorization()) {
    error403();
}

$dbq = $db->prepare('SELECT * FROM demo_objects');
$dbq->execute();
$objects = $dbq->fetchAll();

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
        <div class="overlay">
            <video autoplay="" muted="" loop="">
                <source src="/public/video/demo.mp4" type="video/mp4">
            </video>
        </div>
        <div class="c-container">
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
            <div class="text-animated-box">
                <span id="t1">the [system]</span>
                <span id="t2">power</span>
                <span id="t3">is off</span>
                <span id="t4">now</span>
                <span id="t5">--------</span>
            </div>
            <div class="main">
                <div class="container">
                    <a title="На главную страницу" class="btn" href="./">&lt;&lt; На главную</a>
                    <br>
                </div>
                <br>
                <div class="container">
                    <h2>Структура данных:</h2>
                    <br>
                    <a title="Добавить объект в дерево" class="btn btn-green btn-border" href="./add">Добавить</a>
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
                                    <a href="./edit?id=<?=$item['id']?>"><?=$item['title']?></a>
                                </td>
                                <td>
                                    <?php if (empty($item['parent_id'])) { ?>
                                        -
                                    <?php } else { ?>
                                        ID <?=$item['parent_id']?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a title="Удалить объект" href="./delete?id=<?=$item['id']?>" class="btn">удалить</a>
                                    <a class="btn" href="./edit?id=<?=$item['id']?>">редактировать</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="4"><br></td>
                        </tr>
                    </table>
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