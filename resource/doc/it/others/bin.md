# Imballaggio binario

webman supporta l'imballaggio del progetto in un unico file binario, consentendo l'esecuzione su Linux senza ambiente PHP.

> **Nota**
> Il file impacchettato supporta attualmente solo l'esecuzione su sistemi Linux x86_64. Windows e macOS non sono supportati.
> È necessario disattivare l'opzione phar in `php.ini` impostando `phar.readonly = 0`.

## Installare lo strumento da riga di comando
`composer require webman/console`

## Imballaggio
Eseguire il comando
```
php webman build:bin
```
È possibile specificare la versione di PHP da usare per l'imballaggio, ad esempio
```
php webman build:bin 8.3
```

Dopo l'imballaggio, verrà generato un file `webman.bin` nella directory build.

## Avvio
Caricare webman.bin sul server Linux ed eseguire `./webman.bin start` o `./webman.bin start -d` per avviare.

## Principio
* Innanzitutto il progetto webman locale viene impacchettato in un file phar
* Poi php8.x.micro.sfx viene scaricato da remoto
* php8.x.micro.sfx e il file phar vengono concatenati in un unico file binario

## Note
* Si consiglia vivamente di usare la stessa versione di PHP in locale e per l'imballaggio (es. PHP 8.3 per entrambi) per evitare problemi di compatibilità
* L'imballaggio scarica il codice sorgente di PHP 8 ma non lo installa localmente, senza impatto sull'ambiente PHP locale
* webman.bin supporta attualmente solo Linux x86_64 e non macOS
* I progetti impacchettati non supportano reload; gli aggiornamenti al codice richiedono un riavvio
* Di default il file env non viene impacchettato (controllato da exclude_files in `config/plugin/webman/console/app.php`). All'avvio il file env deve trovarsi nella stessa directory di webman.bin
* Durante l'esecuzione viene creata una directory runtime nella directory di webman.bin per i file di log
* webman.bin attualmente non legge file php.ini esterni. Per personalizzare php.ini, configurarlo in custom_ini in `config/plugin/webman/console/app.php`
* Alcuni file non vanno impacchettati; configurate le esclusioni in `config/plugin/webman/console/app.php` per evitare pacchetti troppo grandi
* L'imballaggio binario non supporta le coroutine Swoole
* Non memorizzare mai file caricati dagli utenti nel pacchetto binario. Utilizzarli tramite il protocollo `phar://` è molto pericoloso (vulnerabilità di deserializzazione phar). I file caricati devono essere memorizzati separatamente su disco fuori dal pacchetto
* Se l'applicazione deve caricare file nella directory public, collocate la directory public nella stessa posizione di webman.bin, configurate `config/app.php` come segue e impacchettate di nuovo:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Scaricare PHP statico separatamente
Se serve solo un eseguibile PHP senza distribuire un ambiente PHP completo, [scaricare PHP statico qui](https://www.workerman.net/download).

> **Suggerimento**
> Per specificare un file php.ini per PHP statico: `php -c /your/path/php.ini start.php start -d`

## Estensioni supportate
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Origine del progetto

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
