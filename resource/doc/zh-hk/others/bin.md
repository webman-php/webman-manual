# 二進制打包

webman支持將項目打包成一個二進制文件，這使得webman無需php環境也能在linux系統運行起來。

> **注意**
> 打包後的文件目前只支持運行在x86_64架構的linux系統上，不支持mac系統
> 需要關閉`php.ini`的phar配置選項，即設定 `phar.readonly = 0`

## 安裝命令行工具
`composer require webman/console ^1.2.24`

## 配置設置
打開 `config/plugin/webman/console/app.php` 文件，設置 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
用於打包時排除一些無用的目錄及文件，避免打包體積過大

## 打包
運行命令
```php
php webman build:bin
```
同時可以指定以哪個php版本打包，例如
```php
php webman build:bin 8.1
```

打包後會在bulid目錄生成一個`webman.bin`文件

## 啟動
將webman.bin上傳至linux服務器，執行 `./webman.bin start` 或 `./webman.bin start -d` 即可啟動。

## 原理
* 首先將本地webman項目打包成一個phar文件
* 然後遠程下載php8.x.micro.sfx到本地
* 將php8.x.micro.sfx和phar文件拼接為一個二進制文件

## 注意事項
* 本地php版本>=7.2都可以執行打包命令
* 但是只能打包成php8的二進制文件
* 強烈建議本地php版本和打包版本一致，也就是如果本地是php8.0，打包也用php8.0，避免出現兼容問題
* 打包會下載php8的源碼，但是並不會本地安裝，不會影響本地php環境
* webman.bin目前只支持在x86_64架構的linux系統運行，不支持在mac系統運行
* 默認不打包env文件(`config/plugin/webman/console/app.php`中exclude_files控制)，所以啟動時env文件應該放置與webman.bin相同目錄下
* 運行過程中會在webman.bin所在目錄生成runtime目錄，用於存放日誌文件
* 目前webman.bin不會讀取外部php.ini文件，如需要自定義php.ini，請在 `/config/plugin/webman/console/app.php` 文件custom_ini中設置

## 單獨下載靜態PHP
有時候你只是不想部署PHP環境，只需要一個PHP可執行文件，點擊請點擊這裡下載[靜態php下載](https://www.workerman.net/download)

> **提示**
> 如需給靜態php指定php.ini文件，請使用以下命令 `php -c /your/path/php.ini start.php start -d`

## 支持的擴展
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

## 項目出處

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
