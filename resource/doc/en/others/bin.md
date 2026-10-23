# Binary Packaging

Webman supports packaging a project into a single binary file, enabling webman to run on Linux systems without a PHP environment.

> **Note**
> The packaged file currently only supports running on x86_64 architecture Linux systems. Windows and macOS are not supported.
> You must disable the phar configuration option in `php.ini` by setting `phar.readonly = 0`.

## Install command line tool
`composer require webman/console`

## Packaging
Run the command
```
php webman build:bin
```
You can also specify which PHP version to package with, for example
```
php webman build:bin 8.3
```

After packaging, a `webman.bin` file will be generated in the build directory.

## Updating the packaging runtime

PHP 8.2–8.5 security releases are available. The runtime excludes `intl/ICU`, and Linux runtimes use UPX packing. UPX reduces disk size; runtime memory does not decrease proportionally, and startup includes decompression overhead.

The packaging command reuses cached `micro.sfx` or ZIP files in `build`. Remove the matching cache before rebuilding with an updated runtime, for example PHP 8.3:

```bash
rm -f build/php8.3.micro.sfx build/php8.3.micro.sfx.zip
php webman build:bin 8.3
```

If `build_dir` was customized in `config/plugin/webman/console/app.php`, remove the corresponding files there. Existing `webman.bin` files must be rebuilt and deployed to receive the update.

## Startup
Upload webman.bin to your Linux server and run `./webman.bin start` or `./webman.bin start -d` to start.

## Principle
* First, the local webman project is packaged into a phar file
* Then php8.x.micro.sfx is downloaded remotely to the local environment
* php8.x.micro.sfx and the phar file are concatenated into a single binary file

## Notes
* It is strongly recommended to use the same PHP version locally and for packaging (e.g. PHP 8.3 for both) to avoid compatibility issues
* Packaging will download PHP 8 source code but will not install it locally, so it will not affect your local PHP environment
* webman.bin currently only supports running on x86_64 architecture Linux systems and does not support macOS
* Packaged projects do not support reload; code updates require a restart
* By default the env file is not packaged (controlled by exclude_files in `config/plugin/webman/console/app.php`), so the env file should be placed in the same directory as webman.bin when starting
* A runtime directory is created in the directory where webman.bin resides during execution, used for storing log files
* Currently webman.bin does not read external php.ini files. To customize php.ini, set it in the custom_ini option in `config/plugin/webman/console/app.php`
* Some files do not need to be packaged; you can configure exclusions in `config/plugin/webman/console/app.php` to avoid an oversized package
* Binary packaging does not support Swoole coroutines
* Never store user-uploaded files inside the binary package. Operating on user-uploaded files via the `phar://` protocol is very dangerous (phar deserialization vulnerability). User-uploaded files must be stored separately on disk outside the package
* If your application needs to upload files to the public directory, extract the public directory to the same location as webman.bin, then configure `config/app.php` as follows and repackage:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Download Standalone Static PHP
Sometimes you only want a PHP executable without deploying a full PHP environment. Click [here to download static PHP](https://www.workerman.net/download).

> **Tip**
> To specify a php.ini file for static PHP, use: `php -c /your/path/php.ini start.php start -d`

## Supported Extensions
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Project Source

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
