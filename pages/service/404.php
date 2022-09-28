<?php
header("HTTP/1.0 404 Not Found", true, 404);
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?= $appTitle ?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
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
                        <h1><?= $appName ?></h1>
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
                    <a title="На главную страницу" class="btn" href="/">&lt;&lt; на главную</a>
                </div>
                <div class="container">
                    <p>Страница не найдена</p>
                </div>
            </div>
            <footer>
                <div>
                    <img alt="demo" src="/public/images/demo-guy.png">
                    <br>
                    <span>© Kartoshkin "DEMO"</span>
                </div>
                <?php foreach ($errors['system'] as $error) { ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php } ?>
                <?php foreach ($messages['sysinfo'] as $message) { ?>
                    <div class="message"><?php echo $message; ?></div>
                <?php } ?>
                <img class="q-triangle" alt="quadrat-triangle" src="/public/images/q-triangle.png">
            </footer>
        </div>
    </body>
</html>