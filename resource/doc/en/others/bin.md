# Binary Packaging

webman supports packaging the project into a binary file, which allows webman to run on a Linux system without the need for a PHP environment.

> **Note**
> The packaged file is currently only supported to run on x86_64 architecture Linux systems and does not support macOS systems.
> The phar configuration option in `php.ini` needs to be disabled, i.e. set `phar.readonly = 0`

## Install Command Line Tool
`composer require webman/console ^1.2.24`

## Configuration Settings
Open the `config/plugin/webman/console/app.php` file and set 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
to exclude some unnecessary directories and files when packaging, to avoid excessive package size

## Packaging
Run the command
```
php webman build:bin
```
You can also specify which version of PHP to use for packaging, for example
```
php webman build:bin 8.1
```

After packaging, a `webman.bin` file will be generated in the build directory.

## Startup
Upload the webman.bin file to the Linux server and execute `./webman.bin start` or `./webman.bin start -d` to start.

## Principle
* First, package the local webman project into a phar file
* Then remotely download php8.x.micro.sfx to the local machine
* Concatenate php8.x.micro.sfx and the phar file into a binary file

## Notes
* Local PHP versions >= 7.2 can execute the packaging command
* However, only PHP 8 binary files can be packaged
* It is strongly recommended to use the same local PHP version for packaging to avoid compatibility issues
* The packaging will download the PHP 8 source code, but will not install it locally, and will not affect the local PHP environment
* webman.bin currently only supports running on x86_64 architecture Linux systems and does not support running on macOS
* By default, env files are not packaged (`config/plugin/webman/console/app.php` controls the exclude_files), so the env file should be placed in the same directory as webman.bin when started
* During runtime, a runtime directory will be generated in the directory where webman.bin is located, used to store log files
* Currently, webman.bin does not read external php.ini files. If a custom php.ini is needed, please set it in the `/config/plugin/webman/console/app.php` file under custom_ini.

## Download Standalone PHP
Sometimes, if you only need an executable PHP file and do not want to deploy the PHP environment, you can download the [standalone PHP here](https://www.workerman.net/download)

> **Tip**
> If you need to specify a php.ini file for the standalone PHP, use the following command: `php -c /your/path/php.ini start.php start -d`

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

## Project Sources

- https://github.com/crazywhalecc/static-php-cli
- https://github.com/walkor/static-php-cli