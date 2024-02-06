# Gestion des sessions

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
        return response('Bonjour ' . $session->get('name'));
    }
}
```

Obtenez une instance de `Workerman\Protocols\Http\Session` en utilisant `$request->session();` et utilisez les méthodes de l'instance pour ajouter, modifier et supprimer des données de session.

> Remarque : Lorsque l'objet session est détruit, les données de session sont automatiquement enregistrées. Ne conservez pas l'objet retourné par `$request->session()` dans un tableau global ou en tant que membre de classe, car cela pourrait empêcher la session d'être enregistrée.

## Obtenir toutes les données de session
```php
$session = $request->session();
$all = $session->all();
```
Il retourne un tableau. S'il n'y a pas de données de session, un tableau vide est retourné.

## Obtenir une valeur spécifique de la session
```php
$session = $request->session();
$name = $session->get('name');
```
Si les données n'existent pas, cela renvoie null.

Vous pouvez également passer une valeur par défaut en deuxième paramètre à la méthode get. S'il n'y a pas de valeur correspondante dans le tableau de session, la valeur par défaut est renvoyée. Par exemple :
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Stocker une session
Utilisez la méthode `set` pour stocker des données spécifiques.
```php
$session = $request->session();
$session->set('name', 'tom');
```
La méthode `set` n'a pas de valeur de retour, les données de session sont automatiquement sauvegardées lorsque l'objet de session est détruit.

Lors du stockage de plusieurs valeurs, utilisez la méthode `put`.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
De même, la méthode `put` n'a pas de valeur de retour.

## Supprimer des données de session
Utilisez la méthode `forget` pour supprimer des données spécifiques ou plusieurs données de session.
```php
$session = $request->session();
// Supprimer une entrée
$session->forget('name');
// Supprimer plusieurs entrées
$session->forget(['name', 'age']);
```

De plus, le système propose également la méthode `delete`, qui diffère de la méthode `forget` car elle ne peut supprimer qu'une entrée.
```php
$session = $request->session();
// Équivalent à $session->forget('name');
$session->delete('name');
```

## Obtenir et supprimer une valeur de session spécifique
```php
$session = $request->session();
$name = $session->pull('name');
```
Cela équivaut à :
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Si la session correspondante n'existe pas, cela renvoie null.

## Supprimer toutes les données de session
```php
$request->session()->flush();
```
Il n'y a pas de valeur de retour, les données de session sont automatiquement supprimées du stockage lorsque l'objet de session est détruit.

## Vérifier si des données de session correspondantes existent
```php
$session = $request->session();
$has = $session->has('name');
```
Lorsque la session correspondante n'existe pas ou que sa valeur est null, cela renvoie false, sinon cela renvoie true.

```
$session = $request->session();
$has = $session->exists('name');
```
Ce code est également utilisé pour vérifier si des données de session correspondent, la différence est que lorsque la valeur de la session correspondante est null, cela renvoie également true.

## Fonction d'aide `session()`
> Nouveauté le 09-12-2020

webman fournit la fonction d'aide `session()` qui permet d'accomplir les mêmes tâches.
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

// Définir des valeurs de session
session(['key1'=>'value1', 'key2' => 'value2']);
// Équivalent à
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Équivalent à
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## Fichier de configuration
Le fichier de configuration de session se trouve dans `config/session.php`, qui ressemble à ceci :
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class ou RedisSessionHandler::class ou RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Si le gestionnaire est FileSessionHandler::class, la valeur est fichier,
    // Si le gestionnaire est RedisSessionHandler::class, la valeur est redis
    // Si le gestionnaire est RedisClusterSessionHandler::class, la valeur est redis_cluster (cluster de Redis)
    'type'    => 'file',

    // Chaque gestionnaire utilise sa propre configuration
    'config' => [
        // Configuration pour le type file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuration pour le type redis
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

    'session_name' => 'PHPSID', // Nom du cookie stockant session_id
    
    // === Les configurations suivantes nécessitent webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Actualisation automatique de la session, par défaut désactivé
    'lifetime' => 7*24*60*60,          // Durée de vie de la session
    'cookie_lifetime' => 365*24*60*60, // Durée de vie du cookie stockant session_id
    'cookie_path' => '/',              // Chemin du cookie stockant session_id
    'domain' => '',                    // Domaine du cookie stockant session_id
    'http_only' => true,               // Activer httpOnly, par défaut activé
    'secure' => false,                 // Activer la session uniquement en https, par défaut désactivé
    'same_site' => '',                 // Utilisé pour prévenir les attaques CSRF et le suivi des utilisateurs, les valeurs possibles sont strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilité de nettoyage de la session
];
```

> **Remarque** 
> À partir de la version 1.4.0, webman a modifié l'espace de noms de SessionHandler. Au lieu de
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> changer à  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## Configuration de la durée de vie
Lorsque webman-framework < 1.3.14, la durée de vie de la session dans webman doit être configurée dans `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Supposons que la durée de vie soit de 1440 secondes, la configuration serait la suivante
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Remarque**
> Vous pouvez utiliser la commande `php --ini` pour trouver l'emplacement de `php.ini`.
