# Бинарная упаковка

webman поддерживает упаковку проекта в один бинарный файл, что позволяет запускать webman на системах Linux без необходимости наличия среды PHP.

> **Внимание**
> Упакованный файл в настоящее время поддерживает запуск только на системах Linux с архитектурой x86_64, не поддерживает системы Mac. 
> Необходимо отключить опцию настройки `phar` в `php.ini`, установив значение `phar.readonly` на 0.

## Установите инструмент командной строки
`composer require webman/console ^1.2.24`

## Настройка
Откройте файл `config/plugin/webman/console/app.php` и установите:
```php
'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Для исключения некоторых ненужных каталогов и файлов при упаковке, чтобы избежать излишних объемов упаковки.

## Упаковка
Запустите команду
```
php webman build:bin
```
Также можно указать, с помощью какой версии PHP упаковать, например
```
php webman build:bin 8.1
```

После упаковки будет создан файл `webman.bin` в каталоге `build`.

## Запуск
Загрузите webman.bin на сервер Linux и выполните `./webman.bin start` или `./webman.bin start -d` для запуска.

## Принцип
* Сначала проект webman упаковывается в файл phar
* Затем удаленно загружается php8.x.micro.sfx
* Файл php8.x.micro.sfx и файл phar объединяются в один бинарный файл

## Важно
* Для выполнения команды упаковки локальная версия PHP должна быть не ниже 7.2
* Однако упаковка поддерживается только для бинарных файлов PHP8
* Настоятельно рекомендуется, чтобы версия локальной PHP была совместимой с версией упаковки, то есть, если локальная версия PHP8.0, то и упаковка должна осуществляться с помощью PHP8.0, чтобы избежать проблем совместимости
* При упаковке загружается исходный код PHP8, но он не устанавливается локально и не влияет на локальную среду PHP
* webman.bin в настоящее время поддерживается только на системах Linux с архитектурой x86_64, не поддерживается на системах Mac
* По умолчанию файл env (`config/plugin/webman/console/app.php` содержит управление exclude_files) не упаковывается, поэтому при запуске файл env должен быть в том же каталоге, что и webman.bin
* В ходе выполнения в каталоге, где находится webman.bin будет создан каталог runtime для хранения файлов журнала
* В настоящее время webman.bin не будет считывать внешний файл php.ini. Если требуется настраивать php.ini, укажите параметры в файле `/config/plugin/webman/console/app.php` в custom_ini

## Самостоятельная загрузка статического PHP
Иногда вам просто нужен исполняемый файл PHP, без необходимости развертывания среды PHP. Нажмите здесь, чтобы скачать [статическую версию PHP](https://www.workerman.net/download)

> **Подсказка**
> Если необходимо указать файл php.ini для статического PHP, используйте следующую команду `php -c /your/path/php.ini start.php start -d`

## Поддерживаемые расширения
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## Источники проекта
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
