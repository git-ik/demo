# DEMO #

![alt text](/public/demo.jpg "Демо приложение")

## Для запуска тестового приложения на Linux:
 - установить LAMP (https://ru.wikipedia.org/wiki/LAMP),
 - скачать файлы приложения DEMO и положить в директорию проектов веб сервера (по умолчанию директория веб сервера Apache в Linux - /var/www)
 - проверить включен ли в apache2 mod rewrite (a2enmod rewrite) и проверить настройки директивы AllowOverride All для директории веб проекта в конфиге apache
 - проверить настройки в конфигурационном файле [[/var/www]]/core/config.php
 - настроить MYSQL (создать БД и пользователя как указано в конфигурационном файле)
 - создать в MYSQL базу данных и загрузить в неё файл дампа [[/var/www]]/db.zip

 ## Для запуска тестового приложения на Windows на примере Open Server:
 - установить Open Server
 - скачать файлы приложения DEMO в папку [[папка установки Open Server]]/domains/localhost/
 - проверить настройки в файле [[папка установки Open Server]]/domains/localhost/core/config.php
 - открыть PhpMyAdmin (кликнуть на значок в трее и открыть вкладку "дополнительно"), создать пользователя, базу данных c нужным названием (как в конфигурационном файле) и загрузить в неё дамп файл db.zip
 - открыть в веб браузере http://localhost
