# Imballaggio binario

webman supporta l'opzione di imballare il progetto in un file binario, che consente a webman di essere eseguito su sistemi Linux anche senza l'ambiente PHP.

> **Nota**
> Dopo l'imballaggio, il file supporta solo l'esecuzione su sistemi Linux basati su architettura x86_64 e non supporta macOS.
> È necessario disattivare l'opzione di configurazione phar del file `php.ini`, impostando `phar.readonly = 0`.

## Installazione dello strumento della riga di comando
Eseguire il comando
```shell
composer require webman/console ^1.2.24
```

## Configurazione
Aprire il file `config/plugin/webman/console/app.php` e impostare
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
per escludere alcune directory e file inutili durante l'imballaggio, al fine di evitare dimensioni eccessive.

## Imballaggio
Eseguire il comando
```shell
php webman build:bin
```
È inoltre possibile specificare con quale versione di PHP eseguire l'imballaggio, ad esempio
```shell
php webman build:bin 8.1
```
Dopo l'imballaggio, verrà generato un file `webman.bin` nella directory di build.

## Avvio
Caricare webman.bin sul server Linux e eseguire `./webman.bin start` o `./webman.bin start -d` per avviarlo.

## Principio
* In primo luogo, il progetto webman locale verrà imballato in un file phar.
* Successivamente, verrà scaricato il file php8.x.micro.sfx in remoto.
* Infine, php8.x.micro.sfx e il file phar verranno concatenati in un file binario.

## Note
* Possono essere eseguiti comandi di imballaggio con una versione locale di PHP >= 7.2, ma verrà solo prodotto un file binario per PHP 8.
* Si consiglia vivamente di utilizzare la stessa versione locale di PHP e di imballaggio per evitare problemi di compatibilità. Ad esempio, se la versione locale è PHP 8.0, utilizzare anche PHP 8.0 per l'imballaggio.
* Durante l'imballaggio, viene scaricato il codice sorgente di PHP 8, ma non viene installato localmente e non influisce sull'ambiente PHP locale.
* Al momento, webman.bin è supportato solo su sistemi Linux basati su architettura x86_64 e non su macOS.
* Per impostazione predefinita, non è incluso il file env nell'imballaggio (`config/plugin/webman/console/app.php` controlla exclude_files), pertanto il file env dovrebbe essere posizionato nella stessa directory di webman.bin al momento dell'avvio.
* Durante l'esecuzione, verrà creata una directory runtime nella stessa directory di webman.bin per memorizzare i file di registro.
* Attualmente, webman.bin non leggerà file php.ini esterni. Se è necessario personalizzare php.ini, è possibile farlo nel file `/config/plugin/webman/console/app.php` custom_ini.

## Download di PHP statici separatamente
A volte potresti non voler distribuire l'ambiente PHP, ma avere solo un file eseguibile per PHP. Fare clic [qui](https://www.workerman.net/download) per scaricare PHP statici.

> **Nota**
> Se è necessario specificare un file php.ini per PHP statici, utilizzare il seguente comando `php -c /your/path/php.ini start.php start -d`.

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

## Origine del progetto
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
