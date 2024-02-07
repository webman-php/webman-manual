# Бинарная упаковка

webman поддерживает упаковку проекта в один бинарный файл, что позволяет webman запускаться на системах Linux без необходимости наличия среды PHP.

> **Внимание**
> Упакованный файл в настоящее время поддерживает запуск только на системах Linux архитектуры x86_64, не поддерживается на системах Mac.
> Необходимо отключить опцию конфигурации `phar` в `php.ini`, установив `phar.readonly = 0`.

## Установка командной строки инструментов
`composer require webman/console ^1.2.24`

## Настройки конфигурации
Откройте файл `config/plugin/webman/console/app.php` и установите:
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
для исключения некоторых ненужных каталогов и файлов во время упаковки, чтобы избежать излишнего увеличения размера упаковки.

## Упаковка
Выполните команду
```sh
php webman build:bin
```
Также можно указать, с какой версией PHP упаковывать, например
```sh
php webman build:bin 8.1
```

После упаковки будет создан файл `webman.bin` в каталоге `build`.

## Запуск
Загрузите webman.bin на сервер Linux, выполните `./webman.bin start` или `./webman.bin start -d` для запуска.

## Принцип работы
* Сначала локальный проект webman упаковывается в файл phar.
* Затем удаленно загружается php8.x.micro.sfx на локальный компьютер.
* Файлы php8.x.micro.sfx и phar объединяются в один бинарный файл.

## Важно
* Упаковку можно выполнять на локальной машине с версией PHP не ниже 7.2.
* Однако бинарный файл будет создан только для PHP 8.
* Рекомендуется использовать одинаковую версию локальной и упакованной PHP, чтобы избежать проблем совместимости.
* Во время упаковки будет загружен исходный код PHP 8, однако он не будет установлен локально и не повлияет на среду PHP на локальной машине.
* В настоящее время webman.bin поддерживает запуск только на системах Linux архитектуры x86_64, не поддерживается на системах Mac.
* По умолчанию файлы `.env` не упаковываются (управляется параметром `exclude_files` в `config/plugin/webman/console/app.php`), поэтому файл `.env` должен быть размещен в одной папке с webman.bin.
* В процессе работы будет создан каталог `runtime` в каталоге, где находится webman.bin, для хранения файлов журналов.
* В настоящее время webman.bin не будет читать внешний файл php.ini. Если требуется настроить файл php.ini, укажите его в `custom_ini` в файле `/config/plugin/webman/console/app.php`.

## Отдельная загрузка статического PHP
Иногда вам может потребоваться только выполнить PHP-файл без установки среды PHP. Щелкните здесь, чтобы скачать статический PHP [здесь](https://www.workerman.net/download).

> **Совет**
> Если требуется указать файл php.ini для статического PHP, используйте следующую команду: `php -c /your/path/php.ini start.php start -d`.

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
