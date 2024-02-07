# Pacchetto phar

Phar è un tipo di file di impacchettamento simile a JAR in PHP, che consente di impacchettare il progetto webman in un singolo file phar per facilitarne la distribuzione.

**Un ringraziamento speciale a [fuzqing](https://github.com/fuzqing) per il suo PR.**

> **Nota**
> È necessario disabilitare le opzioni di configurazione phar nel file `php.ini`, impostando `phar.readonly = 0`

## Installazione del tool da riga di comando
Eseguire il comando `composer require webman/console`

## Configurazione
Aprire il file `config/plugin/webman/console/app.php` e impostare `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` per escludere alcune cartelle e file inutili durante l'impacchettamento, evitando così di creare un phar troppo grande.

## Impacchettamento
Eseguire il comando `php webman phar:pack` nella cartella radice del progetto webman. Questo genererà un file `webman.phar` nella cartella "build".

> Le configurazioni relative all'impacchettamento sono presenti in `config/plugin/webman/console/app.php`

## Comandi per avviare e interrompere
**Avviare**
`php webman.phar start` o `php webman.phar start -d`

**Interrompere**
`php webman.phar stop`

**Verificare lo stato**
`php webman.phar status`

**Verificare lo stato della connessione**
`php webman.phar connections`

**Riavviare**
`php webman.phar restart` o `php webman.phar restart -d`

## Note
* Dopo aver eseguito webman.phar, verrà generata una cartella runtime nella stessa directory, utilizzata per salvare i file di log e altri file temporanei.

* Se nel progetto viene utilizzato un file .env, è necessario posizionarlo nella stessa directory di webman.phar.

* Se il tuo progetto richiede di caricare file nella cartella public, è necessario isolare la cartella public e posizionarla nella stessa directory di webman.phar. In questo caso, è necessario configurare `config/app.php`.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Il business può utilizzare la funzione di assistenza `public_path()` per trovare effettivamente la posizione della cartella public.

* webman.phar non supporta l'avvio di processi personalizzati in Windows.
