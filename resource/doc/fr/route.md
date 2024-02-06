## Routage
## Règles de routage par défaut
La règle de routage par défaut de webman est `http://127.0.0.1:8787/{contrôleur}/{action}`.

Le contrôleur par défaut est `app\controller\IndexController`, et l'action par défaut est `index`.

Par exemple, en visitant :
- `http://127.0.0.1:8787`, cela accédera par défaut à la méthode `index` de la classe `app\controller\IndexController`.
- `http://127.0.0.1:8787/foo`, cela accédera par défaut à la méthode `index` de la classe `app\controller\FooController`.
- `http://127.0.0.1:8787/foo/test`, cela accédera par défaut à la méthode `test` de la classe `app\controller\FooController`.
- `http://127.0.0.1:8787/admin/foo/test`, cela accédera par défaut à la méthode `test` de la classe `app\admin\controller\FooController` (voir [application multiple](multiapp.md)).

En outre, à partir de la version 1.4, webman prend en charge des règles de routage par défaut plus complexes, par exemple :

```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── contrôleur
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

Lorsque vous souhaitez modifier une route de requête spécifique, veuillez modifier le fichier de configuration `config/route.php`.

Si vous souhaitez désactiver le routage par défaut, ajoutez la configuration suivante à la dernière ligne du fichier de configuration `config/route.php` :
```php
Route::disableDefaultRoute();
```

## Routage de fermeture éclair
Ajoutez le code de routage suivant dans `config/route.php` :
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Remarque**
> Comme les fonctions de fermeture éclair n'appartiennent à aucun contrôleur, les variables `$request->app`, `$request->controller` et `$request->action` sont toutes des chaînes vides.

Lorsque l'adresse est `http://127.0.0.1:8787/test`, elle retournera la chaîne `test`.

> **Remarque**
> Le chemin de routage doit commencer par `/`, par exemple :

```php
// Utilisation incorrecte
Route::any('test', function ($request) {
    return response('test');
});

// Utilisation correcte
Route::any('/test', function ($request) {
    return response('test');
});
```


## Routage de classe
Ajoutez le code de routage suivant dans `config/route.php` :
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Lorsque l'adresse est `http://127.0.0.1:8787/testclass`, elle retournera la valeur renvoyée par la méthode `test` de la classe `app\controller\IndexController`.

## Paramètres de routage
Si des paramètres sont présents dans la route, ils correspondent en utilisant `{clé}`, et le résultat correspondant est transmis aux paramètres de la méthode du contrôleur correspondant (à partir du deuxième paramètre), par exemple :
```php
// Correspond à /user/123 ou /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Paramètre reçu'.$id);
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

## Regroupement de routes
Parfois, les routes contiennent un préfixe commun important, dans ce cas, nous pouvons utiliser le regroupement de routes pour simplifier la définition. Par exemple :

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Équivalent à
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Utilisation en cascade du regroupement

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
Nous pouvons attribuer un middleware à une route ou à un groupe de routes spécifique. Par exemple :
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
> Avec webman-framework <= 1.5.6, lorsque `->middleware()` est appliqué après le regroupement de groupes de routes, la route actuelle doit être dans ce groupe spécifique.

```php
# Exemple d'utilisation incorrect (ce mode est valide avec webman-framework >= 1.5.7)
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

## Routes de ressources
```php
Route::resource('/test', app\controller\IndexController::class);

//Route de ressources spécifiée
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//Route de ressources non définie
// Par exemple, si l'adresse visitée est une adresse de type any /test/notify ou /test/notify/{id}, elle peut toujours être utilisée. routeName est test.notify
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
> L'utilisation de la génération d'URL pour les routes groupées imbriquées n'est pas actuellement prise en charge.

Par exemple, pour la route :
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Nous pouvons utiliser la méthode suivante pour générer l'URL de cette route.
```php
route('blog.view', ['id' => 100]); // Résultat : /blog/100
```

Lors de l'utilisation de l'URL de la route dans la vue, cette méthode peut être utilisée pour générer automatiquement l'URL, évitant ainsi de devoir modifier de nombreux fichiers de vue en cas de modification de la règle de la route.

## Obtention des informations de routage
> **Remarque**
> Nécessite webman-framework >= 1.3.2

Avec l'objet `$request->route`, nous pouvons obtenir les informations de routage de la requête actuelle, par exemple :

```php
$route = $request->route; // Équivalent à $route = request()->route;
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
> Si la requête actuelle ne correspond à aucune des routes configurées dans `config/route.php`, alors `$request->route` sera nul, ce qui signifie que lorsque la route par défaut est utilisée, `$request->route` sera nul.

## Gestion des erreurs 404
Lorsqu'une route n'est pas trouvée, le code de statut 404 est renvoyé par défaut et le contenu du fichier `public/404.html` est affiché.

Si un développeur souhaite intervenir dans le processus métier lorsqu'une route n'est pas trouvée, il peut utiliser la méthode de fallback de routage fournie par webman `Route::fallback($callback)`. Par exemple, la logique suivante redirige vers la page d'accueil lorsque la route n'est pas trouvée.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Par exemple, lorsqu'une route n'est pas trouvée, elle renvoie des données JSON, ce qui est très utile lorsque webman est utilisé en tant qu'interface API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Liens connexes [Personnalisation des pages d'erreur 404 et 500](others/custom-error-page.md)
## Interfaces de routage
```php
// Définir la route pour n'importe quelle méthode de demande pour $uri
Route::any($uri, $callback);
// Définir la route pour la demande GET de $uri
Route::get($uri, $callback);
// Définir la route pour la demande POST de $uri
Route::post($uri, $callback);
// Définir la route pour la demande PUT de $uri
Route::put($uri, $callback);
// Définir la route pour la demande PATCH de $uri
Route::patch($uri, $callback);
// Définir la route pour la demande DELETE de $uri
Route::delete($uri, $callback);
// Définir la route pour la demande HEAD de $uri
Route::head($uri, $callback);
// Définir simultanément des routes pour plusieurs types de demandes
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Routes groupées
Route::group($path, $callback);
// Route de ressource
Route::resource($path, $callback, [$options]);
// Désactiver la route par défaut
Route::disableDefaultRoute($plugin = '');
// Route de repli, définir la route par défaut en dernier recours
Route::fallback($callback, $plugin = '');
```
Si aucune route correspond à l'uri (y compris la route par défaut) et qu'aucune route de repli n'est configurée, une erreur 404 sera renvoyée.

## Fichiers de configuration de route multiples
Si vous souhaitez gérer les routes à l'aide de plusieurs fichiers de configuration de route, par exemple, chaque application a son propre fichier de configuration de route dans [multiapp.md], vous pouvez charger des fichiers de configuration de route externes à l'aide de la directive `require`.
Par exemple, dans `config/route.php`
```php
<?php

// Charger la configuration de route de l'application admin
require_once app_path('admin/config/route.php');
// Charger la configuration de route de l'application api
require_once app_path('api/config/route.php');
```
