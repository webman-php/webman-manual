# Benutzerdefinierter Prozess

In webman können Sie wie in Workerman benutzerdefinierte Listener- oder Prozesse erstellen.

> **Hinweis**
> Windows-Benutzer müssen `php windows.php` verwenden, um webman zu starten und benutzerdefinierte Prozesse zu starten.

## Benutzerdefinierter HTTP-Server
Manchmal haben Sie möglicherweise spezielle Anforderungen, die eine Änderung des Kerncodes des webman HTTP-Servers erfordern. In diesem Fall können Sie benutzerdefinierte Prozesse verwenden.

Beispielsweise erstellen Sie eine neue Datei app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Hier überschreiben Sie die Methoden von Webman\App
}
```

Fügen Sie die folgende Konfiguration zu `config/process.php` hinzu

```php
use Workerman\Worker;

return [
    // ... Andere Konfigurationen hier ausgelassen...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Anzahl der Prozesse
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Anforderungsklasse einrichten
            'logger' => \support\Log::channel('default'), // Protokollinstanz
            'app_path' => app_path(), // Position des App-Verzeichnisses
            'public_path' => public_path() // Position des Public-Verzeichnisses
        ]
    ]
];
```

> **Hinweis**
> Wenn Sie den mitgelieferten HTTP-Prozess von webman ausschalten möchten, setzen Sie einfach `listen => ''` in der Datei `config/server.php`.

## Beispiel für benutzerdefinierten WebSocket-Listener

Erstellen Sie `app/Pusher.php`
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
> Hinweis: Alle onXXX-Eigenschaften sind public

Fügen Sie die folgende Konfiguration zu `config/process.php` hinzu
```php
return [
    // ... Andere Prozesskonfigurationen hier ausgelassen ...
    
    // websocket_test ist der Name des Prozesses
    'websocket_test' => [
        // Hier wird die Prozessklasse angegeben, d.h. die oben definierte Pusher-Klasse
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Beispiel für benutzerdefinierten Nicht-Listener-Prozess
Erstellen Sie `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Überprüfe alle 10 Sekunden, ob sich ein neuer Benutzer registriert hat
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Fügen Sie die folgende Konfiguration zu `config/process.php` hinzu
```php
return [
    // ... Andere Prozesskonfigurationen hier ausgelassen...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Hinweis: Wenn listen weggelassen wird, lauscht der Prozess keinen Port. Wenn count weggelassen wird, ist die Anzahl der Prozesse standardmäßig auf 1 festgelegt.

## Erklärung der Konfigurationsdatei

Eine vollständige Konfiguration für einen Prozess ist wie folgt definiert:
```php
return [
    // ... 

    // websocket_test ist der Prozessname
    'websocket_test' => [
        // Hier wird die Prozessklasse angegeben
        'handler' => app\Pusher::class,
        // Protokoll, IP und Port zum Lauschen (optional)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Anzahl der Prozesse (optional, standardmäßig 1)
        'count'   => 2,
        // Benutzer, unter dem der Prozess läuft (optional, standardmäßig aktueller Benutzer)
        'user'    => '',
        // Benutzergruppe, unter der der Prozess läuft (optional, standardmäßig aktuelle Benutzergruppe)
        'group'   => '',
        // Ist der aktuelle Prozess reloadable? (optional, standardmäßig true)
        'reloadable' => true,
        // Ist reusePort aktiviert? (optional, diese Option erfordert php>=7.0, standardmäßig true)
        'reusePort'  => true,
        // Transport (optional, wenn SSL aktiviert werden soll, auf ssl setzen, standardmäßig tcp)
        'transport'  => 'tcp',
        // Kontext (optional, wenn transport auf ssl steht, muss der Pfad des Zertifikats übergeben werden)
        'context'    => [], 
        // Konstruktorparameter der Prozessklasse, dies sind optionale Parameter (optional)
        'constructor' => [],
    ],
];
```

## Zusammenfassung
Benutzerdefinierte Prozesse in webman sind eigentlich nur eine einfache Verpackung von Workerman. Es trennt die Konfiguration von der Geschäftslogik und implementiert die `onXXX`-Rückrufe von Workerman durch Methoden in Klassen. Alle anderen Verwendungszwecke bleiben vollständig identisch mit Workerman.
