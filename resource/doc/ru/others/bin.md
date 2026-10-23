# Бинарная упаковка

webman позволяет упаковать проект в один бинарный файл, что даёт возможность запускать webman на Linux без среды PHP.

> **Внимание**
> Упакованный файл в настоящее время работает только на Linux с архитектурой x86_64. Windows и macOS не поддерживаются.
> Необходимо отключить опцию phar в `php.ini`, установив `phar.readonly = 0`.

## Установка инструмента командной строки
`composer require webman/console`

## Упаковка
Выполните команду
```
php webman build:bin
```
Можно указать версию PHP для упаковки, например
```
php webman build:bin 8.3
```

После упаковки в каталоге build будет создан файл `webman.bin`.

## Запуск
Загрузите webman.bin на сервер Linux и выполните `./webman.bin start` или `./webman.bin start -d`.

## Принцип работы
* Сначала локальный проект webman упаковывается в файл phar
* Затем удалённо загружается php8.x.micro.sfx
* Файлы php8.x.micro.sfx и phar объединяются в один бинарный файл

## Важно
* Рекомендуется использовать одну и ту же версию PHP при разработке и упаковке (например, PHP 8.3 в обоих случаях), чтобы избежать проблем совместимости
* При упаковке загружается исходный код PHP 8, но он не устанавливается локально и не влияет на локальную среду PHP
* webman.bin в настоящее время работает только на Linux x86_64 и не поддерживает macOS
* Упакованные проекты не поддерживают reload; обновление кода требует перезапуска
* По умолчанию файл env не упаковывается (управляется exclude_files в `config/plugin/webman/console/app.php`). При запуске файл env должен находиться в том же каталоге, что и webman.bin
* Во время работы в каталоге с webman.bin создаётся каталог runtime для хранения логов
* webman.bin не читает внешние php.ini. Для настройки php.ini укажите параметры в custom_ini в `config/plugin/webman/console/app.php`
* Некоторые файлы можно исключить из упаковки в `config/plugin/webman/console/app.php`, чтобы уменьшить размер пакета
* Бинарная упаковка не поддерживает корутины Swoole
* Никогда не храните файлы, загруженные пользователями, внутри бинарного пакета. Работа с ними через протокол `phar://` опасна (уязвимость десериализации phar). Загруженные файлы должны храниться отдельно на диске вне пакета
* Если приложению нужно загружать файлы в каталог public, поместите каталог public рядом с webman.bin, настройте `config/app.php` следующим образом и пересоберите пакет:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Отдельная загрузка статического PHP
Если нужен только исполняемый файл PHP без установки окружения, [скачайте статический PHP здесь](https://www.workerman.net/download).

> **Подсказка**
> Для указания файла php.ini для статического PHP: `php -c /your/path/php.ini start.php start -d`

## Поддерживаемые расширения
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Источники проекта

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
