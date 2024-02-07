# Redis

Die Redis-Komponente von webman verwendet standardmäßig [illuminate/redis](https://github.com/illuminate/redis), was die Redis-Bibliothek von Laravel ist, und wird genauso verwendet wie in Laravel.

Bevor Sie `illuminate/redis` verwenden können, muss die Redis-Erweiterung für `php-cli` installiert sein.

> **Hinweis**
> Verwenden Sie den Befehl `php -m | grep redis`, um zu überprüfen, ob die Redis-Erweiterung für `php-cli` installiert ist. Beachten Sie: Selbst wenn Sie die Redis-Erweiterung für `php-fpm` installiert haben, bedeutet dies nicht, dass Sie sie für `php-cli` verwenden können, da `php-cli` und `php-fpm` unterschiedliche Anwendungen sind und möglicherweise unterschiedliche `php.ini`-Konfigurationen verwenden. Verwenden Sie den Befehl `php --ini`, um zu überprüfen, welche `php.ini`-Konfigurationsdatei von Ihrem `php-cli` verwendet wird.

## Installation

```php
composer require -W illuminate/redis illuminate/events
```

Nach der Installation ist ein Restart erforderlich (ein Reload funktioniert nicht).

## Konfiguration
Die Redis-Konfigurationsdatei befindet sich unter `config/redis.php`.
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## Beispiel
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Redis-API
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
Entsprechend:
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **Hinweis**
> Verwenden Sie die `Redis::select($db)`-Schnittstelle vorsichtig, da webman ein permanentes Framework im Speicher ist. Wenn eine Anfrage `Redis::select($db)` zur Umstellung der Datenbank verwendet, wird dies sich auf nachfolgende Anfragen auswirken. Für den Gebrauch von mehreren Datenbanken wird empfohlen, unterschiedliche `$db`-Konfigurationen für verschiedene Redis-Verbindungen vorzunehmen.

## Verwendung mehrerer Redis-Verbindungen
Beispielsweise in der Konfigurationsdatei `config/redis.php`:
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],
];
```
Standardmäßig wird die in `default` konfigurierte Verbindung verwendet. Sie können die Methode `Redis::connection()` verwenden, um zu wählen, welche Redis-Verbindung verwendet werden soll.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Cluster-Konfiguration
Wenn Ihre Anwendung einen Redis-Server-Cluster verwendet, sollten Sie in der Redis-Konfigurationsdatei mithilfe des `clusters`-Keys diese Cluster definieren:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

Standardmäßig kann der Cluster auf dem Knoten das Client-Sharding ermöglichen, um einen Pool von Knoten zu implementieren und eine große Menge verfügbarer Speicher zu schaffen. Es ist zu beachten, dass das Client-Sharing nicht mit fehlgeschlagenen Vorgängen umgeht und hauptsächlich für das Abrufen von Cache-Daten aus einer anderen primären Datenbank verwendet wird. Wenn Sie den nativen Redis-Cluster verwenden möchten, müssen Sie dies im `options`-Key der Konfigurationsdatei wie folgt angeben:
```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Pipeline-Befehl
Wenn Sie viele Befehle an den Server senden müssen, wird empfohlen, den Pipeline-Befehl zu verwenden. Die Methode `pipeline` akzeptiert eine Closure für eine Redis-Instanz. Sie können alle Befehle an die Redis-Instanz senden, und sie werden alle in einem Vorgang ausgeführt:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
