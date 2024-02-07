## Redis-Warteschlange

Eine Nachrichtenwarteschlange basierend auf Redis, die die verzögerte Verarbeitung von Nachrichten unterstützt.

## Installation
`composer require webman/redis-queue`

## Konfigurationsdatei
Die Redis-Konfigurationsdatei wird automatisch unter `config/plugin/webman/redis-queue/redis.php` generiert. Der Inhalt ähnelt dem folgenden Beispiel:
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // Passwort, optionales Feld
            'db' => 0,            // Datenbank
            'max_attempts'  => 5, // Anzahl der Wiederholungsversuche nach fehlgeschlagener Verarbeitung
            'retry_seconds' => 5, // Wartezeit zwischen den Wiederholungsversuchen in Sekunden
        ]
    ],
];
```

### Wiederholungsversuch bei fehlgeschlagener Verarbeitung
Wenn die Verarbeitung fehlschlägt (z. B. aufgrund einer Ausnahme), wird die Nachricht in eine verzögerte Warteschlange verschoben und auf den nächsten erneuten Versuch gewartet. Die Anzahl der Versuche wird durch den Parameter `max_attempts` gesteuert, während das Intervall zwischen den Versuchen von `retry_seconds` und `max_attempts` gemeinsam gesteuert wird. Zum Beispiel, wenn `max_attempts` 5 und `retry_seconds` 10 beträgt, beträgt das Intervall für den ersten Wiederholungsversuch `1*10` Sekunden, für den zweiten Wiederholungsversuch `2*10` Sekunden, für den dritten Wiederholungsversuch `3*10` Sekunden, und so weiter, bis zu 5 Wiederholungsversuchen. Wenn die Anzahl der Wiederholungsversuche, die in `max_attempts` festgelegt ist, überschritten wird, wird die Nachricht in die fehlgeschlagene Warteschlange mit dem Namen `{redis-queue}-failed` verschoben.

## Senden von Nachrichten (synchron)
> **Hinweis**
> Erfordert webman/redis >= 1.2.0 und abhängige Redis-Erweiterung

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Warteschlangenname
        $queue = 'send-mail';
        // Daten können direkt als Array übergeben werden, ohne Serialisierung
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Nachricht senden
        Redis::send($queue, $data);
        // Verzögerte Nachricht senden, die nach 60 Sekunden verarbeitet wird
        Redis::send($queue, $data, 60);

        return response('Redis-Warteschlangentest');
    }
}
```
`Redis::send()` gibt `true` zurück, wenn das Senden erfolgreich war. Andernfalls wird `false` zurückgegeben oder eine Ausnahme ausgelöst.

> **Hinweis**
> Die Verzögerung bei der Verarbeitung von Warteschlangen kann zu Ungenauigkeiten führen, beispielsweise wenn die Verarbeitungsgeschwindigkeit geringer ist als die Produktionsgeschwindigkeit, was zu Warteschlangenüberlastung und damit zu Verzögerungen bei der Verarbeitung führen kann. Ein möglicher Ansatz zur Linderung besteht darin, mehr Verarbeitungsprozesse zu starten.

## Senden von Nachrichten (asynchron)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Warteschlangenname
        $queue = 'send-mail';
        // Daten können direkt als Array übergeben werden, ohne Serialisierung
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Nachricht senden
        Client::send($queue, $data);
        // Verzögerte Nachricht senden, die nach 60 Sekunden verarbeitet wird
        Client::send($queue, $data, 60);

        return response('Redis-Warteschlangentest');
    }
}
```
`Client::send()` gibt keine Rückgabewerte zurück, da es sich um einen asynchronen Aufruf handelt, der nicht garantiert, dass die Nachricht zu 100 Prozent an Redis übermittelt wird.

> **Hinweis**
> Das Funktionsprinzip von `Client::send()` besteht darin, eine interne Speicherwarteschlange zu erstellen und die Nachrichten asynchron zu Redis zu übertragen (die Übertragungsgeschwindigkeit ist sehr hoch, etwa 10.000 Nachrichten pro Sekunde). Wenn der Prozess neu gestartet wird und die Nachrichten in der internen Speicherwarteschlange noch nicht vollständig übertragen wurden, geht die Nachricht verloren. `Client::send()` eignet sich daher für die asynchrone Übermittlung von unwichtigen Nachrichten.

> **Hinweis**
> `Client::send()` ist asynchron und kann nur in der Workerman-Ausführungsumgebung verwendet werden. Für Befehlszeilenskripte sollte die synchrone Schnittstelle `Redis::send()` verwendet werden.

## Senden von Nachrichten in anderen Projekten
Manchmal müssen Sie Nachrichten in anderen Projekten senden und können `webman/redis-queue` nicht verwenden. In diesem Fall können Sie die folgende Funktion als Beispiel verwenden, um Nachrichten an die Warteschlange zu senden.

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

Hierbei ist `$redis` die Redis-Instanz. Ein Beispiel für die Verwendung der Redis-Erweiterung sieht wie folgt aus:
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
````

## Verbrauch
Die Konfigurationsdatei des Verbraucherprozesses befindet sich unter `config/plugin/webman/redis-queue/process.php`. Die Verbraucherklassen befinden sich im Verzeichnis `app/queue/redis/`.

Wenn der Befehl `php webman redis-queue:consumer my-send-mail` ausgeführt wird, wird die Datei `app/queue/redis/MyMailSend.php` generiert.

> **Hinweis**
> Wenn der Befehl nicht vorhanden ist, können Sie die Verbraucherklasse auch manuell erstellen.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Name der zu verarbeitenden Warteschlange
    public $queue = 'send-mail';

    // Name der Verbindung, die der Konfiguration in `plugin/webman/redis-queue/redis.php` entspricht
    public $connection = 'default';

    // Verarbeitung
    public function consume($data)
    {
        // Keine Notwendigkeit das zu deserialisieren
        var_export($data); // Gibt ['to' => 'tom@gmail.com', 'content' => 'hello'] aus
    }
}
```

> **Hinweis**
> Für den Verbrauchsvorgang gilt: Wenn keine Ausnahme oder Fehler aufgetreten sind, gilt der Verbrauch als erfolgreich. Andernfalls wird der Verbrauch als fehlgeschlagen betrachtet und die Nachricht wird in die Wiederholungswarteschlange verschoben.
> Redis-Queue hat keine ACK-Mechanismus. Sie können es als automatisches ACK (bei Abwesenheit von Ausnahme oder Fehler) betrachten. Wenn Sie während des Verbrauchsvorgangs markieren möchten, dass die aktuelle Nachricht nicht erfolgreich verarbeitet wurde, können Sie manuell eine Ausnahme werfen, um die aktuelle Nachricht in die Wiederholungswarteschlange zu verschieben. Dies unterscheidet sich faktisch nicht von einem ACK-Mechanismus.

> **Hinweis**
> Der Verbraucher unterstützt mehrere Server und mehrere Prozesse, und dieselbe Nachricht wird **nicht** doppelt verbraucht. Verarbeitete Nachrichten werden automatisch aus der Warteschlange entfernt und müssen nicht manuell gelöscht werden.

> **Hinweis**
> Der Verbraucherprozess kann gleichzeitig verschiedene Warteschlangen verbrauchen. Das Hinzufügen einer neuen Warteschlange erfordert keine Änderung in der Konfiguration von `process.php`. Es muss lediglich eine entsprechende `Consumer`-Klasse im Verzeichnis `app/queue/redis` hinzugefügt werden und der Name der zu verarbeitenden Warteschlange mit der Klassenvariable `$queue` angegeben werden.

> **Hinweis**
> Windows-Benutzer müssen `php windows.php` ausführen, um webman zu starten, anderfalls wird der Verbraucherprozess nicht gestartet

## Separates Zuweisen von Verarbeitungsprozessen für verschiedene Warteschlangen
Standardmäßig teilen sich alle Verbraucher denselben Verbraucherprozess. Manchmal möchten wir jedoch einige Warteschlangen in getrennten Verarbeitungsprozessen verarbeiten, z. B. langsame Geschäftsvorgänge in einer Gruppe von Prozessen und schnelle Geschäftsvorfälle in einer anderen Gruppe. Hierfür können Verbraucher in zwei Verzeichnissen unterteilt werden, z. B. `app_path() . '/queue/redis/fast'` und `app_path() . '/queue/redis/slow'` (beachten Sie, dass der Namensraum der Verbrauchsklasse entsprechend angepasst werden muss). Die Konfiguration sieht dann wie folgt aus:
```php
return [
    ...Andere Konfigurationen hier ausgelassen...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Verzeichnis für Verbraucherklassen
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Verzeichnis für Verbraucherklassen
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

Durch die Kategorisierung der Verbraucherklassen und die entsprechende Konfiguration können wir leicht unterschiedliche Verarbeitungsprozesse für verschiedene Verbraucher festlegen.
## Mehrfach Redis-Konfiguration
#### Konfiguration
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // Passwort, Zeichenfolgentyp, optionaler Parameter
            'db' => 0,            // Datenbank
            'max_attempts'  => 5, // Nach dem Verbrauchsfehler Wiederholungsversuche
            'retry_seconds' => 5, // Wiederholungsintervall in Sekunden
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // Passwort, Zeichenfolgentyp, optionaler Parameter
            'db' => 0,             // Datenbank
            'max_attempts'  => 5, // Nach dem Verbrauchsfehler Wiederholungsversuche
            'retry_seconds' => 5, // Wiederholungsintervall in Sekunden
        ]
    ],
];
```

Beachten Sie, dass der Konfiguration ein zusätzliches `other` als Schlüssel für die Redis-Konfiguration hinzugefügt wurde.

#### Mehrfaches Redis Message Publishing
```php
// Nachrichten an die Warteschlange mit dem Schlüssel `default` senden
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// Entsprechend
Client::send($queue, $data);
Redis::send($queue, $data);

// Nachrichten an die Warteschlange mit dem Schlüssel `other` senden
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### Mehrfacher Redis-Verbrauch
Verbrauchen Sie Nachrichten aus der Warteschlange, die mit dem Schlüssel `other` in der Konfiguration definiert ist.
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Zu konsumierende Warteschlangenname
    public $queue = 'send-mail';

    // === Hier wird `other` eingestellt, um die Warteschlange zu konsumieren, die in der Konfiguration mit `other` als Schlüssel definiert ist ===
    public $connection = 'other';

    // Konsumieren
    public function consume($data)
    {
        // Keine Deserialisierung erforderlich
        var_export($data);
    }
}
```

## Häufig gestellte Fragen
**Warum erhalte ich den Fehler "Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)"?**

Dieser Fehler tritt nur in der asynchronen Sendeschnittstelle `Client::send()` auf. Bei asynchronem Senden wird die Nachricht zunächst im lokalen Speicher gespeichert und bei Leerlauf des Prozesses an Redis gesendet. Wenn die Geschwindigkeit, mit der Redis die Nachrichten empfängt, langsamer ist als die Produktionsgeschwindigkeit der Nachrichten, oder wenn der Prozess kontinuierlich mit anderen Geschäften beschäftigt ist und nicht genügend Zeit hat, um die im Speicher befindlichen Nachrichten mit Redis zu synchronisieren, kann es zu einer Verstopfung kommen. Wenn eine Verstopfung länger als 600 Sekunden besteht, wird dieser Fehler ausgelöst.

Lösungsansatz: Verwenden Sie die synchrone Sendeschnittstelle `Redis::send()` zum Senden von Nachrichten.
