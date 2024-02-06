# Redis

Il componente redis di webman utilizza di default [illuminate/redis](https://github.com/illuminate/redis), che è la libreria redis di Laravel e si utilizza allo stesso modo.

È necessario installare l'estensione redis per `php-cli` prima di utilizzare `illuminate/redis`.

> **Nota**
> Utilizzare il comando `php -m | grep redis` per verificare se l'estensione redis è installata su `php-cli`. Nota: anche se hai installato l'estensione redis su `php-fpm`, non significa che la puoi utilizzare su `php-cli`, poiché `php-cli` e `php-fpm` sono due applicazioni diverse e potrebbero utilizzare configurazioni `php.ini` diverse. Utilizzare il comando `php --ini` per verificare quale file di configurazione `php.ini` sta utilizzando il tuo `php-cli`.

## Installazione

```php
composer require -W illuminate/redis illuminate/events
```

Dopo l'installazione, è necessario riavviare (reload non funziona).

## Configurazione
Il file di configurazione di redis si trova in `config/redis.php`
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

## Esempio
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

## Interfaccia Redis
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
equivalente a
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

> **Nota**
> Fare attenzione all'uso dell'interfaccia `Redis::select($db)`. Poiché webman è un framework in memoria persistente, se una richiesta utilizza `Redis::select($db)` per cambiare il database, influenzerà le richieste successive. Per i database multipli, si consiglia di configurare connessioni Redis diverse per ogni `$db`.

## Utilizzo di più connessioni Redis
Ad esempio, nel file di configurazione `config/redis.php`
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
Di default, viene utilizzata la connessione configurata in `default`, ma è possibile selezionare quale connessione Redis utilizzare utilizzando il metodo `Redis::connection()`.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configurazione del cluster
Se la tua applicazione utilizza un cluster di server Redis, dovresti definire questi cluster nel file di configurazione di Redis usando la chiave clusters:
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

Per impostazione predefinita, il cluster può implementare lo shard del client su nodi, consentendoti di implementare pool di nodi e creare una grande quantità di memoria disponibile. È importante notare che lo shard client non gestirà i fallimenti; pertanto, questa funzionalità è principalmente adatta per memorizzare dati in cache ottenuti da un'altra cache. Se si desidera utilizzare direttamente il cluster Redis nativo, è necessario specificare quanto segue nelle opzioni del file di configurazione:

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

## Comando pipeline
Quando hai bisogno di inviare molti comandi al server in un'operazione, si consiglia di utilizzare il comando pipeline. Il metodo pipeline accetta una callback di un'istanza Redis. Puoi inviare tutti i comandi all'istanza Redis e verranno eseguiti in un'unica operazione:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
