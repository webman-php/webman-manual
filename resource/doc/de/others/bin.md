# Binärpackaging

Webman unterstützt das Verpacken eines Projekts in eine einzige Binärdatei, sodass Webman auf Linux-Systemen ohne PHP-Umgebung ausgeführt werden kann.

> **Hinweis**
> Die verpackte Datei unterstützt derzeit nur die Ausführung auf x86_64-Linux-Systemen. Windows und macOS werden nicht unterstützt.
> Die phar-Konfigurationsoption in `php.ini` muss deaktiviert sein, indem Sie `phar.readonly = 0` setzen.

## Befehlszeilentool installieren
`composer require webman/console`

## Verpacken
Führen Sie den Befehl aus:
```
php webman build:bin
```
Sie können auch die PHP-Version angeben, mit der gepackt werden soll, z. B.:
```
php webman build:bin 8.3
```

Nach dem Verpacken wird im build-Verzeichnis eine Datei `webman.bin` erstellt.

## Starten
Laden Sie webman.bin auf Ihren Linux-Server hoch und führen Sie `./webman.bin start` oder `./webman.bin start -d` aus.

## Prinzip
* Zunächst wird das lokale Webman-Projekt in eine phar-Datei gepackt
* Anschließend wird php8.x.micro.sfx remote heruntergeladen
* php8.x.micro.sfx und die phar-Datei werden zu einer Binärdatei zusammengefügt

## Hinweise
* Es wird dringend empfohlen, lokal und beim Verpacken dieselbe PHP-Version zu verwenden (z. B. PHP 8.3 für beides), um Kompatibilitätsprobleme zu vermeiden
* Beim Verpacken wird der PHP-8-Quellcode heruntergeladen, aber nicht lokal installiert und beeinflusst die lokale PHP-Umgebung nicht
* webman.bin unterstützt derzeit nur x86_64-Linux-Systeme und nicht macOS
* Verpackte Projekte unterstützen kein Reload; Code-Änderungen erfordern einen Neustart
* Standardmäßig wird die env-Datei nicht mitverpackt (gesteuert durch exclude_files in `config/plugin/webman/console/app.php`). Die env-Datei muss beim Start im gleichen Verzeichnis wie webman.bin liegen
* Während der Ausführung wird im Verzeichnis von webman.bin ein runtime-Verzeichnis für Logdateien angelegt
* webman.bin liest derzeit keine externen php.ini-Dateien. Für eigene php.ini-Einstellungen nutzen Sie die Option custom_ini in `config/plugin/webman/console/app.php`
* Einige Dateien müssen nicht verpackt werden; Ausschlüsse können in `config/plugin/webman/console/app.php` konfiguriert werden, um zu große Pakete zu vermeiden
* Binärpackaging unterstützt keine Swoole-Koroutinen
* Speichern Sie niemals Benutzer-Uploads im Binärpaket. Der Zugriff auf Uploads über das `phar://`-Protokoll ist riskant (phar-Deserialisierung). Benutzer-Uploads müssen separat auf der Festplatte außerhalb des Pakets liegen
* Falls Ihre Anwendung in das public-Verzeichnis hochlädt, legen Sie public in das Verzeichnis von webman.bin, konfigurieren Sie `config/app.php` wie folgt und verpacken Sie neu:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Statisches PHP separat herunterladen
Wenn Sie nur eine PHP-Executable ohne vollständige PHP-Installation benötigen: [Statisches PHP herunterladen](https://www.workerman.net/download)

> **Tipp**
> Um eine php.ini für statisches PHP anzugeben: `php -c /your/path/php.ini start.php start -d`

## Unterstützte Erweiterungen
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Projektquellen

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
