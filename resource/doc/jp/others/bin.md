# バイナリパッケージング

webmanはプロジェクトをバイナリファイルにパッケージ化することができます。これにより、webmanはPHP環境なしでもLinuxシステムで実行できます。

> **注意**
> パッケージ化されたファイルは現在x86_64アーキテクチャのLinuxシステムでのみ実行でき、Macシステムはサポートされていません
> `php.ini`のphar構成オプションを無効にする必要があります。つまり、`phar.readonly = 0` を設定します。

## コマンドラインツールのインストール
`composer require webman/console ^1.2.24`

## 設定
`config/plugin/webman/console/app.php` ファイルを開き、次のように設定します
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
これにより、パッケージ化時にいくつかの不要なディレクトリとファイルが除外され、パッケージサイズが大きくならないようにします。

## パッケージ化
次のコマンドを実行します
```bash
php webman build:bin
```
同時に、どのPHPバージョンでパッケージ化するかを指定することもできます。例えば
```bash
php webman build:bin 8.1
```

パッケージ化した後、`build`ディレクトリに`webman.bin`ファイルが生成されます。

## 起動
webman.binをLinuxサーバーにアップロードし、`./webman.bin start` または `./webman.bin start -d` を実行すると起動します。

## 原理
* 最初に、ローカルのwebmanプロジェクトをpharファイルにパッケージ化します
* 次に、リモートからphp8.x.micro.sfxをダウンロードします
* 最後に、php8.x.micro.sfxとpharファイルを結合してバイナリファイルを作成します

## 注意事項
* ローカルのPHPバージョンが7.2以上であればパッケージ化コマンドを実行できます
* ただし、PHP8のバイナリファイルのみを作成できます
* ローカルのPHPバージョンとパッケージ化バージョンが一致することを強くお勧めします。つまり、ローカルがphp8.0であれば、パッケージ化もphp8.0を使用することで互換性の問題を回避できます
* パッケージ化中にphp8のソースコードがダウンロードされますが、ローカルにインストールされるわけではなく、ローカルのPHP環境に影響を与えません
* webman.binは現時点ではx86_64アーキテクチャのLinuxシステムでのみ実行できます。Macシステムではサポートされていません
* デフォルトではenvファイルをバンドル化しません(`/config/plugin/webman/console/app.php`のexclude_filesで制御)、そのため起動時にはenvファイルを`webman.bin`と同じディレクトリに配置する必要があります
* 実行中には`webman.bin`があるディレクトリに`runtime`ディレクトリが生成され、ログファイルがそこに保存されます
* 現在、`webman.bin`は外部のphp.iniファイルを読み込まないため、カスタムのphp.iniが必要な場合は`/config/plugin/webman/console/app.php`ファイルでcustom_iniを設定してください

## 単独で静的PHPをダウンロード
時々、PHP環境をデプロイしたくない場合があります。その場合は、単にPHPの実行可能ファイルが必要です。[こちらから静的phpをダウンロード](https://www.workerman.net/download)

> **ヒント**
> 静的PHPに独自のphp.iniファイルを指定する必要がある場合は、以下のコマンドを使用してください `php -c /your/path/php.ini start.php start -d`

## サポートされている拡張機能
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

## プロジェクトの出処
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
