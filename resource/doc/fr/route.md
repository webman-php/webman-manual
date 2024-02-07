## Routage
## Règles de routage par défaut
La règle de routage par défaut de webman est `http://127.0.0.1:8787/{contrôleur}/{action}`.

Le contrôleur par défaut est `app\controller\IndexController`, et l'action par défaut est `index`.

Par exemple, en visitant :
- `http://127.0.0.1:8787`, cela va par défaut visiter la méthode `index` de la classe `app\controller\IndexController`.
- `http://127.0.0.1:8787/foo`, cela va par défaut visiter la méthode `index` de la classe `app\controller\FooController`.
- `http://127.0.0.1:8787/foo/test`, cela va par défaut visiter la méthode `test` de la classe `app\controller\FooController`.
- `http://127.0.0.1:8787/admin/foo/test`, cela va par défaut visiter la méthode `test` de la classe `app\admin\controller\FooController` (consultez [Multi-application](multiapp.md)).

De plus, à partir de webman 1.4, le routage par défaut plus complexe est pris en charge, par exemple :
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

Lorsque vous souhaitez modifier le routage d'une requête, veuillez modifier le fichier de configuration `config/route.php`.

Si vous souhaitez désactiver le routage par défaut, ajoutez la configuration suivante à la dernière ligne du fichier de configuration `config/route.php` :
```php
Route::disableDefaultRoute();
```

## Routage par fermeture
Ajoutez le code de routage suivant dans `config/route.php` :
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Remarque**
> Comme la fonction de fermeture ne fait pas partie d'un contrôleur, les variables `$request->app`, `$request->controller` et `$request->action` seront toutes des chaînes vides.

Lorsque l'adresse visitée est `http://127.0.0.1:8787/test`, cela retournera la chaîne `test`.

> **Remarque**
> Le chemin de routage doit commencer par `/`, par exemple :

```php
// Mauvaise utilisation
Route::any('test', function ($request) {
    return response('test');
});

// Bonne utilisation
Route::any('/test', function ($request) {
    return response('test');
});
```


## Routage de classe
Ajoutez le code de routage suivant dans `config/route.php` :
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Lorsque l'adresse visitée est `http://127.0.0.1:8787/testclass`, cela retournera la valeur de la méthode `test` de la classe `app\controller\IndexController`.


## Paramètres de routage
Si des paramètres sont présents dans le routage, utilisez `{clé}` pour les faire correspondre, et les résultats de la correspondance seront transmis en tant que paramètres des méthodes du contrôleur (à partir du deuxième paramètre), par exemple :
```php
// Corrrespondre à /user/123 ou /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Paramètre reçu : '.$id);
    }
}
```

Plus d'exemples :
```php
// Correspond à /user/123, ne correspond pas à /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Correspond à /user/foobar, ne correspond pas à /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Correspond à /user, /user/123 et /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Correspond à toutes les requêtes options
Route::options('[{path:.+}]', function () {
    return response('');
});
```


## Groupe de routages
Parfois, le routage contient un préfixe important, dans ce cas, nous pouvons utiliser des groupes de routage pour simplifier la définition. Par exemple :
```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Équivalent à :
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Utilisation de groupe imbriqué :
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## Middleware de routage
Nous pouvons appliquer un ou plusieurs middleware à une route individuelle ou à un groupe de routes. Par exemple :
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Remarque** :
> Avec webman-framework <= 1.5.6, lorsqu'un middleware est appliqué avec `->middleware()` à un groupe, la route actuelle doit se trouver sous ce groupe.

```php
# Exemple d'utilisation incorrect (ceci est valable à partir de webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Exemple d'utilisation correct
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## Routage de ressources
```php
Route::resource('/test', app\controller\IndexController::class);

// Routage de ressources spécifié
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Routage de ressources non défini
// Par exemple, la route d'accès est any /test/notify ou /test/notify/{id}, routeName est test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verbe   | URI                 | Action   | Nom de la route    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |


## Génération d'URL
> **Remarque** 
> La génération d'URL pour les routages imbriqués par groupe n'est pas encore prise en charge  

Par exemple, avec le routage :
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Nous pouvons générer l'URL pour ce routage en utilisant la méthode suivante :
```php
route('blog.view', ['id' => 100]); // Renvoie /blog/100
```

Lors de l'utilisation de l'URL du routage dans les vues, nous pouvons utiliser cette méthode pour générer automatiquement l'URL, évitant ainsi de devoir modifier de nombreuses fichiers de vues en cas de modification de l'adresse du routage.
## Obtenir des informations sur les itinéraires
> **Remarque**
> Nécessite webman-framework >= 1.3.2

À l'aide de l'objet `$request->route`, nous pouvons obtenir des informations sur l'itinéraire de la requête actuelle, par exemple

```php
$route = $request->route; // équivaut à $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Cette fonctionnalité nécessite webman-framework >= 1.3.16
}
```

> **Remarque**
> Si la requête actuelle ne correspond à aucun itinéraire configuré dans le fichier config/route.php, alors `$request->route` sera null, ce qui signifie qu'en cas d'itinéraire par défaut, `$request->route` sera null.


## Gérer les erreurs 404
Lorsqu'aucun itinéraire n'est trouvé, le code d'état 404 est renvoyé par défaut et le contenu du fichier `public/404.html` est affiché.

Si un développeur souhaite intervenir dans le processus métier lorsque l'itinéraire n'est pas trouvé, il peut utiliser la méthode de secours fournie par webman `Route::fallback($callback)`. Par exemple, la logique de code suivante redirige vers la page d'accueil lorsque l'itinéraire n'est pas trouvé.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Par exemple, renvoyer des données json lorsque l'itinéraire n'existe pas, est très utile lorsque webman est utilisé comme interface API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 non trouvé']);
});
```

Lien connexe : [Personnaliser les pages d'erreur 404 et 500](others/custom-error-page.md)

## Interface de routage
```php
// Définir l'itinéraire pour n'importe quelle méthode de demande sur $uri
Route::any($uri, $callback);
// Définir l'itinéraire GET sur $uri
Route::get($uri, $callback);
// Définir l'itinéraire POST sur $uri
Route::post($uri, $callback);
// Définir l'itinéraire PUT sur $uri
Route::put($uri, $callback);
// Définir l'itinéraire PATCH sur $uri
Route::patch($uri, $callback);
// Définir l'itinéraire DELETE sur $uri
Route::delete($uri, $callback);
// Définir l'itinéraire HEAD sur $uri
Route::head($uri, $callback);
// Définir plusieurs types de méthode de demande sur l'itinéraire simultanément
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Itinéraire groupé
Route::group($path, $callback);
// Itinéraire de ressources
Route::resource($path, $callback, [$options]);
// Désactiver l'itinéraire par défaut
Route::disableDefaultRoute($plugin = '');
// Route de secours, définir l'itinéraire par défaut
Route::fallback($callback, $plugin = '');
```
Si l'itinéraire n'a pas de correspondance (y compris l'itinéraire par défaut) et qu'aucun itinéraire de secours n'est défini, une réponse 404 sera renvoyée.

## Plusieurs fichiers de configuration d'itinéraire
Si vous souhaitez gérer les itinéraires avec plusieurs fichiers de configuration d'itinéraire, par exemple [pour plusieurs applications](multiapp.md) où chaque application a son propre fichier de configuration d'itinéraire, vous pouvez charger des fichiers de configuration d'itinéraire externes à l'aide de la directive `require`.
Par exemple dans `config/route.php`
```php
<?php

// Charger la configuration d'itinéraire de l'application admin
require_once app_path('admin/config/route.php');
// Charger la configuration d'itinéraire de l'application api
require_once app_path('api/config/route.php');

```
