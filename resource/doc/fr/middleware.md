# Middleware
Les middlewares sont généralement utilisés pour intercepter les requêtes ou les réponses. Par exemple, pour vérifier l'identité de l'utilisateur de manière uniforme avant d'exécuter le contrôleur, rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté, ajouter un en-tête à la réponse, ou encore pour calculer la proportion d'une demande d'URI, etc.

## Modèle d'oignon des middlewares
```plaintext
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── Requête ────────────────────> Contrôleur ─ Réponse ───────────────────────────> Client
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Les middlewares et les contrôleurs forment un modèle d'oignon classique, où les middlewares agissent comme des couches d'oignon et le contrôleur en constitue le cœur. Comme le montre le schéma, la requête traverse les middlewares 1, 2 et 3 pour atteindre le contrôleur, qui renvoie une réponse. Ensuite, la réponse traverse de nouveau les middlewares 3, 2 et 1 dans l'ordre, avant d'être renvoyée au client. Cela signifie qu'à chaque middleware, nous pouvons à la fois obtenir la requête et la réponse.

## Interception des requêtes
Parfois, nous ne voulons pas qu'une certaine requête atteigne le contrôleur, par exemple, si un middleware d'authentification constate que l'utilisateur n'est pas connecté, il peut directement interrompre la requête et renvoyer une réponse de connexion. Le processus ressemble à ceci :

```plaintext
                              
            ┌──────────────────────────────────────────────────────────┐
            │                     middleware1                          │ 
            │     ┌──────────────────────────────────────────────┐     │
            │     │            Middleware d'authentification     │     │
            │     │         ┌──────────────────────────────┐     │     │
            │     │         │         middleware3          │     │     │       
            │     │         │     ┌──────────────────┐     │     │     │
            │     │         │     │                  │     │     │     │
   ── Requête ──────────┐   │     │   Contrôleur     │     │     │     │
            │     │ Réponse │     │                  │     │     │     │
   <────────────────────┘   │     └──────────────────┘     │     │     │
            │     │         │                              │     │     │
            │     │         └──────────────────────────────┘     │     │
            │     │                                              │     │
            │     └──────────────────────────────────────────────┘     │
            │                                                          │
            └──────────────────────────────────────────────────────────┘
```
Comme illustré, la requête atteint le middleware d'authentification qui génère une réponse de connexion, traversant de nouveau le middleware 1 avant de revenir au navigateur.

## Interface du middleware
Le middleware doit implémenter l'interface `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Cela signifie qu'il faut implémenter la méthode `process`, qui doit renvoyer un objet `support\Response`. Par défaut, cet objet est généré par `$handler($request)` (la requête continue à traverser l'oignon). Il peut également s'agir d'une réponse générée par les fonctions d'aide telles que `response()`, `json()`, `xml()`, `redirect()`, etc. (la requête cesse de traverser l'oignon).

## Obtention des requêtes et des réponses dans le middleware
Dans un middleware, nous pouvons obtenir à la fois la requête et la réponse après le traitement du contrôleur. Ainsi, le middleware se divise en trois parties internes :
1. La phase de passage de la requête, c'est-à-dire avant le traitement de la requête
2. La phase de traitement de la requête par le contrôleur
3. La phase de sortie de la réponse, c'est-à-dire après le traitement de la requête

Ces trois phases se manifestent dans le middleware, comme suit :
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
        echo 'C'est la phase de passage de la requête, avant le traitement';
        
        $response = $handler($request); // La requête continue à traverser l'oignon jusqu'à ce qu'elle soit traitée par le contrôleur
        
        echo 'C'est la phase de sortie de la réponse, après le traitement de la requête';
        
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
            // Déjà connecté, la demande continue de traverser les couches d'oignon
            return $handler($request);
        }

        // Obtenir par réflexion les méthodes du contrôleur qui n'ont pas besoin de connexion
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // La méthode demandée nécessite une connexion
        if (!in_array($request->action, $noNeedLogin)) {
            // Intercepter la demande, renvoyer une réponse de redirection, arrêter la demande de traverser les couches d'oignon
            return redirect('/user/login');
        }

        // Pas besoin de se connecter, la demande continue de traverser les couches d'oignon
        return $handler($request);
    }
}
```

Créez un contrôleur `app/controller/UserController.php` comme suit :
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Méthodes qui ne nécessitent pas de connexion
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
> `$noNeedLogin` enregistre les méthodes du contrôleur actuel qui peuvent être consultées sans connexion

Ajoutez le middleware global dans `config/middleware.php` comme suit :
```php
return [
    // Middleware global
    '' => [
        // ... D'autres middlewares sont omis ici
        app\middleware\AuthCheckTest::class,
    ]
];
```

Avec le middleware d'authentification, nous pouvons nous concentrer sur l'écriture du code métier au niveau du contrôleur, sans nous soucier de savoir si l'utilisateur est connecté ou non.

## Exemple : Middleware pour les requêtes de cross-origin
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
        
        // Ajoute des en-têtes HTTP liés à la cross-origin à la réponse
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
> Les requêtes cross-origin peuvent entraîner des requêtes OPTIONS, nous ne voulons pas que les requêtes OPTIONS atteignent le contrôleur, donc nous renvoyons directement une réponse vide (`response('')`) pour intercepter la demande.
> Si votre API nécessite une configuration de route, veuillez utiliser `Route::any(..)` ou `Route::add(['POST', 'OPTIONS'], ..)`.

Ajoutez le middleware global dans `config/middleware.php` comme suit :
```php
return [
    // Middleware global
    '' => [
        // ... D'autres middlewares sont omis ici
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Remarque**
> Si une requête ajax a des entêtes personnalisés, ces entêtes personnalisés doivent être ajoutés au champ `Access-Control-Allow-Headers` du middleware, sinon une erreur ` Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` sera signalée.

## Remarque

- Les middlewares sont divisés en middlewares globaux, middlewares d'application (valables uniquement en mode multi-Application, voir [Multiple Applications](multiapp.md)), et middlewares de route.
- Actuellement, les middlewares spécifiques à un contrôleur ne sont pas supportés (mais vous pouvez simuler des middlewares de contrôleur en vérifiant `$request->controller` dans le middleware).
- Les fichiers de configuration des middlewares se trouvent dans `config/middleware.php`.
- La configuration des middlewares globaux se fait sous la clé `''`.
- La configuration des middlewares d'application se fait sous le nom de l'application spécifique, par exemple :

```php
return [
    // Middleware global
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware de l'application API (valide uniquement en mode multi-Application)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware de route

Nous pouvons attribuer un ou plusieurs middlewares à une route ou à un groupe de routes.
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

## Passing Parameters to Middlewares Constructor

> **Remarque**
> Cette fonctionnalité nécessite webman-framework >= 1.4.8

À partir de la version 1.4.8, le fichier de configuration peut instancier directement des middlewares ou des fonctions anonymes, ce qui facilite la transmission de paramètres au middleware via son constructeur.
Par exemple, dans `config/middleware.php`, vous pouvez également configurer comme ceci :

```php
return [
    // Middleware global
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware de l'application API (valide uniquement en mode multi-Application)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

De même, vous pouvez également transmettre des paramètres au middleware via son constructeur dans les middlewares de route, par exemple dans `config/route.php` :

```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ordre d'exécution des middlewares

- L'ordre d'exécution des middlewares est le suivant : `middleware global` -> `middleware d'application` -> `middleware de route`.
- Lorsqu'il y a plusieurs middlewares globaux, ils sont exécutés dans l'ordre configuré (idem pour les middlewares d'application et de route).
- Les requêtes 404 ne déclenchent aucun middleware, y compris les middlewares globaux.

## Passing Parameters to Middlewares via Route (route->setParams)

**Configuration de la route dans `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (supposons être global)**
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
        // Par défaut, $request->route est nul, donc vous devez vérifier si $request->route est vide
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Passing Parameters from Middleware to Controller

Parfois, le contrôleur a besoin d'utiliser des données générées dans le middleware, dans ce cas, nous pouvons transmettre les paramètres au contrôleur en ajoutant des propriétés à l'objet `$request`. Par exemple :

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
        $request->data = 'some value';
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
## Obtenir les informations de routage actuelles dans un middleware

> **Remarque**
> Nécessite webman-framework >= 1.3.2

Nous pouvons utiliser `$request->route` pour obtenir l'objet de routage et obtenir les informations correspondantes en appelant les méthodes correspondantes.

**Configuration de routage**
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
        // Si aucune route ne correspond à la requête (à l'exception de la route par défaut), $request->route sera null
        // Supposons que l'adresse visitée dans le navigateur soit /user/111, les informations suivantes seront imprimées
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **Remarque**
> La méthode `$route->param()` nécessite webman-framework >= 1.3.16

## Obtenir une exception dans un middleware

> **Remarque**
> Nécessite webman-framework >= 1.3.15

Pendant le traitement des activités, des exceptions peuvent survenir, dans ce cas, dans le middleware, utilisez `$response->exception()` pour obtenir l'exception.

**Configuration de routage**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**Middleware：**
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

## Middleware superglobal

> **Remarque**
> Cette fonctionnalité nécessite webman-framework >= 1.5.16

Les middlewares globaux du projet principal n'affectent que le projet principal et n'affectent pas les [plugins d'application](app/app.md). Parfois, nous souhaitons ajouter un middleware qui affectera globalement tous les plugins, dans ce cas, nous pouvons utiliser les middlewares superglobaux.

Configurez comme suit dans `config/middleware.php` :
```php
return [
    '@' => [ // Ajoute un middleware global au projet principal et à tous les plugins
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Ajoute un middleware global uniquement au projet principal
];
```

> **Remarque**
> Le middleware superglobal `@` peut non seulement être configuré dans le projet principal, mais également dans un plugin spécifique, par exemple, s'il est configuré dans `plugin/ai/config/middleware.php`, cela affectera également le projet principal et tous les plugins.

## Ajouter un middleware à un plugin spécifique

> **Remarque**
> Cette fonctionnalité nécessite webman-framework >= 1.5.16

Parfois, nous voulons ajouter un middleware à un [plugin d'application](app/app.md), sans modifier le code du plugin (car il serait écrasé lors de la mise à jour). Dans ce cas, nous pouvons configurer le middleware dans le projet principal.

Configurez comme suit dans `config/middleware.php` :
```php
return [
    'plugin.ai' => [], // Ajoute un middleware au plugin AI
    'plugin.ai.admin' => [], // Ajoute un middleware au module admin du plugin AI
];
```

> **Remarque**
> Bien entendu, il est également possible de configurer une configuration similaire dans un plugin spécifique pour affecter d'autres plugins, par exemple, en ajoutant les mêmes configurations dans `plugin/foo/config/middleware.php`, cela affectera le plugin AI.
