# Processi personalizzati

In Webman è possibile personalizzare i processi di ascolto o i processi in modo simile a Workerman.

> **Nota**
> Gli utenti Windows devono avviare Webman usando `php windows.php` per avviare i processi personalizzati.

## Server http personalizzato
A volte potresti avere esigenze speciali che richiedono modifiche al codice sorgente del servizio http di Webman. In tal caso, puoi utilizzare i processi personalizzati.

Ad esempio, crea il file app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Qui puoi sovrascrivere i metodi presenti in Webman\App
}
```

Nel file `config/process.php` aggiungi la seguente configurazione

```php
use Workerman\Worker;

return [
    // ... altre configurazioni omesse...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // numero dei processi
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // impostazione della classe di richiesta
            'logger' => \support\Log::channel('default'), // istanza di logging
            'app_path' => app_path(), // posizione della directory app
            'public_path' => public_path() // posizione della directory pubblica
        ]
    ]
];
```

> **Suggerimento**
> Se desideri disattivare il processo http predefinito di Webman, è sufficiente impostare `listen=>''` nel file config/server.php

## Esempio di ascolto WebSocket personalizzato
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
> Nota: tutte le proprietà onXXX devono essere pubbliche

Nel file `config/process.php` aggiungi la seguente configurazione
```php
return [
    // ... altre configurazioni di processo omesse...

    // websocket_test è il nome del processo
    'websocket_test' => [
        // Qui specifica la classe del processo, che è la classe Pusher definita in precedenza
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
        // Ogni 10 secondi controlla se ci sono nuovi utenti registrati nel database
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Nel file `config/process.php` aggiungi la seguente configurazione
```php
return [
    // ... altre configurazioni di processo omesse...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Nota: Se non viene specificato listen, non verrà ascoltata alcuna porta; se non viene specificato count, il numero predefinito dei processi sarà 1.

## Spiegazione della configurazione

La definizione completa di una configurazione del processo è la seguente:
```php
return [
    // ... 

    'websocket_test' => [
        // Qui specifica la classe del processo
        'handler' => app\Pusher::class,
        // Protocollo IP e porta da ascoltare (opzionale)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Numero dei processi (opzionale, predefinito 1)
        'count'   => 2,
        // Utente di esecuzione del processo (opzionale, di default l'utente corrente)
        'user'    => '',
        // Gruppo di esecuzione del processo (opzionale, di default il gruppo corrente)
        'group'   => '',
        // Se il processo corrente supporta il ricaricamento (opzionale, predefinito true)
        'reloadable' => true,
        // Abilita l'opzione reusePort (opzionale, richiede php>=7.0, predefinito true)
        'reusePort'  => true,
        // Trasporto (opzionale, impostare su ssl quando è necessario abilitare ssl, predefinito tcp)
        'transport'  => 'tcp',
        // contesto (opzionale, quando il trasporto è ssl, è necessario passare il percorso del certificato)
        'context'    => [], 
        // Parametri del costruttore della classe del processo, in questo caso per la classe process\Pusher::class (opzionale)
        'constructor' => [],
    ],
];
```

## Conclusione
I processi personalizzati di Webman sono in realtà solo una semplice incapsulatura di Workerman, separando la configurazione dal business e implementando i callback `onXXX` di Workerman come metodi delle classi. Altri usi sono identici a quelli di Workerman.
