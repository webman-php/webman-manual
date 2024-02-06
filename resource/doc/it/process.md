# Processi personalizzati

In webman è possibile personalizzare l'ascolto o i processi come in workerman.

> **Nota**
> Gli utenti Windows devono avviare webman utilizzando `php windows.php` per avviare i processi personalizzati.

## Server HTTP personalizzato
A volte potresti avere esigenze particolari che richiedono modifiche al core del servizio http di webman. In questo caso, puoi utilizzare i processi personalizzati.

Ad esempio, creare app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Qui sovrascrivere i metodi in Webman\App
}
```

Aggiungi la seguente configurazione a `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Altre configurazioni omesse ...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Numero di processi
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Impostazioni della classe di richiesta
            'logger' => \support\Log::channel('default'), // Istanza di log
            'app_path' => app_path(), // Posizione della directory app
            'public_path' => public_path() // Posizione della directory pubblica
        ]
    ]
];
```

> **Suggerimento**
> Se desideri disattivare il processo http predefinito di webman, è sufficiente impostare `listen=>''` in `config/server.php`

## Esempio di ascolto del WebSocket personalizzato
Crea `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> Nota: tutte le proprietà onXXX sono pubbliche

Aggiungi la seguente configurazione a `config/process.php`
```php
return [
    // ... Altre configurazioni dei processi omesse ...

    // websocket_test è il nome del processo
    'websocket_test' => [
        // Specifica la classe del processo, cioè la classe Pusher definita in precedenza
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Esempio di processo non di ascolto personalizzato
Crea `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Controlla il database ogni 10 secondi per nuove registrazioni utente
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Aggiungi la seguente configurazione a `config/process.php`
```php
return [
    // ... Altre configurazioni dei processi omesse

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Nota: Se `listen` viene omesso, non verrà ascoltata nessuna porta. Se `count` viene omesso, il numero predefinito di processi sarà 1.

## Spiegazione della configurazione
Una configurazione completa per un processo è la seguente:
```php
return [
    // ... 

    // websocket_test è il nome del processo
    'websocket_test' => [
        // Qui specifica la classe del processo
        'handler' => app\Pusher::class,
        // Protocollo, IP e porta da ascoltare (opzionale)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Numero di processi (opzionale, predefinito 1)
        'count'   => 2,
        // Utente che esegue il processo (opzionale, predefinito utente corrente)
        'user'    => '',
        // Gruppo che esegue il processo (opzionale, predefinito gruppo corrente)
        'group'   => '',
        // Se il processo supporta il reload (opzionale, predefinito true)
        'reloadable' => true,
        // Attiva reusePort (opzionale, richiede php>=7.0, predefinito true)
        'reusePort'  => true,
        // Trasporto (opzionale, impostare su ssl se necessario, predefinito tcp)
        'transport'  => 'tcp',
        // Contesto (opzionale, fornire il percorso del certificato se il trasporto è ssl)
        'context'    => [], 
        // Parametri del costruttore della classe del processo, per la classe process\Pusher::class (opzionale)
        'constructor' => [],
    ],
];
```

## Conclusione
I processi personalizzati in webman sono essenzialmente un'implementazione semplificata di workerman, separando la configurazione dal business e implementando i callback `onXXX` di workerman come metodi delle classi. Altri utilizzi sono completamente identici a workerman.
