<?php

require_once('././core/TestFactory/Factory.php');

$errors['form']['fields']['login'] = [];
$errors['form']['fields']['password'] = [];
$values['form']['login'] = '';
$values['form']['password'] = '';
$messages['form']['auth'] = [];

if (isset($_POST['login']) && isset($_POST['password'])) {
    $values['form']['login'] = $_POST['login'];
    if (!authorization($_POST['login'], $_POST['password'], $db)) {
        $messages['form']['auth'][] = 'Неправильный логин и пароль';
    }
}

if (empty($errors['system'])) {
    $dbq = $db->prepare('SELECT * FROM objects');
    $dbq->execute();
    $objects = $dbq->fetchAll();
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
                    <h1><?= $appName ?></h1>
                </div>
            </header>
            <div class="main">
                <?php if (!empty($_SESSION['auth']) && empty($errors['system'])) { ?>
                    <div class="container">
                        <form method="POST">
                            <div class="align-center">
                                <a class="btn" href="./admin">Перейти в административную панель</a>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                <?php if (empty($_SESSION['auth']) && empty($errors['system'])) { ?>
                    <div class="container">
                        <form method="POST">
                            <table id="authorization" class="<?= empty($messages['form']['auth']) ? '' : 'error' ?>">
                                <tr>
                                    <td colspan="2" class="auth-form-header">
                                        <div>
                                            <h3>Авторизация</h3>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="auth-form-label">
                                        <label for="login"><b>Пользователь</b></label>
                                    </td>
                                    <td class="auth-form-input">
                                        <input id="login" type="text" placeholder="Введите логин" name="login" value="<?= $values['form']['login'] ?>" required>
                                        <?php foreach ($errors['form']['fields']['login'] as $message) { ?>
                                            <div class="message error"><?php echo $message; ?></div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="auth-form-label">
                                        <label for="password"><b>Пароль</b></label>
                                    </td>
                                    <td class="auth-form-input">
                                        <input id="password" type="password" placeholder="Введите пароль" name="password" value="" required>
                                        <?php foreach ($errors['form']['fields']['password'] as $message) { ?>
                                            <div class="message error"><?php echo $message; ?></div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="auth-form-submit-area" colspan="2">
                                        <?php foreach ($messages['form']['auth'] as $message) { ?>
                                            <div class="message error"><?php echo $message; ?></div>
                                        <?php } ?>
                                        <br>
                                        <button name="authorize" value="1" type="submit">Авторизоваться</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                <?php } ?>
                <div class="container">
                    <div class="align-center">
                        <?php if (!empty($errors['configuration'])) { ?>
                            <?php foreach ($errors['configuration'] as $error) { ?>
                                <div class="message error"><?php echo $error; ?></div>
                            <?php } ?>
                        <?php } ?>
                        <?php if (empty($errors['system'])) { ?>
                            <?php if (!empty($_SESSION['auth'])) { ?>
                                <h3>Пользователь авторизован</h3>
                            <?php } else { ?>
                                <p>Используйте следующие реквизиты для авторизации:</p>
                                <p>Пользователь: admin</p>
                                <p>Пароль: 12345678</p>
                            <?php } ?>
                            <?php if (!empty($_SESSION['auth'])) { ?>
                                <form method="POST">
                                    <button class="btn" title="Разлогиниться" name="unauthorize" type="submit" value="1">Выйти</button>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div id="projectDescription" class="container">
                    <h2>Задача проекта:</h2>
                    &nbsp;&nbsp;&nbsp;&nbsp;Привести пример кода с использованием PHP8, MYSQL, JS, HTML, CSS
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;Реализовать демо приложение не являющееся коммерческим <a href="https://ru.wikipedia.org/wiki/%D0%9C%D0%B8%D0%BD%D0%B8%D0%BC%D0%B0%D0%BB%D1%8C%D0%BD%D0%BE_%D0%B6%D0%B8%D0%B7%D0%BD%D0%B5%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%D0%BD%D1%8B%D0%B9_%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82">MVP</a>
                    <br>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<span class="message">✔</span>
                    <br>
                    <br>
                    <h3>Страницы приложения:</h3>
                    <h4>&nbsp;&nbsp;&nbsp;&nbsp;Для неавторизованных пользователей:</h4>
                    &nbsp;&nbsp;&nbsp;&nbsp;1) Главная страница включающая:
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - форму авторизации пользователей <a href="#authorizationForm">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - описание проекта <a href="#projectDescription">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - представление структуры данных в древовидном исполнении: <a href="#dataObjectsExample">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - представление реализации кода PHP на примере реализации паттерна ООП: <a href="#codeExample">перейти</a>
                    <h4>&nbsp;&nbsp;&nbsp;&nbsp;Для авторизованных пользователей:</h4>
                    &nbsp;&nbsp;&nbsp;&nbsp;1) Страница списка объектов с возможностью удаления, редактирования и добавления объектов в структуру: <a href="./admin">открыть</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;2) Страница редактирования объектов древовидной структуры: <a href="./edit?id=0">открыть</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;3) Страница добавления объектов древовидной структуры: <a href="./add">открыть</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;4) Страница удаления объектов древовидной структуры: <a href="./delete?id=0">открыть</a>
                    <br>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<span class="message">✔</span>
                    <br>
                    <br>
                    <h3>Дополнительная информация о приложении:</h3>
                    &nbsp;&nbsp;&nbsp;&nbsp;- приложение осуществляет формирование и вывод данных при помощи языка PHP8
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- представлен код в функциональном и ООП виде, присутствуют рекурсивные функции и др.
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- верстка страниц проверена по стандартам HTML5 (и в нескольких браузерах)
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- Применен JavaScript (JS) без использования каких либо фреймворков
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- В БД MYSQL хранится таблица пользователей c зашифрованнами паролями и структура данных (всего 2 таблицы)
                    <h4>&nbsp;&nbsp;&nbsp;&nbsp;Для запуска приложения необходимо:</h4>
                    &nbsp;&nbsp;&nbsp;&nbsp;- загрузить на локальный сервер файлы приложения
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- загрузить в базу данных приложенный дамп
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- отредактировать конфигурационный файл приложения /core/config.php
                    <br>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn" href="./db.zip">Скачать дамп БД MYSQL</a>
                    <br>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<span class="message">✔</span>
                    <br>
                    <br>
                </div>
                <div class="container canvas-container">
                    <canvas id="canvas1"></canvas>
                </div>
                <?php if (empty($errors['system'])) { ?>
                    <div id="dataObjectsExample" class="container">
                        <h2>СТРУКТУРА ДАННЫХ:</h2>
                        <p>Дерево объектов, чтобы увидеть описание объекта необходимо кликнуть на название.</p>
                        <div id="objects">
                            <div class="row">
                                <div class="objects-list">
                                    <div>
                                        <?php echo renderTreeRecursive(makeTreeArrayRecursive($objects)) ?>
                                    </div>
                                </div>
                                <div id="objectDescription" class="object-description">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container canvas-container">
                        <canvas id="canvas2"></canvas>
                    </div>
                    <div id="codeExample" class="container factory-check">
                        <h2>Пример реализации ООП:</h2>
                        <p>Примером служит реализация нескольких классов для описания телефонов как объектов манипулирования средствами языка программирования PHP реализуя паттерн фабричный метод.</p>
                        <p>[интерфейсы]: PhoneFactoryInterface, PhoneInterface</p>
                        <p>[классы]: PhoneFactory, SmartphoneFactory, Phone, Smartphone</p>
                        <h3>Код PHP:</h3>
                        <div class="code">
                            <i>
                                $phoneFactory = new core\TestFactory\PhoneFactory();<br>
                                $phone = $phoneFactory->create([<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Телефон "Аппарат"',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'description' => 'Типичный телефон',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'functions' => [<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'Звонки',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'СМС',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'4G'<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;]<br>
                                ]);<br>
                                echo '&lt;span&gt;Название телефона&lt;/span&gt;: ' . $phone->getTitle() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Описание телефона&lt;/span&gt;: ' . $phone->getDescription() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Функции телефона&lt;/span&gt;:&lt;br&gt;';<br>
                                foreach ($phone->getFunctions() as $phoneFunction) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&#38;nbsp;&#38;nbsp;&#38;nbsp;&#38;nbsp;- ' . $phoneFunction . '&lt;br&gt;';<br>
                                }<br>
                                echo '&lt;br&gt;';<br>
                                $smartphoneFactory = new core\TestFactory\SmartphoneFactory();<br>
                                $smartphone = $smartphoneFactory->create([<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Смартфон "Смарт Аппарат"',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'description' => 'Типичный смартфон',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'functions' => [<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'Звонки',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'СМС',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'4G',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'GPS',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'Магазин приложений',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'Майнить криптовалюту (без СМС и регистрации)'<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;]<br>
                                ]);<br>
                                echo '&lt;span&gt;Название телефона&lt;/span&gt;: ' . $smartphone->getTitle() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Описание телефона&lt;/span&gt;: ' . $smartphone->getDescription() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Функции телефона&lt;/span&gt;:&lt;br&gt;';<br>
                                foreach ($smartphone->getFunctions() as $phoneFunction) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&#38;nbsp;&#38;nbsp;&#38;nbsp;&#38;nbsp;- ' . $phoneFunction . '&lt;br&gt;';<br>
                                }<br>
                            </i>
                        </div>
                        <h3>Результат выполнения кода PHP:</h3>
                        <?php
                        $phoneFactory = new core\TestFactory\PhoneFactory();
                        $phone = $phoneFactory->create([
                            'title' => 'Телефон "Аппарат"',
                            'description' => 'Типичный телефон',
                            'functions' => [
                                'Звонки',
                                'СМС',
                                '4G'
                            ]
                        ]);
                        echo '<span>Название телефона</span>: ' . $phone->getTitle() . '<br>';
                        echo '<span>Описание телефона</span>: ' . $phone->getDescription() . '<br>';
                        echo '<span>Функции телефона</span>:<br>';
                        foreach ($phone->getFunctions() as $phoneFunction) {
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;- ' . $phoneFunction . '<br>';
                        }
                        echo '<br>';
                        $smartphoneFactory = new core\TestFactory\SmartphoneFactory();
                        $smartphone = $smartphoneFactory->create([
                            'title' => 'Смартфон "Смарт Аппарат"',
                            'description' => 'Типичный смартфон',
                            'functions' => [
                                'Звонки',
                                'СМС',
                                '4G',
                                'GPS',
                                'Магазин приложений',
                                'Майнить криптовалюту (без СМС и регистрации)'
                            ]
                        ]);
                        echo '<span>Название телефона</span>: ' . $smartphone->getTitle() . '<br>';
                        echo '<span>Описание телефона</span>: ' . $smartphone->getDescription() . '<br>';
                        echo '<span>Функции телефона</span>:<br>';
                        foreach ($smartphone->getFunctions() as $phoneFunction) {
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;- ' . $phoneFunction . '<br>';
                        }
                        ?>
                    </div>
                <?php } ?>
                    <div class="container canvas-container">
                        <canvas id="canvas3"></canvas>
                    </div>
            </div>
            <footer>
                <div>
                    <img alt="demo" src="/public/demo-guy.png" />
                    <br>
                    <span>© Kartoshkin "DEMO"</span>
                    <p>P.S.: версия демо проекта не окончательная (особенно что касается дизайна и верстки) но достаточная для того чтобы ответить на вопрос про технологии описываемые в требованиях работодателя на различные вакансии по направлению веб разработки. Проект специально открыт для того чтобы было видно какие технологии использованы и делается в свободное время и не является попыткой перегнать доказательную базу обучающих учреждений либо конкретных личностей показывающих "результат за час". Есть и более основательные проекты которые отсутствуют в свободном доступе в которых в наличии более сложные SQL запросы, более сложная верстка, работа с веб интерфейсом и др. Проект служит попыткой избавиться от предложенных тестовых заданий работодателя присутствующего в ложном ключе на рынке вакансий (что зря тратит моё время), а так же чтобы не открывать основные проекты.</p>
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