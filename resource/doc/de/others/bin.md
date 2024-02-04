# Binärpaketierung

Webman unterstützt das Projekt in eine einzige Binärdatei zu packen, was es ermöglicht, dass Webman auch ohne PHP-Umgebung auf einem Linux-System ausgeführt werden kann.

> **Bitte beachten**
> Die gepackte Datei unterstützt derzeit nur die Ausführung auf einem Linux-System mit x86_64-Architektur und nicht auf Mac-Systemen.
> Es ist erforderlich, die phar-Konfigurationsoptionen in `php.ini` zu deaktivieren, indem `phar.readonly = 0` gesetzt wird.

## Installation des Befehlszeilenwerkzeugs
Führen Sie den Befehl aus:
```
composer require webman/console ^1.2.24
```

## Konfigurationseinstellungen
Öffnen Sie die Datei `config/plugin/webman/console/app.php` und setzen Sie:
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Dies dient dazu, bei der Paketierung einige unnötige Verzeichnisse und Dateien auszuschließen, um zu vermeiden, dass das Paket zu groß wird.

## Paketierung
Führen Sie den Befehl aus:
```
php webman build:bin
```
Es ist auch möglich anzugeben, mit welcher PHP-Version das Paket erstellt werden soll, z. B.:
```
php webman build:bin 8.1
```

Nach dem Paketieren wird eine Datei `webman.bin` im Verzeichnis `bulid` erstellt.

## Starten
Laden Sie die webman.bin auf den Linux-Server hoch und führen Sie `./webman.bin start` oder `./webman.bin start -d` aus, um Webman zu starten.

## Prinzip
* Zuerst wird das lokale Webman-Projekt zu einer phar-Datei verpackt.
* Anschließend wird die Datei `php8.x.micro.sfx` von einem entfernten Ort heruntergeladen und lokal gespeichert.
* Dann werden die Dateien `php8.x.micro.sfx` und die phar-Datei zu einer einzigen Binärdatei zusammengesetzt.

## Hinweise
* Lokale PHP-Versionen ab 7.2 können das Paketierungs-Tool ausführen.
* Es kann jedoch nur eine Binärdatei für PHP 8 erstellt werden.
* Es wird dringend empfohlen, dass die lokale PHP-Version und die Version, mit der das Paket erstellt wird, identisch sind, um Kompatibilitätsprobleme zu vermeiden.
* Bei der Paketierung wird der Quellcode von PHP 8 heruntergeladen, aber nicht lokal installiert, was die lokale PHP-Umgebung nicht beeinflusst.
* Die webman.bin-Datei kann derzeit nur auf einem Linux-System mit x86_64-Architektur ausgeführt werden und nicht auf einem Mac-System.
* Standardmäßig wird die .env-Datei nicht gepackt (gesteuert durch `exclude_files` in `config/plugin/webman/console/app.php`), daher sollte die .env-Datei im gleichen Verzeichnis wie webman.bin platziert sein, wenn Webman gestartet wird.
* Während der Ausführung wird das Verzeichnis runtime im Verzeichnis der webman.bin-Datei generiert, um Protokolldateien zu speichern.
* Die webman.bin-Datei liest derzeit keine externe php.ini-Datei aus. Um eine benutzerdefinierte php.ini zu verwenden, legen Sie dies bitte in `config/plugin/webman/console/app.php` im Abschnitt `custom_ini` fest.

## Separates Herunterladen von statischem PHP
Manchmal möchten Sie nur die PHP-Umgebung nicht bereitstellen und benötigen nur eine ausführbare PHP-Datei. Klicken Sie hier, um das [statische PHP herunterzuladen](https://www.workerman.net/download).

> **Hinweis**
> Wenn Sie eine spezifische php.ini-Datei für das statische PHP angeben möchten, verwenden Sie bitte den folgenden Befehl: `php -c /your/path/php.ini start.php start -d`

## Unterstützte Erweiterungen
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

## Projektquellen
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
