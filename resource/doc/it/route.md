## Routing
## Regole di routing predefinite
La regola predefinita di routing di webman è `http://127.0.0.1:8787/{controller}/{azione}`.

Il controller predefinito è `app\controller\IndexController`, e l'azione predefinita è `index`.

Ad esempio, se si accede a:
- `http://127.0.0.1:8787` verrà utilizzato per impostazione predefinita il metodo `index` della classe `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` verrà utilizzato per impostazione predefinita il metodo `index` della classe `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` verrà utilizzato per impostazione predefinita il metodo `test` della classe `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` verrà utilizzato per impostazione predefinita il metodo `test` della classe `app\admin\controller\FooController` (vedi [Applicazioni multiple](multiapp.md))

Inoltre, a partire da webman 1.4, è supportato un sistema di routing predefinito più complesso, ad esempio
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

Quando si desidera modificare il percorso di una richiesta, è necessario modificare il file di configurazione `config/route.php`.

Se si desidera disattivare il routing predefinito, è possibile aggiungere la seguente configurazione all'ultima riga del file di configurazione `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Routing a funzione anonima
Aggiungi il seguente codice di routing nel file `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Nota**
> Poiché le funzioni anonime non appartengono a nessun controller, le variabili `$request->app`, `$request->controller` e `$request->action` sono tutte stringhe vuote.

Quando si accede all'indirizzo `http://127.0.0.1:8787/test`, verrà restituita la stringa `test`.

> **Nota**
> Il percorso di routing deve iniziare con `/`, ad esempio

```php
// Modo sbagliato
Route::any('test', function ($request) {
    return response('test');
});

// Modo corretto
Route::any('/test', function ($request) {
    return response('test');
});
```


## Routing a classe
Aggiungi il seguente codice di routing nel file `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Quando si accede all'indirizzo `http://127.0.0.1:8787/testclass`, verrà restituito il valore restituito del metodo `test` della classe `app\controller\IndexController`.


## Parametri di routing
Se sono presenti parametri nel routing, corrispondi utilizzando `{chiave}`, e il risultato del match verrà passato come parametro del metodo del controller corrispondente (iniziando dal secondo parametro), ad esempio:
```php
// Corrisponde /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Ricevuti parametri'.$id);
    }
}
```

Altri esempi:
```php
// Corrisponde a /user/123, non corrisponde a /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Corrisponde a /user/foobar, non corrisponde a /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Corrisponde a /user, /user/123 e /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Corrisponde a tutte le richieste options
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Gruppi di routing
A volte i routing includono un sacco di prefissi simili, in tal caso è possibile semplificare la definizione utilizzando i gruppi di routing. Ad esempio:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("vista $id");});
});
```
Equivalente a
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("vista $id");});
```

Utilizzo di gruppi nidificati

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("vista $id");});
   });  
});
```

## Middleware di routing

Possiamo impostare un middleware per uno o più route.
Ad esempio:

```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("vista $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Nota**:
> In webman-framework <= 1.5.6, quando il middleware della route viene impostato dopo il raggruppamento di gruppo, la route corrente deve essere sotto il gruppo corrente.

```php
# Esempio di utilizzo errato (questo metodo è valido in webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("vista $id");});
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
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("vista $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## Routing delle risorse
```php
Route::resource('/test', app\controller\IndexController::class);

// Routing delle risorse specifico
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Routing delle risorse non definite
// Ad esempio, l'indirizzo di accesso è qualsiasi di /test/notify o /test/notify/{id}, routeName è test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verbo  | URI                 | Azione  | Nome del percorso |
|--------|---------------------|---------|-------------------|
| GET    | /test               | index   | test.index        |
| GET    | /test/create        | create  | test.create       |
| POST   | /test               | store   | test.store        |
| GET    | /test/{id}          | show    | test.show         |
| GET    | /test/{id}/edit     | edit    | test.edit         |
| PUT    | /test/{id}          | update  | test.update       |
| DELETE | /test/{id}          | destroy | test.destroy      |
| PUT    | /test/{id}/recovery | recovery| test.recovery     |



## Generare URL
> **Nota**
> Al momento non è supportata la generazione di URL per routing nidificati.

Ad esempio, per il seguente routing:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Possiamo utilizzare il seguente metodo per generare l'URL per questo routing.
```php
route('blog.view', ['id' => 100]); // Restituisce /blog/100
```

Quando si utilizza questo metodo negli URL delle viste, l'URL verrà generato automaticamente indipendentemente dalle modifiche alle regole di routing, evitando la necessità di modificare massicciamente i file di visualizzazione a causa di modifiche negli indirizzi dei routing.


## Ottenere informazioni sul routing
> **Nota**
> È necessario avere webman-framework >= 1.3.2

Attraverso l'oggetto `$request->route` possiamo ottenere le informazioni sul routing corrente, ad esempio

```php
$route = $request->route; // Equivalente a $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Questa funzione richiede webman-framework >= 1.3.16
}
```

> **Nota**
> Se la richiesta corrente non corrisponde a nessun routing configurato in `config/route.php`, allora `$request->route` è null, il che significa che quando si utilizza il routing predefinito, `$request->route` è null.


## Gestire l'errore 404
Quando un routing non viene trovato, viene restituito automaticamente il codice di stato 404 e il contenuto del file `public/404.html`.

Se si desidera gestire manualmente il comportamento quando il routing non viene trovato, è possibile utilizzare il metodo di fallback del routing di webman `Route::fallback($callback)`. Ad esempio, il seguente codice redirezionerà alla homepage quando il routing non viene trovato.
```php
Route::fallback(function(){
    return redirect('/');
});
```

Allo stesso modo, ad esempio, quando il routing non viene trovato, verranno restituiti dati JSON, che è molto utile quando webman funge da interfaccia API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 pagina non trovata']);
});
```

Link correlati [Pagina di errore personalizzata](others/custom-error-page.md)
## Interfaccia del router
```php
// Imposta la route per qualsiasi metodo di richiesta per $uri
Route::any($uri, $callback);
// Imposta la route di richiesta GET per $uri
Route::get($uri, $callback);
// Imposta la route di richiesta POST per $uri
Route::post($uri, $callback);
// Imposta la route di richiesta PUT per $uri
Route::put($uri, $callback);
// Imposta la route di richiesta PATCH per $uri
Route::patch($uri, $callback);
// Imposta la route di richiesta DELETE per $uri
Route::delete($uri, $callback);
// Imposta la route di richiesta HEAD per $uri
Route::head($uri, $callback);
// Imposta simultaneamente la route per più tipi di richiesta
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Route raggruppate
Route::group($path, $callback);
// Route delle risorse
Route::resource($path, $callback, [$options]);
// Disabilita la route predefinita
Route::disableDefaultRoute($plugin = '');
// Route di fallback, imposta la route predefinita di fallback
Route::fallback($callback, $plugin = '');
```
Se non esiste una route per $uri (inclusa la route predefinita) e non è stata impostata la route di fallback, verrà restituito il codice di stato 404.

## File di configurazione del router multipli
Se si desidera gestire le route utilizzando più file di configurazione del router, ad esempio quando ogni applicazione ha il proprio file di configurazione delle route [multiapp](multiapp.md), è possibile caricare file di configurazione esterni con l'istruzione `require`.
Per esempio in `config/route.php`:
```php
<?php

// Carica il file di configurazione delle route dell'applicazione admin
require_once app_path('admin/config/route.php');
// Carica il file di configurazione delle route dell'applicazione api
require_once app_path('api/config/route.php');

```
