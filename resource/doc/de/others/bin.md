# Binärpaket

Webman unterstützt das Verpacken eines Projekts in eine einzige ausführbare Binärdatei, die es ermöglicht, Webman auf einem Linux-System ohne PHP-Umgebung auszuführen.

> **Hinweis**
> Die verpackte Datei unterstützt derzeit nur die Ausführung auf Linux-Systemen mit der x86_64-Architektur und nicht auf Mac-Systemen.
> Die phar-Konfigurationsoption in der `php.ini` muss deaktiviert sein, das heißt `phar.readonly = 0` setzen.

## Installation des Befehlszeilenwerkzeugs
Führen Sie den Befehl aus:
```php
composer require webman/console ^1.2.24
```

## Konfigurationseinstellungen
Öffnen Sie die Datei `config/plugin/webman/console/app.php` und legen Sie fest:
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Dies dient dazu, beim Packen bestimmte unnötige Verzeichnisse und Dateien auszuschließen, um eine unnötig große Paketgröße zu vermeiden.

## Paket erstellen
Führen Sie den Befehl aus:
```bash
php webman build:bin
```
Es kann auch spezifiziert werden, mit welcher PHP-Version das Paket erstellt werden soll, zum Beispiel:
```bash
php webman build:bin 8.1
```

Nach dem Erstellen wird in das Verzeichnis `build` eine Datei namens `webman.bin` erstellt.

## Starten
Laden Sie die webman.bin-Datei auf den Linux-Server hoch und führen Sie `./webman.bin start` oder `./webman.bin start -d` aus, um den Server zu starten.

## Prinzip
* Das lokale Webman-Projekt wird zunächst in eine phar-Datei gepackt.
* Anschließend wird php8.x.micro.sfx heruntergeladen.
* Die Datei php8.x.micro.sfx und die phar-Datei werden zu einer Binärdatei verbunden.

## Hinweise
* Das Paketieren kann mit einer lokalen PHP-Version ab 7.2 durchgeführt werden.
* Es kann jedoch nur eine Binärdatei für PHP 8 erstellt werden.
* Es wird dringend empfohlen, dass die lokale PHP-Version mit der Version übereinstimmt, mit der das Paket erstellt wurde, um Kompatibilitätsprobleme zu vermeiden.
* Das Paket enthält den Quellcode von PHP 8, installiert diesen jedoch nicht auf dem lokalen System und beeinflusst nicht die lokale PHP-Umgebung.
* Derzeit wird die webman.bin-Datei nur auf einem Linux-System mit der x86_64-Architektur unterstützt und nicht auf einem Mac-System.
* Standardmäßig wird die env-Datei nicht gepackt (das wird durch `config/plugin/webman/console/app.php` durch exclude_files gesteuert), daher sollte die env-Datei im gleichen Verzeichnis wie die webman.bin-Datei platziert werden.
* Während des Betriebs wird im Verzeichnis der webman.bin-Datei ein `runtime`-Verzeichnis erstellt, in dem Protokolldateien abgelegt werden.
* Die webman.bin-Datei liest derzeit keine externe php.ini-Dateien ein. Wenn eine benutzerdefinierte php.ini erforderlich ist, legen Sie diese bitte in der Datei `/config/plugin/webman/console/app.php` im Abschnitt `custom_ini` fest.

## Separates Herunterladen von statischem PHP
Manchmal möchten Sie nur die PHP-Umgebung nicht bereitstellen, sondern nur eine ausführbare PHP-Datei. Klicken Sie hier, um [statisches PHP herunterzuladen](https://www.workerman.net/download).

> **Hinweis**
> Wenn Sie eine benutzerdefinierte php.ini-Datei für das statische PHP festlegen möchten, verwenden Sie den folgenden Befehl: `php -c /your/path/php.ini start.php start -d`.

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
