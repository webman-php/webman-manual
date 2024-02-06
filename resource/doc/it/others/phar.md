# Imballaggio Phar

Phar è un file di imballaggio simile a JAR in PHP, che consente di impacchettare il tuo progetto webman in un singolo file Phar per facilitarne il rilascio.

**Un ringraziamento speciale a [fuzqing](https://github.com/fuzqing) per il suo PR.**

> **Nota**
> È necessario disabilitare le opzioni di configurazione Phar in `php.ini`, cioè impostare `phar.readonly = 0`.

## Installazione dello strumento da riga di comando
Esegui `composer require webman/console`.

## Configurazione
Apri il file `config/plugin/webman/console/app.php` e imposta `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` per escludere alcune directory e file non utilizzati durante l'imballaggio, evitando così di aumentare eccessivamente le dimensioni del pacchetto.

## Imballaggio
Esegui il comando `php webman phar:pack` nella directory radice del progetto webman.
Verrà generato un file `webman.phar` nella directory 'build'.

> Le configurazioni di imballaggio sono presenti in `config/plugin/webman/console/app.php`.

## Comandi per avviare e fermare
**Avvio**
`php webman.phar start` o `php webman.phar start -d`

**Arresto**
`php webman.phar stop`

**Stato**
`php webman.phar status`

**Stato dei collegamenti**
`php webman.phar connections`

**Riavvio**
`php webman.phar restart` o `php webman.phar restart -d`

## Note
* Una volta avviato webman.phar, verrà generata una directory 'runtime' nella directory in cui si trova webman.phar, utilizzata per memorizzare file temporanei come i log.

* Se il tuo progetto utilizza un file .env, assicurati di posizionarlo nella stessa directory di webman.phar.

* Nel caso in cui il tuo progetto richieda di caricare file nella directory pubblica, separala e posizionala nella stessa directory di webman.phar. In tal caso, sarà necessario modificare `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Il tuo progetto potrà utilizzare la funzione helper `public_path()` per trovare la posizione effettiva della directory pubblica.

* webman.phar non supporta l'avvio di processi personalizzati su Windows.
