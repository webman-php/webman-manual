# Binary Packaging

Webman supports packaging projects into a binary file, allowing it to run on Linux systems without the need for a PHP environment.

> **Note**
> The packaged file currently only supports running on x86_64 architecture Linux systems and does not support macOS.
> The `php.ini` phar configuration option needs to be turned off by setting `phar.readonly = 0`.

## Install command line tool
`composer require webman/console ^1.2.24`

## Configuration Settings
Open the `config/plugin/webman/console/app.php` file and set
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
to exclude some unnecessary directories and files during packaging to avoid excessive file size.

## Packaging
Run the command
```
php webman build:bin
```
You can also specify which PHP version to package with, for example
```
php webman build:bin 8.1
```

This will generate a `webman.bin` file in the build directory.

## Startup
Upload webman.bin to the Linux server and execute `./webman.bin start` or `./webman.bin start -d` to start.

## Principle
* First, package the local webman project into a Phar file.
* Then, remotely download php8.x.micro.sfx to the local environment.
* Concatenate php8.x.micro.sfx and the Phar file into a binary file.

## Notes
* Local PHP versions of 7.2 and above can execute the packaging command.
* However, only binary files for PHP 8 can be packaged.
* It is strongly recommended to use the same PHP version for local and packaged versions. For example, if the local version is PHP 8.0, use PHP 8.0 for packaging to avoid compatibility issues.
* The packaging process will download the PHP 8 source code, but will not install it locally or affect the local PHP environment.
* The webman.bin currently only supports running on x86_64 architecture Linux systems and does not support macOS.
* By default, the env file is not packaged (`config/plugin/webman/console/app.php` excludes_files control), so the env file should be placed in the same directory as webman.bin when starting.
* During runtime, a runtime directory will be generated in the directory where webman.bin is located, which is used to store log files.
* Currently, webman.bin does not read external php.ini files. If you need to customize php.ini, please set it in the `config/plugin/webman/console/app.php` file under custom_ini.

## Downloading the Standalone PHP
Sometimes you may only want to deploy the PHP environment and only need an executable PHP file. Click [here](https://www.workerman.net/download) to download the [Standalone PHP](https://www.workerman.net/download).

> **Note**
> If you need to specify the php.ini file for the standalone PHP, please use the following command `php -c /your/path/php.ini start.php start -d`.

## Supported Extensions
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

## Project Source
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
