# Gestion de session

## Exemple
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

Obtenez l'instance de `Workerman\Protocols\Http\Session` avec `$request->session();`, puis utilisez les méthodes de l'objet pour ajouter, modifier et supprimer les données de session.

> Remarque : L'objet de session sauvegardera automatiquement les données de session lorsqu'il sera détruit. Par conséquent, ne conservez pas l'objet retourné par `$request->session()` dans un tableau global ou en tant que membre de classe, ce qui empêcherait la sauvegarde de la session.

## Obtenir toutes les données de session
```php
$session = $request->session();
$all = $session->all();
```
Renvoie un tableau. S'il n'y a pas de données de session, un tableau vide est renvoyé.

## Obtenir une valeur de session spécifique
```php
$session = $request->session();
$name = $session->get('name');
```
Renvoie null si les données n'existent pas.

Vous pouvez également fournir une valeur par défaut en passant un deuxième argument à la méthode get. Si aucune valeur correspondante n'est trouvée dans le tableau de session, la valeur par défaut est renvoyée. Par exemple :
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Stocker une session
Utilisez la méthode `set` pour stocker des données.
```php
$session = $request->session();
$session->set('name', 'tom');
```
La méthode `set` ne renvoie rien. Les données de session seront automatiquement sauvegardées lorsque l'objet de session est détruit.

Utilisez la méthode `put` pour stocker plusieurs valeurs à la fois.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
De même, cette méthode ne renvoie rien.

## Supprimer des données de session
Utilisez la méthode `forget` pour supprimer une ou plusieurs données de session.
```php
$session = $request->session();
// Supprimer une valeur
$session->forget('name');
// Supprimer plusieurs valeurs
$session->forget(['name', 'age']);
```

De plus, le système propose la méthode `delete`, qui diffère de la méthode `forget` en ce qu'elle ne peut supprimer qu'une seule valeur.
```php
$session = $request->session();
// Équivaut à $session->forget('name');
$session->delete('name');
```

## Obtenir et supprimer une valeur de session spécifique
```php
$session = $request->session();
$name = $session->pull('name');
```
Cela a le même effet que le code suivant :
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Renvoie null si la session correspondante n'existe pas.

## Supprimer toutes les données de session
```php
$request->session()->flush();
```
Cette méthode ne renvoie rien. Les données de session seront automatiquement supprimées de la sauvegarde lorsque l'objet de session est détruit.

## Vérifier si des données de session spécifiques existent
```php
$session = $request->session();
$has = $session->has('name');
```
Lorsque la session correspondante n'existe pas ou que sa valeur est null, false est renvoyé, sinon true est renvoyé.

```php
$session = $request->session();
$has = $session->exists('name');
```
Ce code est également utilisé pour vérifier si des données de session existent. La différence est que lorsque la valeur correspondante de la session est null, true est également renvoyé.

## Fonction helper session()
> Ajouté le 09-12-2020

webman propose la fonction helper `session()` pour accomplir les mêmes fonctionnalités.
```php
// Obtenir une instance de session
$session = session();
// Équivalent à
$session = $request->session();

// Obtenir une valeur spécifique
$value = session('key', 'default');
// Équivalent à
$value = session()->get('key', 'default');
// Équivalent à
$value = $request->session()->get('key', 'default');

// Affecter une valeur à la session
session(['key1'=>'value1', 'key2' => 'value2']);
// Équivalent à
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Équivalent à
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## Fichier de configuration
Le fichier de configuration de la session se trouve dans `config/session.php` et a le contenu suivant :
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class ou RedisSessionHandler::class ou RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // type est égal à file lorsque handler est FileSessionHandler::class,
    // type est égal à redis lorsque handler est RedisSessionHandler::class,
    // type est égal à redis_cluster lorsque handler est RedisClusterSessionHandler::class, c'est-à-dire un cluster Redis.
    'type'    => 'file',

    // Différents gestionnaires utilisent des configurations différentes
    'config' => [
        // Configuration lorsque le type est file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuration lorsque le type est redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Nom du cookie pour stocker l'ID de session
    
    // === Les configurations suivantes nécessitent webman-framework>=1.3.14 et workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Actualiser automatiquement la session, désactivé par défaut
    'lifetime' => 7*24*60*60,          // Durée de vie de la session
    'cookie_lifetime' => 365*24*60*60, // Durée de vie du cookie stockant l'ID de session
    'cookie_path' => '/',              // Chemin du cookie stockant l'ID de session
    'domain' => '',                    // Domaine du cookie stockant l'ID de session
    'http_only' => true,               // Activer uniquement HTTP, activé par défaut
    'secure' => false,                 // Activer la session uniquement en HTTPS, désactivé par défaut
    'same_site' => '',                 // Pour prévenir les attaques CSRF et le suivi des utilisateurs, valeurs possibles : strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilité de collecte de session
];
```

> **Remarque** 
> À partir de la version 1.4.0, webman a modifié l'espace de noms de SessionHandler, passant de
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> à
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  


## Configuration de la durée de validité
Pour webman-framework < 1.3.14, la durée de validité de la session dans webman doit être configurée dans `php.ini`.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```
Supposons que la durée de validité soit de 1440 secondes, la configuration est la suivante :
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Remarque**
> Vous pouvez utiliser la commande `php --ini` pour trouver l'emplacement de votre `php.ini`.
