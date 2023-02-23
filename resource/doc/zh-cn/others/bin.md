# 二进制打包

webman支持将项目打包成一个二进制文件，这使得webman无需php环境也能在linux系统运行起来。

> **注意**
> 打包后的文件目前只支持运行在x86_64架构的linux系统上，不支持mac系统
> 需要关闭`php.ini`的phar配置选项，既设置 `phar.readonly = 0`

## 安装命令行工具
`composer require webman/console ^1.2.24`

## 配置设置
打开 `config/plugin/webman/console/app.php` 文件，设置 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
用于打包时排除一些无用的目录及文件，避免打包体积过大

## 打包
运行命令
```
php webman build:bin
```
同时可以指定以哪个php版本打包，例如
```
php webman build:bin 8.1
```

打包后会在bulid目录生成一个`webman.bin`文件

## 启动
将webman.bin上传至linux服务器，执行 `./webman.bin start` 或 `./webman.bin start -d` 即可启动。

## 原理
* 首先将本地webman项目打包成一个phar文件
* 然后远程下载php8.x.micro.sfx到本地
* 将php8.x.micro.sfx和phar文件合并为一个二进制文件

## 注意事项
* 本地php版本>=7.2都可以执行打包命令
* 但是只能打包成php8的二进制文件
* 强烈建议本地php版本和打包版本一致，也就是如果本地是php8.0，打包也用php8.0，避免出现兼容问题
* 打包会下载php8的源码，但是并不会本地安装，不会影响本地php环境
* webman.bin目前只支持在x86_64架构的linux系统运行，不支持在mac系统运行
* 默认不打包env文件(`config/plugin/webman/console/app.php`中exclude_files控制)，所以启动时env文件应该放置与webman.bin相同目录下
* 运行过程中会在webman.bin所在目录生成runtime目录，用于存放日志文件
* 目前webman.bin不会读取外部任何php.ini文件
* webman.bin文件体积至少30M，加上业务代码体积会很大，建议在`app/functions.php`首行加入`ini_set('memory_limit', '512M');`提高PHP内存限制，避免出现内存超限错误

## 单独下载静态PHP
有时候你只是不想部署PHP环境，只需要一个PHP可执行文件，点击请点击这里下载[静态php下载](https://www.workerman.net/download)

## 支持的扩展
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

## 项目出处

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
