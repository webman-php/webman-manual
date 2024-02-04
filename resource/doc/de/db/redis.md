# Redis

Die webman-Redis-Komponente verwendet standardmäßig [illuminate/redis](https://github.com/illuminate/redis), was die Redis-Bibliothek von Laravel ist. Die Verwendung ist ähnlich wie bei Laravel.

Bevor Sie `illuminate/redis` verwenden, müssen Sie die Redis-Erweiterung für `php-cli` installieren.

> **Achtung**
> Verwenden Sie den Befehl `php -m | grep redis`, um zu prüfen, ob die Redis-Erweiterung für `php-cli` installiert ist. Beachten Sie, dass auch wenn Sie die Redis-Erweiterung für `php-fpm` installiert haben, dies nicht bedeutet, dass Sie sie für `php-cli` verwenden können, da `php-cli` und `php-fpm` unterschiedliche Anwendungen sind und möglicherweise unterschiedliche `php.ini`-Konfigurationen verwenden. Verwenden Sie den Befehl `php --ini`, um herauszufinden, welche `php.ini`-Konfigurationsdatei von Ihrem `php-cli` verwendet wird.

## Installation

```php
composer require -W illuminate/redis illuminate/events
```

Nach der Installation muss neu gestartet werden (reload ist unwirksam).

## Konfiguration
Die Redis-Konfigurationsdatei befindet sich in `config/redis.php`.
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

## Redis API
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
Entspricht
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

> **Achtung**
> Verwenden Sie die `Redis::select($db)`-Schnittstelle mit Vorsicht, da webman ein im Speicher verbleibendes Framework ist. Wenn eine Anfrage `Redis::select($db)` verwendet, um die Datenbank zu wechseln, wird dies sich auf nachfolgende Anfragen auswirken. Für den Einsatz von mehreren Datenbanken wird empfohlen, verschiedene `$db`-Einstellungen in unterschiedlichen Redis-Verbindungskonfigurationen zu verwenden.

## Verwendung mehrerer Redis-Verbindungen
Zum Beispiel in der Konfigurationsdatei `config/redis.php`
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

]
```
Standardmäßig wird die Verbindung verwendet, die in `default` konfiguriert ist. Sie können die Methode `Redis::connection()` verwenden, um festzulegen, welche Redis-Verbindung verwendet werden soll.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Cluster-Konfiguration
Wenn Ihre Anwendung einen Redis-Server-Cluster verwendet, sollten Sie in der Redis-Konfigurationsdatei mit dem Schlüssel `clusters` diese Cluster definieren:
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

Standardmäßig kann der Cluster das Client-Sharding auf den Knoten implementieren, um einen Pool von Knoten zu erstellen und eine große Menge an verfügbarem Speicher zu ermöglichen. Beachten Sie, dass das Client-Sharing nicht mit Fehlern umgehen wird; daher wird diese Funktion hauptsächlich für das Abrufen von Cachedaten aus einer anderen Hauptdatenbank verwendet. Wenn Sie den nativen Redis-Cluster verwenden möchten, müssen Sie im Schlüssel `options` in der Konfigurationsdatei Folgendes angeben:

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
Wenn Sie viele Befehle an den Server senden müssen, wird empfohlen, den Pipeline-Befehl zu verwenden. Die Methode `pipeline` akzeptiert eine Closure eines Redis-Instanz. Sie können alle Befehle an die Redis-Instanz senden, und sie werden alle in einer Aktion ausgeführt:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
