# Processus personnalisés

Dans webman, vous pouvez personnaliser les processus d'écoute ou de service de la même manière que dans workerman.

> **Remarque**
> Les utilisateurs de Windows doivent démarrer webman avec `php windows.php` pour lancer des processus personnalisés.

## Service HTTP personnalisé
Parfois, vous pouvez avoir un besoin spécifique de modifier le code source du service HTTP de webman. Dans ce cas, vous pouvez utiliser un processus personnalisé pour le faire.

Par exemple, créez app\Server.php :

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Ici, vous réécrivez les méthodes de Webman\App
}
```

Ajoutez la configuration suivante dans `config/process.php` :

```php
use Workerman\Worker;

return [
    // ... autres configurations omises...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Nombre de processus
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Définir la classe de requête
            'logger' => \support\Log::channel('default'), // Instance de journalisation
            'app_path' => app_path(), // Emplacement du répertoire app
            'public_path' => public_path() // Emplacement du répertoire public
        ]
    ]
];
```

> **Remarque**
> Si vous souhaitez désactiver le processus HTTP intégré de webman, il vous suffit de définir `listen=>''` dans le fichier `config/server.php`.

## Exemple de surveillance WebSocket personnalisée
Créez `app/Pusher.php` :

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
> Remarque : Toutes les propriétés onXXX sont publiques

Ajoutez la configuration suivante dans `config/process.php` :

```php
return [
    // ... autres configurations de processus omises... 

    // websocket_test est le nom du processus
    'websocket_test' => [
        // Vous spécifiez ici la classe du processus, c'est-à-dire la classe Pusher définie ci-dessus
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Exemple de processus non surveillé personnalisé
Créez `app/TaskTest.php` :

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Vérifiez la base de données toutes les 10 secondes pour voir s'il y a de nouveaux utilisateurs enregistrés
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Ajoutez la configuration suivante dans `config/process.php` :

```php
return [
    // ... autres configurations de processus omises...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```
> Remarque : Si la surveillance est omise, aucun port ne sera surveillé, et si le nombre de processus est omis, le nombre par défaut sera 1.

## Explication de la configuration

La définition complète d'un processus est la suivante :

```php
return [
    // ... 

    // websocket_test est le nom du processus
    'websocket_test' => [
        // Vous spécifiez ici la classe du processus
        'handler' => app\Pusher::class,
        // Protocole, IP et port surveillés (optionnel)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Nombre de processus (optionnel, par défaut 1)
        'count'   => 2,
        // Utilisateur de fonctionnement du processus (optionnel, utilisateur actuel par défaut)
        'user'    => '',
        // Groupe de fonctionnement du processus (optionnel, groupe actuel par défaut)
        'group'   => '',
        // Le processus prend-il en charge le rechargement ? (optionnel, par défaut vrai)
        'reloadable' => true,
        // Activation de reusePort (optionnel, nécessite PHP >= 7.0 par défaut à vrai)
        'reusePort'  => true,
        // transport (optionnel, définir sur ssl lorsque SSL est requis, par défaut à tcp)
        'transport'  => 'tcp',
        // context (optionnel, lorsque transport est ssl, le chemin du certificat doit être transmis)
        'context'    => [], 
        // Arguments du constructeur de classe de processus. Cet exemple montre les arguments du constructeur de la classe process\Pusher::class (optionnel)
        'constructor' => [],
    ],
];
```

## Conclusion
Les processus personnalisés de webman sont en fait une simple encapsulation de workerman. Il sépare la configuration de la logique métier et implémente les rappels `onXXX` de workerman via les méthodes de classe, tout en étant complètement identique à workerman pour les autres utilisations.
