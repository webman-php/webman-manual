# 二进制打包

webman支持将项目打包成一个二进制文件，这使得webman无需php环境也能在linux系统运行起来。

> **注意**
> 打包后的文件目前只支持运行在x86_64架构的linux系统上，不支持windows和mac系统
> 需要关闭`php.ini`的phar配置选项，既设置 `phar.readonly = 0`

## 安装命令行工具
`composer require webman/console ^1.2.24`

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
* 将php8.x.micro.sfx和phar文件拼接为一个二进制文件

## 注意事项
* 本地php版本>=7.2都可以执行打包命令，但是只能打包成php8的二进制文件
* 强烈建议本地php版本和打包版本一致，例如本地是php8.1，打包也用php8.1，避免出现兼容问题
* 打包会下载php8的源码，但是并不会本地安装，不会影响本地php环境
* webman.bin目前只支持在x86_64架构的linux系统运行，不支持在mac系统运行
* 打包后的项目不支持reload，更新代码需要restart重启
* 默认不打包env文件(`config/plugin/webman/console/app.php`中exclude_files控制)，所以启动时env文件应该放置与webman.bin相同目录下
* 运行过程中会在webman.bin所在目录生成runtime目录，用于存放日志文件
* 目前webman.bin不会读取外部php.ini文件，如需要自定义php.ini，请在 `/config/plugin/webman/console/app.php` 文件custom_ini中设置
* 有些文件不需要打包，可以设置`config/plugin/webman/console/app.php`排除掉，避免打包后的文件过大
* 切勿将用户上传的文件存储在二进制包中，因为以`phar://`协议操作用户上传的文件是非常危险的(phar反序列化漏洞)。用户上传的文件必须单独存储在包之外的磁盘中。
* 如果你的业务需要上传文件到public目录，需要将public目录独立出来放在webman.bin所在目录，这时候需要配置`config/app.php`如下并重新打包。
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## 单独下载静态PHP
有时候你只是不想部署PHP环境，只需要一个PHP可执行文件，点击请点击这里下载[静态php下载](https://www.workerman.net/download)

> **提示**
> 如需给静态php指定php.ini文件，请使用以下命令 `php -c /your/path/php.ini start.php start -d`

## 支持的扩展
Core, date, libxml, openssl, pcre, sqlite3, zlib, amqp, apcu, bcmath, calendar, ctype, curl, dba, dom, sockets, event, hash, fileinfo, filter, gd, gettext, json, iconv, SPL, session, standard, mbstring, igbinary, imagick, exif, mongodb, msgpack, mysqlnd, mysqli, pcntl, PDO, pdo_mysql, pdo_pgsql, pdo_sqlite, pdo_sqlsrv, pgsql, Phar, posix, readline, redis, Reflection, shmop, SimpleXML, soap, sodium, sqlsrv, sysvmsg, sysvsem, sysvshm, tokenizer, xlswriter, xml, xmlreader, xmlwriter, xsl, zip, memcache, Zend OPcache

## 项目出处

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
