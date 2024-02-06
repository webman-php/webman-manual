# Processus personnalisés

Dans webman, vous pouvez personnaliser les processus d'écoute ou les processus de la même manière que dans workerman.

> **Remarque**
> Les utilisateurs de Windows doivent utiliser `php windows.php` pour démarrer webman afin de lancer des processus personnalisés.

## Service HTTP personnalisé
Parfois, vous pouvez avoir un besoin spécifique qui nécessite une modification du code interne du service HTTP de webman. Vous pouvez alors utiliser des processus personnalisés pour le réaliser.

Par exemple, créez app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Rewrite the methods in Webman\App here
}
```

Ajoutez la configuration suivante à `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Autres configurations omises...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Nombre de processus
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Configuration de la classe de requête
            'logger' => \support\Log::channel('default'), // Instance de journal
            'app_path' => app_path(), // Emplacement du répertoire app
            'public_path' => public_path() // Emplacement du répertoire public
        ]
    ]
];
```

> **Conseil**
> Pour désactiver le processus HTTP intégré de webman, il suffit de définir `listen=>''` dans config/server.php.

## Exemple d'écoute WebSocket personnalisée

Créez `app/Pusher.php`
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
> Remarque : Toutes les propriétés onXXX sont publiques.

Ajoutez la configuration suivante à `config/process.php`
```php
return [
    // ... Autres configurations de processus omises...

    // websocket_test est le nom du processus
    'websocket_test' => [
        // Spécifie la classe du processus, c'est-à-dire la classe Pusher définie ci-dessus
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Exemple de processus non d'écoute personnalisé
Créez `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Vérifiez la base de données toutes les 10 secondes pour les nouveaux utilisateurs enregistrés
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Ajoutez la configuration suivante à `config/process.php`
```php
return [
    // ... Autres configurations de processus omises...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Remarque : Si listen est omis, aucun port ne sera écouté ; si count est omis, le nombre de processus par défaut sera de 1.

## Explication des fichiers de configuration

Voici un exemple de définition complète d'un processus :
```php
return [
    // ... 

    // websocket_test est le nom du processus
    'websocket_test' => [
        // Spécifiez ici la classe du processus
        'handler' => app\Pusher::class,
        // Protocole d'écoute, IP et port (optionnel)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Nombre de processus (optionnel, par défaut 1)
        'count'   => 2,
        // Utilisateur d'exécution du processus (optionnel, utilisateur actuel par défaut)
        'user'    => '',
        // Groupe d'exécution du processus (optionnel, groupe actuel par défaut)
        'group'   => '',
        // Le processus actuel prend-il en charge le rechargement ? (optionnel, par défaut vrai)
        'reloadable' => true,
        // Activation ou non de reusePort (optionnel, cette option nécessite php>=7.0, par défaut à vrai)
        'reusePort'  => true,
        // transport (optionnel, spécifier ssl lorsqu'il est activé, par défaut tcp)
        'transport'  => 'tcp',
        // context (optionnel, lorsque le transport est ssl, spécifiez le chemin du certificat)
        'context'    => [], 
        // Paramètres du constructeur de la classe du processus, ici pour la classe process\Pusher::class (optionnel)
        'constructor' => [],
    ],
];
```

## Conclusion
La personnalisation des processus de webman est en fait une simple encapsulation de workerman. Elle sépare la configuration de la logique métier, et met en œuvre les rappels `onXXX` de workerman via les méthodes de classe, respectant ainsi complètement les autres utilisations de workerman.
