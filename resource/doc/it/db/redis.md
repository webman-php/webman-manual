# Redis

Il componente redis di webman utilizza di default [illuminate/redis](https://github.com/illuminate/redis), che è la libreria redis di Laravel e si utilizza allo stesso modo di Laravel.

Prima di utilizzare `illuminate/redis`, è necessario installare l'estensione redis per `php-cli`.

> **Nota**
> Utilizzare il comando `php -m | grep redis` per verificare se l'estensione redis è installata per `php-cli`. Tieni presente che anche se hai installato l'estensione redis per `php-fpm`, non significa che sia disponibile per `php-cli`, in quanto `php-cli` e `php-fpm` sono due applicazioni diverse che potrebbero utilizzare configurazioni `php.ini` diverse. Utilizza il comando `php --ini` per verificare quale file di configurazione `php.ini` sta utilizzando `php-cli`.

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
Equivalente a
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
> Attenzione all'uso dell'interfaccia `Redis::select($db)`, poiché webman è un framework in memoria permanente, se una richiesta usa `Redis::select($db)` per cambiare il database, influenzerà le richieste successive. Per database multipli, si consiglia di configurare connessioni Redis diverse per database diversi.

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
];
```
Di default viene utilizzata la connessione configurata in `default`, è possibile utilizzare il metodo `Redis::connection()` per selezionare quale connessione Redis utilizzare.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configurazione del cluster
Se l'applicazione utilizza un cluster di server Redis, è necessario definire tali cluster nel file di configurazione Redis come segue:
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
Per impostazione predefinita, il cluster può suddividere i client sui nodi, consentendo di realizzare un pool di nodi e creare una grande quantità di memoria disponibile. Tuttavia, è importante notare che la condivisione dei client non gestisce le situazioni di fallimento; quindi, questa funzionalità è principalmente adatta per ottenere dati di cache da un'altra base di dati principale. Se si desidera utilizzare il cluster nativo di Redis, è necessario specificarlo nel file di configurazione nel campo opzioni come segue:
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

## Comandi della pipeline
Quando è necessario inviare molti comandi al server in un'unica operazione, si consiglia di utilizzare i comandi di pipeline. Il metodo pipeline accetta una chiusura di istanza Redis. È quindi possibile inviare tutti i comandi all'istanza Redis e verranno eseguiti tutti in un'unica operazione:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
