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

$messagesCounter = 0;

if (isset($_POST['login']) && isset($_POST['password'])) {
    $values['form']['login'] = $_POST['login'];
    if (!authorization($_POST['login'], $_POST['password'], $db)) {
        $messages['form']['auth'][] = 'Access denied';
    }
}

if (empty($errors['system'])) {
    $objects = getObjects($db);
    $messagesSendCounter = getMessagesSendCounter($db);
    $messagesRecievedCounter = getMessagesRecievedCounter($db);
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
        <div class="overlay">
            <video autoplay="" muted="" loop="">
                <source src="/public/video/demo.mp4" type="video/mp4">
            </video>
        </div>
        <div class="c-container" id="bg">
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
                <?php if (empty($_SESSION['auth']) && empty($errors['system'])) { ?>
                    <div id="authorization" class="container dots">
                        <div>
                            <form method="POST">
                                <table class="<?= empty($messages['form']['auth']) ? '' : 'error' ?>">
                                    <tr>
                                        <td colspan="2" class="auth-form-header">
                                            <div>
                                                <div class="light"></div>
                                                <div class="form-title">
                                                    <span>Авторизация</span>
                                                </div>
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
                                            <br>
                                            <div id="authButton">
                                                <div class="b-left"><img alt="lock" width="30" src="/public/images/lock.png" /></div>
                                                <button name="authorize" value="1" type="submit">Авторизоваться</button>
                                                <div class="b-right"><img alt="lock" width="30" src="/public/images/lock.png" /></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <?php if (empty($errors['system'])) { ?>
                                <?php if (empty($_SESSION['auth'])) { ?>
                                    <div class="auth-req">
                                        <div class="align-center">
                                            <?php foreach ($messages['form']['auth'] as $message) { ?>
                                                <div class="auth-form-submit-area">
                                                    <div class="message error"><?php echo $message; ?></div>
                                                </div>
                                            <?php } ?>
                                            <p>Пользователь: <?=$users['admin']['login']?></p>
                                            <p>Пароль: <?=$users['admin']['password']?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($errors['configuration'])) { ?>
                    <div class="container">
                        <div class="align-center">
                            <?php foreach ($errors['configuration'] as $error) { ?>
                                <div class="message error"><?php echo $error; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($_SESSION['auth']) && empty($errors['system'])) { ?>
                    <div class="container dots">
                        <div class="switch-panel">
                            <div id="messageBox">
                                <input type="hidden" id="sysIKey" value="<?=getSystemKey($db)?>">
                                <input type="hidden" id="messageServiceUrl" value="<?=$serviceMsgUrl?>">
                                <div class="message-box-top">
                                    <img alt="msg" height="22" src="public/images/msg-l.svg" />&nbsp;&nbsp;&nbsp;
                                    <button id="openMessageFieldButton" onclick="openMessageField(this);" class="dark-btn">Отправить сообщение</button>&nbsp;&nbsp;&nbsp;
                                    <img alt="msg" height="22" src="public/images/msg-r.svg" />
                                </div>
                                <div class="m-box-line"></div>
                                <div id="messageField" class="message-box-center">
                                    <p>Внимание! Можно отправить только одно сообщение <br>не длиннее 200 символов.<br><a href="#dataObjects">[информация об использовании]</a></p>
                                    <textarea id="messageText" placeholder="Введите сообщение" class="message-text" oninput="checkInputLength(this, 200);"></textarea>
                                    <div class="ajax-loader">
                                        <div id="lamp1"></div>
                                        <div id="lamp2"></div>
                                        <div id="lamp3"></div>
                                    </div>
                                    <button id="messageCancelButton" onclick="cancelMessage(this);" class="dark-btn">Отмена</button>
                                    <button id="messageSendButton" onclick="sendMessage(this);" class="dark-btn">Отправить</button>
                                    <button id="messageRecieveButton" onclick="getMessage(this);" class="dark-btn">Получить сообщение</button>
                                    <br>
                                    <br>
                                    <div class="counter-window">Отправлено <span id="sendMessagesCounter"><?=$messagesSendCounter?></span></div> <div class="counter-window">Получено <span id="recievedMessagesCounter"><?=$messagesRecievedCounter?></span></div>
                                </div>
                                <div class="m-box-line"></div>
                                <div class="message-box-bottom">
                                    <div id="display" onclick="focusDisplay();" tabindex="1">
                                        <div id="consoleText">
                                            <p class="user-command">[user@system]:# <span id="consoleUIText"><span id="UICommand"></span><span id="consoleUICursor">█</span></span></p>
                                        </div>
                                    </div>
                                    <span class="version">console version 0.01</span>
                                </div>
                            </div>
                            <table class="r-table">
                                <tr>
                                    <td>
                                        Включить\выключить анимацию
                                    </td>
                                    <td>
                                        <div class="switcher">
                                            <input onchange="switchAnimation(this);" type="checkbox" id="lamps" />
                                            <div class="handle-box">
                                                <div class="handle"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="container canvas-container">
                        <img class="rack-left" alt="" src="/public/images/rack-left.png">
                        <img class="rack-right" alt="" src="/public/images/rack-right.png">
                        <canvas id="rack1"></canvas>
                    </div>
                <?php } ?>
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
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - представление структуры данных в древовидном исполнении с разграничением прав доступа: <a href="#dataObjects">перейти</a>
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
                    <img class="rack-left" alt="" src="/public/images/rack-left.png">
                    <img class="rack-right" alt="" src="/public/images/rack-right.png">
                    <canvas id="rack2"></canvas>
                </div>
                <?php if (empty($errors['system'])) { ?>
                    <div id="dataObjects" class="container dots">
                        <h3>СТРУКТУРА ДАННЫХ (объекты):</h3>
                        <p>Дерево объектов. Чтобы увидеть описание объекта необходимо кликнуть на его название.</p>
                        <?php if (!empty($_SESSION['auth']) && empty($errors['system'])) { ?>
                            <a class="btn" href="./admin">Редактировать</a>
                            <br>
                            <br>
                        <?php } ?>
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
                        <img class="rack-left" alt="" src="/public/images/rack-left.png">
                        <img class="rack-right" alt="" src="/public/images/rack-right.png">
                        <canvas id="rack3"></canvas>
                    </div>
                    <div id="codeExample" class="container factory-check">
                        <h3>Пример реализации ООП:</h3>
                        <p>Примером реализации ООП в PHP является представление нескольких классов для описания объектов манипулирования средствами языка программирования реализуя паттерн "<a target="_blank" href="https://designpatternsphp.readthedocs.io/ru/latest/Creational/FactoryMethod/README.html">фабричный метод</a>".</p>
                        <p>[интерфейсы]: PhoneFactoryInterface, Phone</p>
                        <p>[классы]: PhoneFactory, SmartPhoneFactory, SimplePhone, SmartPhone</p>
                        <p>[классы для тестирования]: Test</p>
                        <h4>Код PHP:</h4>
                        <div class="code">
                            <i>
                                $phoneFactory = new PhoneFactory();<br>
                                $simplePhone = $phoneFactory->create([<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Аппарат',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'description' => 'Телефон с антибликовым покрытием экрана, соответствующий стандарту защиты IP127001',<br>
                                ]);<br>
                                echo '&lt;span&gt;Название телефона&lt;/span&gt;: ' . $simplePhone->getTitle() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Описание телефона&lt;/span&gt;: ' . $simplePhone->getDescription() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Функции телефона&lt;/span&gt;:&lt;br&gt;';<br>
                                foreach ($simplePhone->getFunctions() as $phoneFunction) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&#38;nbsp;&#38;nbsp;&#38;nbsp;&#38;nbsp;- ' . $phoneFunction . '&lt;br&gt;';<br>
                                }<br>
                                if (Test::testPhone($simplePhone)) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&lt;span class="tested">✔ протестировано: &lt;i&gt;- Класс телефона "Аппарат" создан фабрикой&lt;/i&gt;&lt;/span&gt;';<br>
                                }<br>
                                echo '&lt;br&gt;&lt;br&gt;';<br>
                                $phoneFactory = new SmartPhoneFactory();<br>
                                $smartPhone = $phoneFactory->create([<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Смарт Аппарат',<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;'description' => 'Смартфон с защитным экраном GCore2Glass',<br>
                                ]);<br>
                                echo '&lt;span&gt;Название телефона&lt;/span&gt;: ' . $smartPhone->getTitle() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Описание телефона&lt;/span&gt;: ' . $smartPhone->getDescription() . '&lt;br&gt;';<br>
                                echo '&lt;span&gt;Функции телефона&lt;/span&gt;:&lt;br&gt;';<br>
                                foreach ($smartPhone->getFunctions() as $phoneFunction) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&#38;nbsp;&#38;nbsp;&#38;nbsp;&#38;nbsp;- ' . $phoneFunction . '&lt;br&gt;';<br>
                                }<br>
                                if (Test::testPhone($smartPhone)) {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;echo '&lt;span class="tested">✔ протестировано: &lt;i&gt;- Класс телефона "Смарт Аппарат" создан фабрикой&lt;/i&gt;&lt;/span&gt;';<br>
                                }<br>
                            </i>
                        </div>
                        <h4>Результат выполнения кода PHP:</h4>
                        <?php
                            $phoneFactory = new PhoneFactory();
                            $simplePhone = $phoneFactory->create([
                                'title' => 'Аппарат',
                                'description' => 'Телефон с антибликовым покрытием экрана, соответствующий стандарту защиты IP127001',
                            ]);
                            echo '<span>Название телефона</span>: ' . $simplePhone->getTitle() . '<br>';
                            echo '<span>Описание телефона</span>: ' . $simplePhone->getDescription() . '<br>';
                            echo '<span>Функции телефона</span>:<br>';
                            foreach ($simplePhone->getFunctions() as $phoneFunction) {
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;- ' . $phoneFunction . '<br>';
                            }
                            if (Test::testPhone($simplePhone)) {
                                echo '<span class="tested">✔ протестировано: <i>- Класс телефона "Аппарат" создан фабрикой</i></span>';
                            }
                            echo '<br><br>';
                            $phoneFactory = new SmartPhoneFactory();
                            $smartPhone = $phoneFactory->create([
                                'title' => 'Смарт Аппарат',
                                'description' => 'Смартфон с защитным экраном GCore2Glass',
                            ]);
                            echo '<span>Название телефона</span>: ' . $smartPhone->getTitle() . '<br>';
                            echo '<span>Описание телефона</span>: ' . $smartPhone->getDescription() . '<br>';
                            echo '<span>Функции телефона</span>:<br>';
                            foreach ($smartPhone->getFunctions() as $phoneFunction) {
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;- ' . $phoneFunction . '<br>';
                            }
                            if (Test::testPhone($smartPhone)) {
                                echo '<span class="tested">✔ протестировано: <i>- Класс телефона "Смарт Аппарат" создан фабрикой</i></span>';
                            }
                        ?>
                    </div>
                <?php } ?>
                <div class="container canvas-container">
                    <img class="rack-left" alt="" src="/public/images/rack-left.png">
                    <img class="rack-right" alt="" src="/public/images/rack-right.png">
                    <canvas id="rack4"></canvas>
                </div>
                <audio id="player" controls>
                    <source id="source" src="public/music.mp3" type="audio/mpeg">
                </audio>
            </div>
            <canvas id="fbg"></canvas>
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