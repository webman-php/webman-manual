# 二進制打包

webman 支援將專案打包成一個二進制檔案，讓 webman 無需 PHP 環境也能在 Linux 上執行。

> **注意**
> 打包後的檔案目前僅支援在 x86_64 架構的 Linux 上執行，不支援 Windows 與 Mac
> 需關閉 `php.ini` 的 phar 配置，設定 `phar.readonly = 0`

## 安裝命令列工具
`composer require webman/console`

## 打包
執行指令
```
php webman build:bin
```
可指定使用的 PHP 版本，例如
```
php webman build:bin 8.3
```

打包完成後會在 build 目錄產生 `webman.bin` 檔案。

## 啟動
將 webman.bin 上傳至 Linux 伺服器，執行 `./webman.bin start` 或 `./webman.bin start -d` 即可啟動。

## 原理
* 先將本地 webman 專案打包成 phar 檔案
* 遠端下載 php8.x.micro.sfx 至本地
* 將 php8.x.micro.sfx 與 phar 拼接為一個二進制檔案

## 注意事項
* 強烈建議本地 PHP 版本與打包版本一致（如皆為 PHP 8.3），以避免相容問題
* 打包會下載 PHP 8 原始碼，但不會在本地安裝，不影響本地 PHP 環境
* webman.bin 僅支援在 x86_64 Linux 上執行，不支援 Mac
* 打包後的專案不支援 reload，更新程式碼需 restart 重啟
* 預設不會打包 env 檔（由 `config/plugin/webman/console/app.php` 的 exclude_files 控制），啟動時 env 檔應與 webman.bin 放在同目錄
* 執行時會在 webman.bin 所在目錄產生 runtime 目錄，用於存放日誌
* 目前 webman.bin 不會讀取外部 php.ini，若需自訂，請在 `config/plugin/webman/console/app.php` 的 custom_ini 中設定
* 不需打包的檔案可在 `config/plugin/webman/console/app.php` 中排除，避免包體過大
* 二進制打包不支援 Swoole 協程
* 切勿將使用者上傳檔案存於二進制包內，以 `phar://` 協定存取上傳檔具 phar 反序列化風險。上傳檔須存放在包外的磁碟
* 若需將檔案上傳至 public 目錄，請將 public 目錄獨立放在 webman.bin 同目錄，並在 `config/app.php` 中設定如下後重新打包：
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## 單獨下載靜態 PHP
若只需要 PHP 可執行檔而不想部署完整 PHP 環境，請至此下載：[靜態 PHP 下載](https://www.workerman.net/download)

> **提示**
> 若需為靜態 PHP 指定 php.ini，請使用：`php -c /your/path/php.ini start.php start -d`

## 支援的擴充
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## 專案出處

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
