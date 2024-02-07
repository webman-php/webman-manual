# Redis

Le composant redis de webman utilise par défaut [illuminate/redis](https://github.com/illuminate/redis), qui est la bibliothèque redis de Laravel, et est utilisé de la même manière que Laravel.

Avant d'utiliser `illuminate/redis`, vous devez d'abord installer l'extension redis pour `php-cli`.

> **Remarque**
> Utilisez la commande `php -m | grep redis` pour vérifier si l'extension redis est installée pour `php-cli`. Notez que même si vous avez installé l'extension redis pour `php-fpm`, cela ne signifie pas que vous pouvez l'utiliser dans `php-cli`, car `php-cli` et `php-fpm` sont des applications différentes et peuvent utiliser des fichiers de configuration `php.ini` différents. Utilisez la commande `php --ini` pour vérifier quel fichier de configuration `php.ini` est utilisé par votre `php-cli`.

## Installation

```php
composer require -W illuminate/redis illuminate/events
```
Après l'installation, vous devez redémarrer (reload ne fonctionnera pas).

## Configuration

Le fichier de configuration redis se trouve dans `config/redis.php`.

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

## Exemple

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

## Interface Redis

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

Équivalent à

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

> **Remarque**
> Utilisez l'interface `Redis::select($db)` avec précaution. Comme webman est un framework résident en mémoire, si une requête utilise `Redis::select($db)` pour changer de base de données, cela affectera les requêtes suivantes. Pour plusieurs bases de données, il est recommandé de configurer différentes connexions Redis pour chaque `$db`.

## Utiliser plusieurs connexions Redis

Par exemple, dans le fichier de configuration `config/redis.php`:

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

Par défaut, la connexion utilisée est celle configurée sous `default`, vous pouvez utiliser la méthode `Redis::connection()` pour choisir quelle connexion Redis utiliser.

```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configuration de cluster

Si votre application utilise un cluster de serveurs Redis, vous devez définir ces clusters dans le fichier de configuration Redis, sous la clé `clusters`:

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

Par défaut, le cluster peut être mis en œuvre pour le fractionnement du client sur les nœuds, ce qui vous permet de mettre en place un pool de nœuds et de créer une très grande quantité de mémoire disponible. Gardez à l'esprit que le partage du client ne gère pas les échecs, il est donc principalement utilisé pour obtenir des données mises en cache à partir d'une autre base de données principale. Si vous souhaitez utiliser le cluster Redis natif, vous devez spécifier dans la section `options` du fichier de configuration comme suit:

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

## Commandes en pipeline

Lorsque vous avez besoin d'envoyer de nombreuses commandes au serveur dans une seule opération, il est recommandé d'utiliser des commandes en pipeline. La méthode pipeline accepte une fonction de rappel pour une instance Redis. Vous pouvez envoyer toutes les commandes à l'instance Redis et elles seront toutes exécutées dans une seule opération :

```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
