<?php
header('HTTP/1.0 403 Forbidden', true, 403);
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
                    <h1><?= $appName ?></h1>
                </div>
            </header>
            <div class="main">
                <div class="container">
                    <a title="На главную страницу" class="btn" href="/">&lt;&lt; на главную</a>
                </div>
                <div class="container">
                    <p>Отсутствует доступ к странице</p>
                </div>
            </div>
            <footer>
                <div>
                    <img alt="demo" src="/public/demo-guy.png" />
                    <br>
                    <span>© X1nGG "DEMO"</span>
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