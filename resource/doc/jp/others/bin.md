# バイナリパッケージング

webmanはプロジェクトをバイナリファイルにパッケージングすることができます。これにより、webmanはPHP環境なしでLinuxシステム上で実行できます。

> **注意**
> パッケージングされたファイルは現在、x86_64アーキテクチャのLinuxシステムでのみ実行できます。macシステムはサポートされていません。
> `php.ini`の`phar`構成オプションを無効にする必要があります。つまり、`phar.readonly = 0`を設定します。

## コマンドラインツールのインストール
`composer require webman/console ^1.2.24`

## 設定
`config/plugin/webman/console/app.php` ファイルを開き、次のように設定します。
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
これにより、パッケージング時にいくつかの不要なディレクトリとファイルが除外され、パッケージのサイズが大きくなるのを避けます。

## パッケージング
次のコマンドを実行します。
```
php webman build:bin
```
または特定のPHPバージョンでパッケージングする場合は、次のように指定できます。
```
php webman build:bin 8.1
```

これにより、`build`ディレクトリに `webman.bin` ファイルが生成されます。

## 起動
webman.binをLinuxサーバーにアップロードし、`./webman.bin start` または `./webman.bin start -d` を実行すると起動します。

## 仕組み
* まず、ローカルのwebmanプロジェクトをpharファイルにパッケージ化します。
* 次に、リモートでphp8.x.micro.sfxをダウンロードしてきます。
* 最後に、php8.x.micro.sfxとpharファイルを結合してバイナリファイルを作成します。

## 注意事項
* ローカルのPHPバージョンは7.2以上であればパッケージングコマンドを実行できます。
* ただし、PHP8のバイナリファイルのみを作成できます。
* ローカルのPHPバージョンとパッケージングバージョンが一致することを強くお勧めします。つまり、ローカルがPHP8.0であれば、パッケージングもPHP8.0を使用してください。互換性の問題を避けるためです。
* パッケージング時にはPHP8のソースコードがダウンロードされますが、ローカルにインストールされることはありませんので、ローカルのPHP環境には影響しません。
* 現在、webman.binはx86_64アーキテクチャのLinuxシステムでのみ動作し、macシステムでは動作しません。
* デフォルトではenvファイルはパッケージングされません(`config/plugin/webman/console/app.php`のexclude_filesで制御されています)。そのため、起動時にはenvファイルをwebman.binと同じディレクトリに配置する必要があります。
* 実行中には、webman.binが存在するディレクトリにruntimeディレクトリが作成され、ログファイルが格納されます。
* 現在、webman.binは外部のphp.iniファイルを読み込みません。カスタムのphp.iniを使用する場合は、`/config/plugin/webman/console/app.php`ファイルの`custom_ini`で設定してください。

## 静的PHPの個別ダウンロード
PHP環境をデプロイしたくない場合があります。その場合は、単一のPHP実行ファイルを使用するだけで十分です。[こちら](https://www.workerman.net/download)から静的なPHPをダウンロードできます。

> **ヒント**
> 静的PHPに独自のphp.iniファイルを指定する必要がある場合は、次のコマンドを使用してください。
> `php -c /your/path/php.ini start.php start -d`

## サポートされる拡張機能
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

## プロジェクトの出典
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
