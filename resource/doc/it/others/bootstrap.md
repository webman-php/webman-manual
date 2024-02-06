# Inizializzazione dell'Applicazione

A volte è necessario effettuare alcune inizializzazioni dell'attività dopo l'avvio dei processi. Questa inizializzazione viene eseguita solo una volta durante il ciclo di vita del processo, ad esempio impostare un timer dopo l'avvio del processo o inizializzare la connessione al database. Di seguito verrà fornita un'illustrazione in merito.

## Principio
In base alle spiegazioni del **[flusso di esecuzione](process.md)**, dopo l'avvio del processo, webman carica le classi impostate in `config/bootstrap.php` (inclusi `config/plugin/*/*/bootstrap.php`) ed esegue il metodo di avvio della classe. In questo modo, nel metodo di avvio è possibile inserire il codice dell'attività per completare l'inizializzazione dell'attività dopo l'avvio del processo.

## Procedura
Supponiamo di voler creare un timer per l'invio periodico dell'utilizzo attuale della memoria del processo, il cui nome della classe è `MemReport`.

#### Esecuzione del comando
Eseguire il comando `php webman make:bootstrap MemReport` per generare il file di inizializzazione `app/bootstrap/MemReport.php`.

> **Nota**
> Se webman non ha installato il pacchetto `webman/console`, eseguire il comando `composer require webman/console` per installarlo.

#### Modifica del file di Inizializzazione
Modificare `app/bootstrap/MemReport.php` con un contenuto simile al seguente:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // È un ambiente della riga di comando?
        $is_console = !$worker;
        if ($is_console) {
            // Se non si desidera eseguire questa inizializzazione in un ambiente della riga di comando, è possibile restituire direttamente qui
            return;
        }
        
        // Esegui ogni 10 secondi
        \Workerman\Timer::add(10, function () {
            // Per comodità della dimostrazione, qui utilizziamo l'output al posto del processo di invio
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Nota**
> Anche durante l'uso del terminale, il framework esegue il metodo di avvio configurato in `config/bootstrap.php`. Possiamo valutare se l'ambiente è una riga di comando mediante la variabile `$worker` null, decidendo così se eseguire il codice di inizializzazione dell'attività.

#### Configurazione per l'avvio del processo
Aprire `config/bootstrap.php` e aggiungere la classe `MemReport` agli elementi di avvio.
```php
return [
    // ... Altre configurazioni sono omesse ...

    app\bootstrap\MemReport::class,
];
```

In questo modo abbiamo completato il flusso di inizializzazione dell'attività.

## Note Aggiuntive
Dopo l'avvio del processo, viene eseguito anche il metodo di avvio configurato in `config/bootstrap.php` per l'avvio del [processo personalizzato](../process.md). Possiamo valutare il tipo di processo attuale tramite `$worker->name` e decidere se eseguire il codice di inizializzazione dell'attività in quel processo. Ad esempio, se non desideriamo monitorare il processo `monitor`, il contenuto di `MemReport.php` sarebbe simile al seguente:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // È un ambiente della riga di comando?
        $is_console = !$worker;
        if ($is_console) {
            // Se non si desidera eseguire questa inizializzazione in un ambiente della riga di comando, è possibile restituire direttamente qui
            return;
        }
        
        // Il processo di monitoraggio non esegue il timer
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Esegui ogni 10 secondi
        \Workerman\Timer::add(10, function () {
            // Per comodità della dimostrazione, qui utilizziamo l'output al posto del processo di invio
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
