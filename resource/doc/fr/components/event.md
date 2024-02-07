# Gestion des événements

`webman/event` fournit un mécanisme d'événement sophistiqué qui permet d'exécuter certaines logiques métier sans modifier le code, réalisant ainsi le découplage entre les modules métier. Un scénario typique consiste à publier un événement personnalisé tel que `user.register` chaque fois qu'un nouvel utilisateur s'inscrit, afin que chaque module puisse recevoir cet événement et exécuter la logique métier correspondante.

## Installation
`composer require webman/event`

## Abonnement aux événements
L'abonnement aux événements se fait de manière uniforme via le fichier `config/event.php`:

```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...autres fonctions de traitement d'événements...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...autres fonctions de traitement d'événements...
    ]
];
```

**Remarque :**
- `user.register`, `user.logout`, etc. sont des noms d'événements de type chaîne, il est recommandé d'utiliser des mots en minuscules et de les séparer par des points (`.`).
- Un événement peut avoir plusieurs fonctions de traitement d'événements, qui seront appelées dans l'ordre configuré.

## Fonctions de traitement des événements
Les fonctions de traitement des événements peuvent être n'importe quelle méthode de classe, fonction, fonction de fermeture, etc.
Par exemple, créez une classe de traitement d'événements `app/event/User.php` (créez le répertoire s'il n'existe pas) :

```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## Publication d'événements
Utilisez `Event::emit($event_name, $data)` pour publier un événement, par exemple :

```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **Remarque :**
> Le paramètre `$data` de `Event::emit($event_name, $data)` peut être n'importe quel type de données, telles qu'un tableau, une instance de classe, une chaîne, etc.

## Écoute d'événements par joker
L'écoute enregistrée par joker vous permet de gérer plusieurs événements avec un même écouteur. Par exemple, dans `config/event.php` :

```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```

Nous pouvons obtenir le nom d'événement spécifique via le 2ème paramètre de la fonction de traitement de l'événement `$event_data` :

```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // Nom spécifique de l'événement, tel que user.register, user.logout, etc.
        var_export($user);
    }
}
```

## Arrêt de la transmission d'événements
Lorsque nous retournons `false` dans une fonction de traitement d'événements, la transmission de cet événement sera stoppée.

## Fonction de traitement des événements par fonction de fermeture
Une fonction de traitement d'événements peut être une méthode de classe ou une fonction de fermeture, par exemple :

```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## Vérification des événements et des écouteurs
Utilisez la commande `php webman event:list` pour afficher tous les événements et écouteurs configurés dans le projet.

## Remarques
La gestion des événements n'est pas asynchrone. Elle n'est pas adaptée pour traiter les tâches métier lentes, qui devraient être gérées par une file d'attente de messages, telle que [webman/redis-queue](https://www.workerman.net/plugin/12).
