## Routing
## Regole di routing predefinite
La regola di routing predefinita di webman è `http://127.0.0.1:8787/{controller}/{action}`.

Il controller predefinito è `app\controller\IndexController`, l'azione predefinita è `index`.

Ad esempio, accedendo a:
- `http://127.0.0.1:8787` accederà per impostazione predefinita al metodo `index` della classe `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` accederà per impostazione predefinita al metodo `index` della classe `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` accederà per impostazione predefinita al metodo `test` della classe `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` accederà per impostazione predefinita al metodo `test` della classe `app\admin\controller\FooController` (vedi [Applicazioni multiple](multiapp.md))

Inoltre, a partire dalla versione 1.4, webman supporta regole di routing predefinite più complesse, ad esempio
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

Quando si desidera modificare il routing di una richiesta, si prega di modificare il file di configurazione `config/route.php`.

Se si desidera disabilitare il routing predefinito, è possibile aggiungere la seguente configurazione all'ultima riga del file di configurazione `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Routing di chiusura
Aggiungere il seguente codice di routing nel file `config/route.php`:
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});
```
> **Nota**
> Poiché la funzione di chiusura non appartiene a nessun controller, quindi `$request->app`, `$request->controller`, `$request->action` sono tutti una stringa vuota.

Quando l'indirizzo è `http://127.0.0.1:8787/test`, restituirà la stringa `test`.

> **Nota**
> Il percorso di routing deve iniziare con `/`, ad esempio:

```php
use support\Request;
// Modo errato
Route::any('test', function (Request $request) {
    return response('test');
});

// Modo corretto
Route::any('/test', function (Request $request) {
    return response('test');
});
```

## Routing di classe
Aggiungere il seguente codice di routing nel file `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Quando l'indirizzo è `http://127.0.0.1:8787/testclass`, restituirà il valore restituito del metodo `test` della classe `app\controller\IndexController`.

## Routing con annotazioni

Definire le route tramite annotazioni sui metodi del controller, senza configurare in `config/route.php`.

> **Nota**
> Questa funzionalità richiede webman-framework >= v2.2.0

### Utilizzo base

```php
namespace app\controller;
use support\annotation\route\Get;
use support\annotation\route\Post;

class UserController
{
    #[Get('/user/{id}')]
    public function show($id)
    {
        return "user $id";
    }

    #[Post('/user')]
    public function store()
    {
        return 'created';
    }
}
```

Annotazioni disponibili: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (qualsiasi metodo). Il percorso deve iniziare con `/`. Il secondo parametro può specificare il nome della route, usato da `route()` per generare URL.

### Annotazioni senza parametri: restringere il metodo HTTP sulla route predefinita

Senza percorso, limita solo i metodi HTTP consentiti per quell'azione, continuando a usare il percorso predefinito:

```php
#[Post]
public function create() { ... }  // Solo POST consentito, il percorso resta /user/create

#[Get]
public function index() { ... }   // Solo GET consentito
```

Si possono combinare più annotazioni per consentire più metodi di richiesta:

```php
#[Get]
#[Post]
public function form() { ... }  // Consente GET e POST
```

I metodi non dichiarati nelle annotazioni restituiranno 405.

Più annotazioni con percorso registrano route indipendenti: `#[Get('/a')] #[Post('/b')]` genera le route GET /a e POST /b.

### Prefisso del gruppo di route

Usare `#[RouteGroup]` sulla classe per aggiungere prefisso a tutte le route dei metodi:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // Percorso effettivo /api/v1/user/{id}
    public function show($id) { ... }
}
```

### Metodi HTTP personalizzati e nome della route

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### Middleware

`#[Middleware]` su controller o metodo si applica alle route con annotazioni, uso come `support\annotation\Middleware`.

## Parametri di Routing
Se ci sono parametri nel routing, corrispondono tramite `{chiave}` e i risultati corrispondenti vengono passati come argomenti ai metodi dei controller (a partire dal secondo argomento), ad esempio:
```php
// Corrisponde a /user/123 e /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('Parametro ricevuto: '.$id);
    }
}
```

Altri esempi:
```php
use support\Request;
// Corrisponde a /user/123, non corrisponde a /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// Corrisponde a /user/foobar, non corrisponde a /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// Corrisponde a /user /user/123 e /user/abc   [] indica opzionale
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// Corrisponde a qualsiasi richiesta con prefisso /user/
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// Corrisponde a tutte le richieste options   : indica regex per il parametro nominato
Route::options('[{path:.+}]', function () {
    return response('');
});
```

Riepilogo utilizzo avanzato

> La sintassi `[]` nelle route Webman si usa principalmente per parti opzionali o corrispondenze dinamiche; permette di definire strutture di percorso più complesse
>
> `:` si usa per specificare espressione regolare

## Gruppo di Routing
A volte i percorsi di routing contengono molti prefissi comuni, in questo caso possiamo utilizzare i gruppi di routing per semplificarne la definizione. Ad esempio:

```php
use support\Request;
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('crea');});
   Route::any('/edit', function (Request $request) {return response('modifica');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
equivalente a
```php
Route::any('/blog/create', function (Request $request) {return response('crea');});
Route::any('/blog/edit', function (Request $request) {return response('modifica');});
Route::any('/blog/view/{id}', function (Request $request, $id) {return response("view $id");});
```

Utilizzo nidificato del gruppo

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('crea');});
      Route::any('/edit', function (Request $request) {return response('modifica');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
});
```

## Middleware di Routing
Possiamo impostare un middleware per una singola o un gruppo di route.
Ad esempio:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('crea');});
   Route::any('/edit', function () {return response('modifica');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Esempio di utilizzo errato (valido nella versione webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('crea');});
      Route::any('/edit', function (Request $request) {return response('modifica');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Esempio di utilizzo corretto
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('crea');});
      Route::any('/edit', function (Request $request) {return response('modifica');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```
## Routing basato su risorse
```php
Route::resource('/test', app\controller\IndexController::class);

// Routing delle risorse specificate
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Routing delle risorse non definite
// Quando si accede a /notify, la route sarà di tipo any /test/notify o /test/notify/{id} sono entrambi validi, con routeName test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verbo  | URI                | Azione   | Nome della route |
|--------|--------------------|----------|------------------|
| GET    | /test              | index    | test.index       |
| GET    | /test/create       | create   | test.create      |
| POST   | /test              | store    | test.store       |
| GET    | /test/{id}         | show     | test.show        |
| GET    | /test/{id}/edit    | edit     | test.edit        |
| PUT    | /test/{id}         | update   | test.update      |
| DELETE | /test/{id}         | destroy  | test.destroy     |
| PUT    | /test/{id}/recovery| recovery | test.recovery    |


## Generazione di URL
> **Nota** 
> Al momento la generazione dell'URL per i gruppi di route nidificati non è supportata  

Per esempio, per la route:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
È possibile generare l'URL di questa route utilizzando il metodo seguente.
```php
route('blog.view', ['id' => 100]); // Risultato: /blog/100
```

Questo metodo può essere utilizzato nelle viste per generare l'URL della route. In questo modo, anche se le regole di routing cambiano, l'URL verrà generato automaticamente, evitando così la necessità di apportare modifiche ai file di visualizzazione a causa di modifiche agli indirizzi delle route.


## Ottenere informazioni sulle route

È possibile ottenere le informazioni sulla route corrente utilizzando l'oggetto `$request->route`. Ad esempio:

```php
$route = $request->route; // Equivalente a $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **Nota**
> Se la richiesta corrente non corrisponde a nessuna delle route definite in `config/route.php`, allora `$request->route` sarà nullo, ossia quando viene utilizzata la route predefinita, `$request->route` sarà nullo.


## Gestione dell'errore 404
Quando la route non viene trovata, viene restituito automaticamente lo stato 404 e viene visualizzato il contenuto 404 corrispondente.

Se i developer desiderano intervenire quando una route non viene trovata, possono utilizzare il metodo di fallback delle route fornito da webman `Route::fallback($callback)`. Ad esempio, il seguente codice reindirizzerà alla homepage quando la route non viene trovata.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Un altro esempio potrebbe essere di restituire dei dati JSON quando la route non esiste, il che risulta particolarmente utile quando si utilizza webman come interfaccia API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## Aggiungere middleware a 404

Per default le richieste 404 non passano attraverso alcun middleware. Se è necessario aggiungere middleware alle richieste 404, consultare il seguente codice:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

Link correlato [Pagina di errore personalizzata 404 500](others/custom-error-page.md)

## Disabilitare route predefinita

```php
// Disabilitare la route predefinita del progetto principale, non influisce sui plugin
Route::disableDefaultRoute();
// Disabilitare la route admin del progetto principale, non influisce sui plugin
Route::disableDefaultRoute('', 'admin');
// Disabilitare la route predefinita del plugin foo, non influisce sul progetto principale
Route::disableDefaultRoute('foo');
// Disabilitare la route admin del plugin foo, non influisce sul progetto principale
Route::disableDefaultRoute('foo', 'admin');
// Disabilitare la route predefinita del controller [\app\controller\IndexController::class, 'index']
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## Annotazione per disabilitare route predefinita

Possiamo usare annotazioni per disabilitare la route predefinita di un controller, ad esempio:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

#[DisableDefaultRoute]
class IndexController
{
    public function index()
    {
        return 'index';
    }
}
```

Allo stesso modo, possiamo anche usare annotazioni per disabilitare la route predefinita di un metodo del controller, ad esempio:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

class IndexController
{
    #[DisableDefaultRoute]
    public function index()
    {
        return 'index';
    }
}
```

## Interfaccia della route
```php
// Imposta una route con qualsiasi metodo per $uri
Route::any($uri, $callback);
// Imposta una route con metodo GET per $uri
Route::get($uri, $callback);
// Imposta una route con metodo POST per $uri
Route::post($uri, $callback);
// Imposta una route con metodo PUT per $uri
Route::put($uri, $callback);
// Imposta una route con metodo PATCH per $uri
Route::patch($uri, $callback);
// Imposta una route con metodo DELETE per $uri
Route::delete($uri, $callback);
// Imposta una route con metodo HEAD per $uri
Route::head($uri, $callback);
// Imposta una route con metodo OPTIONS per $uri
Route::options($uri, $callback);
// Imposta simultaneamente una route per vari tipi di metodo
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Route di gruppo
Route::group($path, $callback);
// Route delle risorse
Route::resource($path, $callback, [$options]);
// Disabilita la route predefinita
Route::disableDefaultRoute($plugin = '');
// Fallback della route, imposta la route predefinita di fallback
Route::fallback($callback, $plugin = '');
// Ottenere tutte le informazioni sulle route
Route::getRoutes();
```
Se non c'è alcuna route corrispondente all'uri (inclusa la route predefinita) e non è stata impostata alcuna route di fallback, verrà restituito uno stato 404.
## Più file di configurazione delle route
Se si desidera gestire le route utilizzando più file di configurazione delle route, ad esempio per [applicazioni multiple](multiapp.md) in cui ogni app ha il proprio file di configurazione delle route, è possibile carica file di configurazione esterni utilizzando il metodo `require`.
Ad esempio nel file `config/route.php`.
```php
<?php

// Carica il file di configurazione delle route dell'app admin
require_once app_path('admin/config/route.php');
// Carica il file di configurazione delle route dell'app api
require_once app_path('api/config/route.php');
```
