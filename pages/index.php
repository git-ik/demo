<?php

require_once('././core/Factory/Factory.php');

use core\Factory\Test;
use core\Factory\PhoneFactory;
use core\Factory\SmartphoneFactory;

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
            <div id="technologies">
                <span id="t1">PHP8</span>
                <span id="t2">JS</span>
                <span id="t3">MySQL</span>
                <span id="t4">GIT</span>
                <span id="t5">GIMP</span>
            </div>
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
                    <div id="authorization" class="container">
                        <form method="POST">
                            <table class="<?= empty($messages['form']['auth']) ? '' : 'error' ?>">
                                <tr>
                                    <td colspan="2" class="auth-form-header">
                                        <div class="light"></div>
                                        <div class="form-title">
                                            <span>Авторизация</span>
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
                                <br>
                                <br>
                            <?php } else { ?>
                                <p>Используйте следующие реквизиты для авторизации:</p>
                                <p>Пользователь: <?=$serviceUsers['admin']['login']?></p>
                                <p>Пароль: <?=$serviceUsers['admin']['password']?></p>
                            <?php } ?>
                            <?php if (!empty($_SESSION['auth'])) { ?>
                                <form method="POST">
                                    <button title="Разлогиниться" name="unauthorize" type="submit" value="1">Выйти</button>
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div id="projectDescription" class="container">
                    <h2>Задача проекта:</h2>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;Привести пример кода с использованием PHP8, MYSQL, JS, HTML, CSS
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;Реализовать демо приложение не являющееся коммерческим <a href="https://ru.wikipedia.org/wiki/%D0%9C%D0%B8%D0%BD%D0%B8%D0%BC%D0%B0%D0%BB%D1%8C%D0%BD%D0%BE_%D0%B6%D0%B8%D0%B7%D0%BD%D0%B5%D1%81%D0%BF%D0%BE%D1%81%D0%BE%D0%B1%D0%BD%D1%8B%D0%B9_%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82">MVP</a>
                    <br>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<span class="success-icon">✔</span>
                    <br>
                    <br>
                    <h3>Страницы проекта:</h3>
                    <h4>&nbsp;&nbsp;&nbsp;&nbsp;Для неавторизованных пользователей:</h4>
                    &nbsp;&nbsp;&nbsp;&nbsp;1) Главная страница:
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - форма авторизации пользователей <a href="#authorizationForm">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - описание демо проекта <a href="#projectDescription">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - представление структуры данных в древовидном исполнении с разграничением прав доступа: <a href="#dataObjectsExample">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - представление реализации кода PHP на примере реализации паттерна ООП: <a href="#codeExample">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;2) Страница ошибки 403 <a href="/403">перейти</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;3) Страница ошибки 404 <a href="/404">перейти</a>
                    <h4>&nbsp;&nbsp;&nbsp;&nbsp;Для авторизованных пользователей:</h4>
                    &nbsp;&nbsp;&nbsp;&nbsp;1) Панель редактирования списка объектов: <a href="./admin">открыть</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Страница редактирования объекта древовидной структуры: <a href="./edit?id=0">открыть</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Страница добавления объекта древовидной структуры: <a href="./add">открыть</a>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Страница удаления объекта древовидной структуры: <a href="./delete?id=0">открыть</a>
                    <br>
                    <br>
                    <h3>Дополнительная информация:</h3>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- приложение осуществляет формирование и вывод данных при помощи PHP8
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- представлен код в функциональном и ООП виде, присутствуют рекурсивные функции и др.
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- верстка страниц проверена по стандартам HTML5 (и в нескольких браузерах)
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- Применен JavaScript (JS) без использования каких либо фреймворков
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- В БД MYSQL хранится таблица пользователей и структура данных (2 таблицы)
                    <h4>&nbsp;&nbsp;&nbsp;&nbsp;Для запуска на локальной тестовой машине необходимо:</h4>
                    &nbsp;&nbsp;&nbsp;&nbsp;- загрузить в папку проектов локального web сервера (apache) файлы приложения
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- проверить настройки apache (должен быть включен mod rewrite и необходимо внести в конфиг апачи директиву AllowOverride All для проекта)
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- загрузить в базу данных приложенный дамп
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- отредактировать конфигурационный файл приложения /core/config.php
                    <br>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn" href="./db.zip">Скачать дамп БД MYSQL</a>
                    <br>
                    <br>
                </div>
                <div class="container canvas-container">
                    <canvas id="canvas1"></canvas>
                </div>
                <?php if (empty($errors['system'])) { ?>
                    <div id="dataObjectsExample" class="container">
                        <h3>СТРУКТУРА ДАННЫХ:</h3>
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
                        <h3>Пример реализации ООП:</h3>
                        <p>Примером реализации ООП в PHP является представление нескольких классов для описания телефонов как объектов манипулирования средствами языка программирования реализуя паттерн "<a target="_blank" href="https://designpatternsphp.readthedocs.io/ru/latest/Creational/FactoryMethod/README.html">фабричный метод</a>".</p>
                        <p>[интерфейсы]: PhoneFactoryInterface, PhoneInterface</p>
                        <p>[классы]: PhoneFactory, SmartphoneFactory, Phone, Smartphone</p>
                        <h4>Код PHP:</h4>
                        <div class="code">
                            <i>
                                $phoneFactory = new PhoneFactory();<br>
                                $phone = $phoneFactory->create([<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Аппарат',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'description' => 'Телефон с антибликовым покрытием экрана, соответствующий стандарту защиты IP127001',<br>
                                ]);<br>
                                echo '&lt;span&gt;Название телефона&lt;/span&gt;: ' . $phone->getTitle() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Описание телефона&lt;/span&gt;: ' . $phone->getDescription() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Функции телефона&lt;/span&gt;:&lt;br&gt;';<br>
                                foreach ($phone->getFunctions() as $phoneFunction) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&#38;nbsp;&#38;nbsp;&#38;nbsp;&#38;nbsp;- ' . $phoneFunction . '&lt;br&gt;';<br>
                                }<br>
                                if (Test::testPhone($phone)) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&lt;span class="tested">✔ протестировано: &lt;i&gt;- Класс телефона "Аппарат" создан фабрикой&lt;/i&gt;&lt;/span&gt;';<br>
                                }<br>
                                echo '&lt;br&gt;&lt;br&gt;';<br>
                                $smartphoneFactory = new SmartphoneFactory();<br>
                                $smartphone = $smartphoneFactory->create([<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Смарт Аппарат',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'description' => 'Смартфон с защитным экраном GCore2Glass',<br>
                                ]);<br>
                                echo '&lt;span&gt;Название телефона&lt;/span&gt;: ' . $smartphone->getTitle() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Описание телефона&lt;/span&gt;: ' . $smartphone->getDescription() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Функции телефона&lt;/span&gt;:&lt;br&gt;';<br>
                                foreach ($smartphone->getFunctions() as $phoneFunction) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&#38;nbsp;&#38;nbsp;&#38;nbsp;&#38;nbsp;- ' . $phoneFunction . '&lt;br&gt;';<br>
                                }<br>
                                if (Test::testPhone($smartphone)) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&lt;span class="tested">✔ протестировано: &lt;i&gt;- Класс телефона "Смарт Аппарат" создан фабрикой&lt;/i&gt;&lt;/span&gt;';<br>
                                }<br>
                            </i>
                        </div>
                        <h4>Результат выполнения кода PHP:</h4>
                        <?php
                            $phoneFactory = new PhoneFactory();
                            $phone = $phoneFactory->create([
                                'title' => 'Аппарат',
                                'description' => 'Телефон с антибликовым покрытием экрана, соответствующий стандарту защиты IP127001',
                            ]);
                            echo '<span>Название телефона</span>: ' . $phone->getTitle() . '<br>';
                            echo '<span>Описание телефона</span>: ' . $phone->getDescription() . '<br>';
                            echo '<span>Функции телефона</span>:<br>';
                            foreach ($phone->getFunctions() as $phoneFunction) {
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;- ' . $phoneFunction . '<br>';
                            }
                            if (Test::testPhone($phone)) {
                                echo '<span class="tested">✔ протестировано: <i>- Класс телефона "Аппарат" создан фабрикой</i></span>';
                            }
                            echo '<br><br>';
                            $smartphoneFactory = new SmartphoneFactory();
                            $smartphone = $smartphoneFactory->create([
                                'title' => 'Смарт Аппарат',
                                'description' => 'Смартфон с защитным экраном GCore2Glass',
                            ]);
                            echo '<span>Название телефона</span>: ' . $smartphone->getTitle() . '<br>';
                            echo '<span>Описание телефона</span>: ' . $smartphone->getDescription() . '<br>';
                            echo '<span>Функции телефона</span>:<br>';
                            foreach ($smartphone->getFunctions() as $phoneFunction) {
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;- ' . $phoneFunction . '<br>';
                            }
                            if (Test::testPhone($smartphone)) {
                                echo '<span class="tested">✔ протестировано: <i>- Класс телефона "Смарт Аппарат" создан фабрикой</i></span>';
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
                    <p>Версия демо проекта не окончательная (особенно что касается дизайна и верстки) но достаточная для того чтобы представить пример использования технологий описываемых в требованиях работодателя на различные вакансии по направлению веб разработки. Проект открыт для того чтобы было видно какие технологии использованы, делается в свободное время и не является попыткой перегнать доказательную базу обучающих учреждений либо частных лиц. Есть и более основательные проекты которые отсутствуют в свободном доступе в которых в наличии более сложные SQL запросы, более сложная верстка, работа с веб интерфейсом и др. Целью проекта является попытка избавиться от предложенных тестовых заданий работодателя присутствующего в ложном ключе на рынке вакансий (что зря тратит моё время), так же демо проект нужен для того чтобы не открывать основные проекты в случае необходимости представить пример кода, верстки и др.</p>
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