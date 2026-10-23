# バイナリパッケージング

webmanはプロジェクトを1つのバイナリファイルにパッケージ化でき、PHP環境がなくてもLinuxで動作します。

> **注意**
> パッケージ化されたファイルは現在x86_64アーキテクチャのLinuxでのみ動作し、WindowsとmacOSはサポートされていません
> `php.ini`のpharオプションを無効にする必要があります（`phar.readonly = 0` を設定）

## コマンドラインツールのインストール
`composer require webman/console`

## パッケージ化
次のコマンドを実行します
```
php webman build:bin
```
使用するPHPバージョンを指定することもできます。例：
```
php webman build:bin 8.3
```

パッケージ化後、buildディレクトリに`webman.bin`ファイルが生成されます。

## 起動
webman.binをLinuxサーバーにアップロードし、`./webman.bin start` または `./webman.bin start -d` で起動します。

## 原理
* まず、ローカルのwebmanプロジェクトをpharファイルにパッケージ化します
* 次に、php8.x.micro.sfxをリモートからダウンロードします
* php8.x.micro.sfxとpharファイルを結合して1つのバイナリファイルを作成します

## 注意事項
* 互換性の問題を避けるため、ローカルとパッケージ化で同じPHPバージョン（例：両方PHP 8.3）を使用することを強く推奨します
* パッケージ化時にPHP 8のソースがダウンロードされますが、ローカルにはインストールされず、ローカルPHP環境には影響しません
* webman.binは現在x86_64 Linuxでのみ動作し、macOSはサポートされていません
* パッケージ化されたプロジェクトはreload非対応で、コード更新時は再起動が必要です
* デフォルトではenvファイルはパッケージに含まれません（`config/plugin/webman/console/app.php`のexclude_filesで制御）。起動時にenvファイルはwebman.binと同じディレクトリに配置してください
* 実行中、webman.binのディレクトリにruntimeディレクトリが作成され、ログが保存されます
* 現在、webman.binは外部のphp.iniを読みません。php.iniをカスタマイズする場合は、`config/plugin/webman/console/app.php`のcustom_iniで設定してください
* 不要なファイルは`config/plugin/webman/console/app.php`で除外でき、パッケージ肥大化を防げます
* バイナリパッケージではSwooleコルーチンは使用できません
* ユーザーアップロードファイルをバイナリパッケージ内に保存しないでください。`phar://`プロトコルでの操作は危険です（pharデシリアライズ脆弱性）。アップロードファイルはパッケージ外のディスクに保存してください
* publicディレクトリへアップロードする場合は、publicディレクトリをwebman.binと同じ場所に配置し、`config/app.php`を次のように設定してから再パッケージ化してください：
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## 静的PHPの単独ダウンロード
PHP環境を導入せず、PHP実行ファイルだけが必要な場合は、[静的PHPをダウンロード](https://www.workerman.net/download)してください。

> **ヒント**
> 静的PHPにphp.iniを指定する場合：`php -c /your/path/php.ini start.php start -d`

## サポート拡張モジュール
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## プロジェクト出典

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
