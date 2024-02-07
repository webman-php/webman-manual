## File d'attente Redis

File d'attente de messages basée sur Redis, prenant en charge le traitement différé des messages.

## Installation
`composer require webman/redis-queue`

## Fichier de configuration
Le fichier de configuration redis est généré automatiquement dans `config/plugin/webman/redis-queue/redis.php`, le contenu est similaire à ceci :
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // mot de passe, paramètre facultatif
            'db' => 0,            // base de données
            'max_attempts'  => 5, // nombre de tentatives de reprise en cas d'échec de la consommation
            'retry_seconds' => 5, // intervalle de réessai, en secondes
        ]
    ],
];
```

### Réessayer en cas d'échec de la consommation
Si la consommation échoue (une exception se produit), alors le message est placé dans une file d'attente différée, en attente de réessai ultérieur. Le nombre de réessais est contrôlé par le paramètre `max_attempts` et l'intervalle de réessai est déterminé par `retry_seconds` et `max_attempts` ensemble. Par exemple, si `max_attempts` est 5 et `retry_seconds` est 10, alors l'intervalle de réessai pour la première tentative est de `1*10` secondes, pour la deuxième tentative c'est de `2*10` secondes, pour la troisième tentative c'est de `3*10` secondes, et ainsi de suite jusqu'à 5 réessais. Si le nombre de tentatives de réessai dépasse le paramètre `max_attempts`, le message est placé dans la file d'attente des échecs sous la clé `{redis-queue}-failed`.

## Livraison de messages (synchronisation)
> **Remarque**
> Nécessite webman/redis >= 1.2.0, dépend de l'extension Redis

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Nom de la file d'attente
        $queue = 'send-mail';
        // Données, peuvent être transmises directement en tant qu'array, pas besoin de sérialisation
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Livraison du message
        Redis::send($queue, $data);
        // Livraison d'un message différé, le message sera traité après 60 secondes
        Redis::send($queue, $data, 60);

        return response('test de file d'attente Redis');
    }

}
```
La réussite de l'envoi avec `Redis::send()` renvoie true, sinon false ou génère une exception.

> **Astuce**
> Il peut y avoir un délai dans la consommation de la file d'attente différée, par exemple si la vitesse de consommation est inférieure à la vitesse de production, cela peut entraîner un retard de consommation. Pour atténuer cela, il est conseillé d'ouvrir plusieurs processus de consommation.

## Livraison de messages (asynchrone)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Nom de la file d'attente
        $queue = 'send-mail';
        // Données, peuvent être transmises directement en tant qu'array, pas besoin de sérialisation
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Livraison du message
        Client::send($queue, $data);
        // Livraison d'un message différé, le message sera traité après 60 secondes
        Client::send($queue, $data, 60);

        return response('test de file d'attente Redis');
    }

}
```
`Client::send()` n'a pas de valeur de retour, il s'agit d'une transmission asynchrone, sans garantie à 100% du transfert du message vers Redis.

> **Astuce**
> Le fonctionnement de `Client::send()` consiste à créer une file d'attente en mémoire locale et à transmettre les messages de manière asynchrone à Redis (la vitesse de transmission est rapide, environ 10 000 messages par seconde). Si le processus redémarre et que tous les messages de la file d'attente en mémoire locale n'ont pas été transmis, cela entraînera une perte de messages. `Client::send()` est donc adapté à la transmission de messages non critiques.

> **Astuce**
> `Client::send()` est asynchrone et ne peut être utilisé que dans l'environnement d'exécution de workerman. Pour les scripts en ligne de commande, veuillez utiliser l'interface synchrone `Redis::send()`.

## Livraison de messages dans un autre projet
Parfois, il est nécessaire de livrer des messages dans un autre projet et il n'est pas possible d'utiliser `webman/redis-queue`. Dans ce cas, vous pouvez vous référer à la fonction suivante pour livrer des messages dans la file d'attente.

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

Dans cette fonction, le paramètre `$redis` représente une instance de Redis. Par exemple, l'utilisation de l'extension Redis ressemblerait à ceci :
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
````

## Consommation
Le fichier de configuration du processus de consommation se trouve dans `config/plugin/webman/redis-queue/process.php`.
Le répertoire du consommateur se trouve sous `app/queue/redis/`.

Exécuter la commande `php webman redis-queue:consumer my-send-mail` générera le fichier `app/queue/redis/MyMailSend.php`.

> **Astuce**
> Si la commande n'existe pas, vous pouvez également le générer manuellement.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Nom de la file d'attente à consommer
    public $queue = 'send-mail';

    // Nom de la connexion, correspondant à la connexion dans le fichier `plugin/webman/redis-queue/redis.php`
    public $connection = 'default';

    // Consommation
    public function consume($data)
    {
        // Pas besoin de désérialiser
        var_export($data); // Sortie ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **Remarque**
> Tout processus de consommation qui ne génère ni exception ni erreur est considéré comme une consommation réussie. Sinon, la consommation échoue et le message est placé dans la file d'attente de réessai.
> Redis-queue n'a pas de mécanisme d'acknowledgement (confirmation de réception), vous pouvez le considérer comme un ack automatique (en l'absence d'exception ou d'erreur). Si vous souhaitez marquer un message comme n'ayant pas été consommé avec succès pendant la consommation, vous pouvez jeter manuellement une exception pour remettre le message dans la file d'attente de réessai. Cela équivaut en réalité au mécanisme d'acknowledgement.
> Le processus de consommation prend en charge la consommation multi-serveurs multi-processus et le même message **ne sera pas** consommé à plusieurs reprises. Les messages consommés sont automatiquement supprimés de la file d'attente et n'ont pas besoin d'être supprimés manuellement.
> Les processus de consommation peuvent consommer simultanément plusieurs files d'attente différentes, l'ajout de nouvelles files d'attente ne nécessite pas de modification de la configuration dans `process.php`, il suffit d'ajouter une classe `Consumer` correspondante dans `app/queue/redis` en spécifiant la propriété de classe `$queue` pour indiquer la file d'attente à consommer.

> **Astuce**
> Les utilisateurs de Windows doivent exécuter `php windows.php` pour démarrer webman, sinon les processus de consommation ne seront pas démarrés.

## Configuration de processus de consommation différente pour différentes files d'attente
Par défaut, tous les consommateurs partagent le même processus de consommation. Cependant, parfois nous devons séparer la consommation de certaines files d'attente, par exemple, pour mettre les affaires lentes dans un groupe de processus de consommation et les affaires rapides dans un autre groupe. Pour ce faire, nous pouvons diviser les consommateurs en deux répertoires, par exemple `app_path() . '/queue/redis/fast'` et `app_path() . '/queue/redis/slow'` (notez que l'espace de noms de la classe de consommation doit être modifié en conséquence), puis configurer comme suit :
```php
return [
    ... autres configurations ici...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Répertoire de classe de consommateur
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Répertoire de classe de consommateur
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

Grâce à la classification des répertoires et aux configurations appropriées, nous pouvons facilement définir des processus de consommation différents pour différents consommateurs.
## Configuration multi-redis
#### Configuration
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // Password, string type, optional parameter
            'db' => 0,            // Database
            'max_attempts'  => 5, // Retry times after consumption failure
            'retry_seconds' => 5, // Retry interval, in seconds
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // Password, string type, optional parameter
            'db' => 0,             // Database
            'max_attempts'  => 5, // Retry times after consumption failure
            'retry_seconds' => 5, // Retry interval, in seconds
        ]
    ],
];
```

Note that a `other` redis configuration has been added to the configuration.

#### Multi-redis message delivery
```php
// Deliver messages to the queue with key 'default'
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// Same as
Client::send($queue, $data);
Redis::send($queue, $data);

// Deliver messages to the queue with key 'other'
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### Multi-redis consumption
Consume messages from the queue with key `other` in the consumption configuration
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Queue name to consume
    public $queue = 'send-mail';

    // === Set this to 'other' to consume from the queue with key 'other' in the consumption configuration ===
    public $connection = 'other';

    // Consume
    public function consume($data)
    {
        // No need for deserialization
        var_export($data);
    }
}
```

## Frequently Asked Questions

**Why do I get the error `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`**

This error only exists in the asynchronous delivery interface `Client::send()`. Asynchronous delivery first saves the message in local memory and sends it to redis when the process is idle. If the redis receiving speed is slower than the message production speed, or if the process is busy with other tasks and does not have enough time to synchronize the memory messages to redis, it can cause message congestion. If there is a message congestion for more than 600 seconds, this error will be triggered.

Solution: Use the synchronous delivery interface `Redis::send()` for message delivery.
