# Benutzerdefinierte Prozesse

In webman können Sie wie bei workerman benutzerdefinierte Listener oder Prozesse erstellen.

> **Hinweis**
> Windows-Benutzer müssen "php windows.php" verwenden, um webman zu starten und benutzerdefinierte Prozesse zu starten.

## Benutzerdefinierter HTTP-Dienst
Manchmal haben Sie möglicherweise spezielle Anforderungen, die eine Änderung des Kerncodes des webman-HTTP-Dienstes erfordern. In solchen Fällen können benutzerdefinierte Prozesse implementiert werden.

Beispielsweise erstellen Sie eine neue Datei app\Server.php:

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Hier überschreiben Sie die Methoden in Webman\App
}
```

Fügen Sie in `config/process.php` die folgende Konfiguration hinzu:

```php
use Workerman\Worker;

return [
    // ... Andere Konfigurationen hier...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Anzahl der Prozesse
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Request-Klasse einstellen
            'logger' => \support\Log::channel('default'), // Logger-Instanz
            'app_path' => app_path(), // Speicherort des App-Verzeichnisses
            'public_path' => public_path() // Speicherort des Public-Verzeichnisses
        ]
    ]
];
```

> **Hinweis**
> Wenn Sie den mitgelieferten HTTP-Prozess von webman ausschalten möchten, setzen Sie in der Datei config/server.php `listen=>''`.

## Beispiel für benutzerdefiniertes WebSocket-Listening

Erstellen Sie `app/Pusher.php`:
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
> Hinweis: Alle onXXX-Eigenschaften sind öffentlich.

Fügen Sie in `config/process.php` die folgende Konfiguration hinzu:
```php
return [
    // ... Andere Prozesskonfigurationen hier...
    
    'websocket_test' => [
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Beispiel für benutzerdefinierten nicht lauschenden Prozess

Erstellen Sie `app/TaskTest.php`:
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Überprüfen Sie alle 10 Sekunden, ob sich neue Benutzer registriert haben
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Fügen Sie in `config/process.php` die folgende Konfiguration hinzu:
```php
return [
    // ... Andere Prozesskonfigurationen hier...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Hinweis: Wenn "listen" weggelassen wird, lauscht der Prozess keinen Port, und wenn "count" weggelassen wird, ist die Standardanzahl der Prozesse 1.

## Konfigurationsdateierklärung

Die vollständige Konfiguration für einen Prozess sieht wie folgt aus:
```php
return [
    // ... 
    
    'websocket_test' => [
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 2,
        'user'    => '',
        'group'   => '',
        'reloadable' => true,
        'reusePort'  => true,
        'transport'  => 'tcp',
        'context'    => [], 
        'constructor' => [],
    ],
];
```

## Fazit
Die benutzerdefinierten Prozesse in webman sind im Wesentlichen eine einfache Verpackung von workerman. Sie trennen Konfigurationen von Geschäftslogik und implementieren die `onXXX`-Callback-Funktionen von workerman über Klassenmethoden. Die Verwendung und Funktionsweise ist ansonsten identisch mit workerman.
