# Middleware
Les middlewares sont généralement utilisés pour intercepter les demandes ou les réponses. Par exemple, vérifier l'identité de l'utilisateur de manière uniforme avant d'exécuter le contrôleur, comme rediriger vers la page de connexion si l'utilisateur n'est pas connecté, ajouter un en-tête spécifique à la réponse, ou encore effectuer une analyse statistique sur la proportion des demandes pour une URI, et ainsi de suite.

## Modèle d'oignon des middlewares

```                          
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Requête ───────────────────────> Contrôleur ─ Réponse ───────────────────────────> Client
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Les middlewares et les contrôleurs forment un modèle d'oignon classique, où les middlewares agissent comme les couches d'un oignon, tandis que le contrôleur forme le noyau de l'oignon. Comme illustré dans le schéma, la demande traverse les middlewares 1, 2, 3 pour atteindre le contrôleur. Ensuite, le contrôleur renvoie une réponse qui traverse à nouveau les middlewares dans l'ordre 3, 2, 1 avant d'être renvoyée au client. Autrement dit, à chaque middleware, nous pouvons à la fois obtenir la demande et la réponse.

## Interception des demandes
Parfois, nous ne voulons pas qu'une demande atteigne le niveau du contrôleur. Par exemple, si dans le middleware 2, nous constatons que l'utilisateur actuel n'est pas connecté, nous pouvons directement intercepter la demande et renvoyer une réponse de connexion. Le processus ressemblerait à ceci :

```                          
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Requête ─────────┐     │    │    Contrôleur    │      │      │     │
            │     │ Réponse  │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

Comme le montre le schéma, une demande atteint le middleware 2 et génère ensuite une réponse de connexion. Cette réponse traverse ensuite le middleware 1 pour être renvoyée au client.

## Interface middleware
Les middlewares doivent implémenter l'interface `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Traite une demande envoyée au serveur.
     *
     * Traite une demande envoyée au serveur afin de produire une réponse.
     * S'il est incapable de produire lui-même la réponse, il peut déléguer cette tâche au gestionnaire de demandes fourni.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Cela signifie qu'ils doivent implémenter la méthode `process`. Cette méthode doit renvoyer un objet `support\Response`. Par défaut, cet objet est généré par `$handler($request)` (la demande continue de traverser l'oignon). Cependant, il peut également être généré par des fonctionnalités auxiliaires telles que `response()`, `json()`, `xml()`, `redirect()`, etc. (la demande cesse de traverser l'oignon).

## Obtention de la demande et de la réponse dans les middlewares
Dans les middlewares, nous pouvons obtenir la demande ainsi que la réponse après l'exécution du contrôleur. Cela se divise en trois parties à l'intérieur du middleware :
1. Phase de traversée de la demande, c'est-à-dire avant le traitement de la demande
2. Phase de traitement de la demande par le contrôleur
3. Phase de traversée de la réponse, c'est-à-dire après le traitement de la demande

Ces trois phases sont illustrées dans le middleware comme suit :
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'Il s'agit de la phase de traversée de la demande, avant le traitement de la demande';

        $response = $handler($request); // La demande continue de traverser l'oignon jusqu'à ce qu'elle atteigne le contrôleur et obtienne une réponse

        echo 'Il s'agit de la phase de traversée de la réponse, après le traitement de la demande';

        return $response;
    }
}
```
## Exemple : Middleware d'authentification
Créez le fichier `app/middleware/AuthCheckTest.php` (créez le répertoire s'il n'existe pas) comme suit :

```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // Déjà connecté, la requête continue de traverser les couches d'oignon
            return $handler($request);
        }
        
        // Obtenir par réflexion les méthodes du contrôleur qui ne nécessitent pas de connexion
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // La méthode demandée nécessite une connexion
        if (!in_array($request->action, $noNeedLogin)) {
            // Interception de la requête, retourne une réponse de redirection, arrêtant la propagation de la requête à travers les couches d'oignon
            return redirect('/user/login');
        }

        // Pas besoin de se connecter, la requête continue de traverser les couches d'oignon
        return $handler($request);
    }
}
```

Créez le contrôleur `app/controller/UserController.php`

```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Méthodes ne nécessitant pas de connexion
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **Remarque**
> `$noNeedLogin` enregistre les méthodes de ce contrôleur qui peuvent être accessibles sans se connecter

Ajoutez le middleware global dans le fichier `config/middleware.php` comme ceci :

```php
return [
    // Middleware global
    '' => [
        // ... Autres middlewares omis ici
        app\middleware\AuthCheckTest::class,
    ]
];
```

Avec le middleware d'authentification, nous pouvons nous concentrer sur l'écriture du code métier dans la couche de contrôleur sans se préoccuper de la connexion de l'utilisateur.

## Exemple : Middleware de requête CORS
Créez le fichier `app/middleware/AccessControlTest.php` (créez le répertoire s'il n'existe pas) comme suit :

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Si c'est une requête OPTIONS, renvoie une réponse vide, sinon continue de traverser les couches d'oignon et obtient une réponse
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Ajoute les entêtes HTTP CORS à la réponse
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **Remarque**
> Les requêtes CORS peuvent déclencher des requêtes OPTIONS. Nous ne voulons pas que les requêtes OPTIONS atteignent le contrôleur, donc nous renvoyons directement une réponse vide (`response('')`) pour intercepter la requête.
> Si votre API nécessite un routage, veuillez utiliser `Route::any(..)` ou `Route::add(['POST', 'OPTIONS'], ..)`.

Ajoutez le middleware global dans le fichier `config/middleware.php` comme ceci :

```php
return [
    // Middleware global
    '' => [
        // ... Autres middlewares omis ici
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Remarque**
> Si une requête ajax a des en-têtes personnalisés, ces en-têtes personnalisés doivent être ajoutés au champ `Access-Control-Allow-Headers` du middleware, sinon une erreur `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` se produira.

## Remarque

- Les middlewares sont divisés en middlewares globaux, middlewares d'application (uniquement disponibles en mode multi-Applications, voir [multiapp.md](multiapp.md)), et middlewares de routage.
- Actuellement, les middlewares individuels de contrôleur ne sont pas pris en charge (mais il est possible d'implémenter une fonctionnalité similaire à travers le middleware en vérifiant `$request->controller`).
- La configuration des middlewares se trouve dans le fichier `config/middleware.php`.
- La configuration des middlewares globaux est sous la clé `''`.
- La configuration des middlewares d'application est sous le nom de l'application spécifique, par exemple :

```php
return [
    // Middleware global
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware d'application api (uniquement disponible en mode multi-Applications)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware de routage

Nous pouvons attribuer un ou plusieurs middlewares à une route spécifique ou à un groupe de routes.

Par exemple, ajoutez la configuration suivante dans `config/route.php` :

```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## Passage de paramètres au Middleware via le constructeur

> **Remarque**
> Cette fonctionnalité nécessite webman-framework >= 1.4.8

À partir de la version 1.4.8, le fichier de configuration prend en charge l'instanciation directe des middlewares ou des fonctions anonymes, ce qui rend possible la transmission de paramètres au middleware via le constructeur. Par exemple, dans le fichier `config/middleware.php`, la configuration peut être la suivante :

```
return [
   // Middleware global
   '' => [
       new app\middleware\AuthCheckTest($param1, $param2, ...),
       function(){
           return new app\middleware\AccessControlTest($param1, $param2, ...);
       },
   ],
   // Middleware d'application api (uniquement disponible en mode multi-Applications)
   'api' => [
       app\middleware\ApiOnly::class,
   ]
];
```

De même, les middlewares de routage peuvent également transmettre des paramètres via le constructeur, par exemple dans `config/route.php` :

```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ordre d'exécution des middlewares

- L'ordre d'exécution des middlewares est le suivant : `middlewares globaux` -> `middlewares d'application` -> `middlewares de routage`.
- Lorsqu'il y a plusieurs middlewares globaux, ils sont exécutés dans l'ordre réel de leur configuration (il en va de même pour les middlewares d'application et de routage).
- Les requêtes 404 ne déclenchent aucun middleware, y compris les middlewares globaux.

## Passage de paramètres aux middlewares via la route (route->setParams)

**Configuration de la route `config/route.php`**

```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (supposons qu'il s'agit d'un middleware global)**

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // La variable de route par défaut $request->route est nulle, donc nous devons vérifier si $request->route est vide
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```
## Transmission de paramètres depuis un middleware vers un contrôleur

Parfois, le contrôleur a besoin d'utiliser les données générées dans le middleware. Dans ce cas, nous pouvons transmettre des paramètres au contrôleur en ajoutant des propriétés à l'objet `$request`. Par exemple :

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'une certaine valeur';
        return $handler($request);
    }
}
```

**Contrôleur :**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## Obtention des informations de route actuelle depuis un middleware
> **Remarque**
> Nécessite webman-framework >= 1.3.2

Nous pouvons utiliser `$request->route` pour obtenir l'objet de la route, et en appelant les méthodes correspondantes, obtenir les informations appropriées.

**Configuration de la route**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // Si aucune route ne correspond à la demande (à l'exception de la route par défaut), $request->route sera nul
        // Supposons que l'adresse visitée par le navigateur est /user/111, les informations suivantes seront imprimées
        if ($route) {
            var_export($route->getPath()); // /user/{uid}
            var_export($route->getMethods()); // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName()); // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback()); // ['app\\controller\\UserController', 'view']
            var_export($route->param()); // ['uid'=>111]
            var_export($route->param('uid')); // 111 
        }
        return $handler($request);
    }
}
```

> **Remarque**
> La méthode `$route->param()` nécessite webman-framework >= 1.3.16


## Récupération d'exceptions depuis un middleware
> **Remarque**
> Nécessite webman-framework >= 1.3.15

Pendant le traitement des requêtes, des exceptions peuvent survenir. Dans le middleware, utilisez `$response->exception()` pour obtenir l'exception.

**Configuration de la route**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('test d'exception');
});
```

**Middleware :**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## Middleware super global

> **Remarque**
> Cette fonctionnalité nécessite webman-framework >= 1.5.16

Les middlewares globaux du projet principal n'affectent que le projet principal et n'ont aucun effet sur les [plugins d'application](app/app.md). Parfois, nous voulons ajouter un middleware qui affecte globalement tous les plugins, y compris les plugins d'application, nous pouvons alors utiliser un middleware super global.

Configurez comme suit dans `config/middleware.php` :
```php
return [
    '@' => [ // Ajouter des middlewares globaux au projet principal et à tous les plugins
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Ajouter des middlewares globaux uniquement au projet principal
];
```

> **Remarque**
> Les middlewares super globaux `@` peuvent être configurés non seulement dans le projet principal, mais aussi dans un plugin spécifique, par exemple, en configurant le middleware super global `@` dans `plugin/ai/config/middleware.php`, il affectera également le projet principal et tous les plugins.


## Ajout d'un middleware à un plugin spécifique

> **Remarque**
> Cette fonctionnalité nécessite webman-framework >= 1.5.16

Parfois, nous voulons ajouter un middleware à un [plugin d'application](app/app.md), mais nous ne voulons pas modifier le code du plugin (car la mise à niveau écraserait les modifications). Dans ce cas, nous pouvons ajouter un middleware au plugin depuis le projet principal.

Configurez comme suit dans `config/middleware.php` :
```php
return [
    'plugin.ai' => [], // Ajouter un middleware au plugin ai
    'plugin.ai.admin' => [], // Ajouter un middleware au module admin du plugin ai
];
```

> **Remarque**
> Bien sûr, il est également possible d'ajouter une configuration similaire à un autre plugin pour affecter le plugin AI, par exemple en ajoutant une telle configuration dans `plugin/foo/config/middleware.php`, cela affecterait également le plugin AI.
