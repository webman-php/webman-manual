# Pacchettizzazione binaria

webman supporta l'impacchettamento del progetto in un file binario, il che consente a webman di essere eseguito su sistemi Linux senza l'ambiente PHP.

> **Nota**
> Il file pacchettizzato attualmente supporta solo l'esecuzione su sistemi Linux con architettura x86_64 e non supporta il sistema macOS.
> È necessario disattivare l'opzione di configurazione `phar` in `php.ini`, cioè impostare `phar.readonly = 0`.

## Installazione dello strumento da riga di comando
`composer require webman/console ^1.2.24`

## Configurazione
Aprire il file `config/plugin/webman/console/app.php` e impostare
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
per escludere alcune directory e file inutili durante l'impacchettamento, evitando così di creare un pacchetto troppo grande.

## Impacchettamento
Eseguire il comando
```
php webman build:bin
```
È possibile specificare anche la versione di PHP con cui impacchettare, ad esempio
```
php webman build:bin 8.1
```

Dopo l'impacchettamento, verrà creato un file `webman.bin` nella cartella build.

## Avvio
Caricare webman.bin sul server Linux, eseguire `./webman.bin start` o `./webman.bin start -d` per avviarlo.

## Principio
* In primo luogo, il progetto webman locale viene impacchettato in un file phar.
* Poi si scarica il file php8.x.micro.sfx in locale.
* Verranno quindi concatenati il file php8.x.micro.sfx e il file phar in un unico file binario.

## Note
* È possibile eseguire il comando di impacchettamento con una versione di PHP locale >=7.2.
* Tuttavia, è possibile impacchettare solo un file binario per PHP 8.
* È fortemente consigliato avere la stessa versione locale di PHP e quella di impacchettamento, in modo che se la versione locale è 8.0, anche l'impacchettamento utilizzi la versione 8.0, evitando così problemi di compatibilità.
* Il processo di impacchettamento scaricherà il codice sorgente di PHP 8, ma non lo installerà localmente e non influenzerà l'ambiente PHP locale.
* Attualmente webman.bin supporta solo l'esecuzione su sistemi Linux con architettura x86_64 e non su sistemi macOS.
* Per impostazione predefinita, il file env non viene incluso nell'impacchettamento (`config/plugin/webman/console/app.php` controlla exclude_files), quindi il file env deve essere posizionato nella stessa directory di webman.bin durante l'avvio.
* Durante l'esecuzione, verrà generata una cartella runtime nella directory in cui si trova webman.bin, utilizzata per memorizzare i file di log.
* Attualmente webman.bin non legge un file php.ini esterno. Se è necessario personalizzare php.ini, è possibile farlo nel file `/config/plugin/webman/console/app.php` tramite custom_ini.

## Download PHP statico separatamente
A volte potresti non voler distribuire l'ambiente PHP, ma avere solo un file eseguibile di PHP. Clicca qui per scaricare [PHP statico](https://www.workerman.net/download).

> **Suggerimento**
> Se è necessario specificare il file php.ini per PHP statico, utilizzare il seguente comando: `php -c /your/path/php.ini start.php start -d`

## Estensioni supportate
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

## Fonti del progetto
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
