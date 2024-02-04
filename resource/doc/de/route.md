## Routen
## Standardroutenregeln
Die Standardroutenregel für webman ist `http://127.0.0.1:8787/{Controller}/{Aktion}`.

Der Standardcontroller ist `app\controller\IndexController`, die Standardaktion ist `index`.

Beispiel für den Zugriff:
- `http://127.0.0.1:8787` entspricht dem Aufruf der Methode `index` der Klasse `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` entspricht dem Aufruf der Methode `index` der Klasse `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` entspricht dem Aufruf der Methode `test` der Klasse `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` entspricht dem Aufruf der Methode `test` der Klasse `app\admin\controller\FooController` (siehe [Mehrere Anwendungen](multiapp.md))

Ab webman 1.4 unterstützt webman auch komplexere Standardrouten, zum Beispiel
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

Wenn Sie eine Anforderungsrouten ändern möchten, ändern Sie bitte die Konfigurationsdatei `config/route.php`.

Wenn Sie die Standardrouten deaktivieren möchten, fügen Sie folgende Konfiguration am Ende der Konfigurationsdatei `config/route.php` hinzu:
```php
Route::disableDefaultRoute();
```

## Closure-Routen
Fügen Sie in `config/route.php` folgenden Routencode hinzu:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Hinweis**
> Da Closure-Funktionen nicht zu einem Controller gehören, sind `$request->app`, `$request->controller` und `$request->action` alle leere Zeichenfolgen.

Wenn Sie die Adresse `http://127.0.0.1:8787/test` aufrufen, wird der Text `test` zurückgegeben.

> **Hinweis**
> Der Routenpfad muss mit `/` beginnen, zum Beispiel

```php
// Falsche Verwendung
Route::any('test', function ($request) {
    return response('test');
});

// Richtig
Route::any('/test', function ($request) {
    return response('test');
});
```

## Klassenrouten
Fügen Sie in `config/route.php` folgenden Routencode hinzu:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Wenn Sie die Adresse `http://127.0.0.1:8787/testclass` aufrufen, wird der Rückgabewert der Methode `test` der Klasse `app\controller\IndexController` zurückgegeben.

## Routenparameter
Wenn in der Route Parameter vorhanden sind, werden sie mit `{key}` gematcht und das Ergebnis wird an die entsprechenden Methodenparameter des Controllers übergeben (beginnend ab dem zweiten Parameter), zum Beispiel:
```php
// Matcht /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Empfangener Parameter'.$id);
    }
}
```

Weitere Beispiele:
```php
// Matcht /user/123, matcht nicht /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Matcht /user/foobar, matcht nicht /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Matcht /user, /user/123 und /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Matcht alle Options-Anfragen
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Routengruppen
Manchmal enthalten Routen viele gleiche Präfixe. In diesem Fall können Routengruppen verwendet werden, um die Definition zu vereinfachen. Zum Beispiel:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
}
```
Äquivalent zu
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Verschachtelte Verwendung von Gruppen

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## Routen-Middleware
Sie können einer oder einer Gruppe von Routen Middleware zuweisen.
Zum Beispiel:
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

> **Hinweis**: 
> In webman-framework <= 1.5.6 wird `->middleware()` auf die Gruppe angewendet, nachdem die Middleware auf die Gruppe angewendet wurde, müssen die aktuellen Routen unter dieser Gruppe sein.

```php
# Falsches Beispiel (diese Verwendung ist in webman-framework >= 1.5.7 gültig)
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
# Richtiges Beispiel
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

## Ressourcenrouten
```php
Route::resource('/test', app\controller\IndexController::class);

// Definieren Sie Ressourcenrouten
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Undefinierte Ressourcenrouten
// Wenn notify-Adresse besucht wird, ist es eine beliebige Route vom Typ /test/notify oder /test/notify/{id}, RouteName ist test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verb   | URI                 | Aktion   | Routenname    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## URL-Generierung
> **Hinweis**
> Die Generierung von verschachtelten Routen wird vorübergehend nicht unterstützt.

Beispielroute:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Wir können die folgende Methode verwenden, um die URL dieser Route zu generieren.
```php
route('blog.view', ['id' => 100]); // Resultat: /blog/100
```

Wenn Sie die URL der Route in den Ansichten verwenden, können Sie diese Methode verwenden, um sicherzustellen, dass die URL automatisch generiert wird, unabhängig von Änderungen an der Routenadresse, um eine große Anzahl von Änderungen an den Ansichtsdateien aufgrund von Routenadressänderungen zu vermeiden.

## Routeninformationen abrufen
> **Hinweis**
> Erfordert webman-framework >= 1.3.2

Über `$request->route`-Objekt können wir aktuelle Routeninformationen abrufen, zum Beispiel

```php
$route = $request->route; // Äquivalent zu $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Diese Funktion erfordert webman-framework >= 1.3.16
}
```

> **Hinweis**
> Wenn die aktuelle Anforderung keine Übereinstimmung mit einer der in `config/route.php` konfigurierten Routen hat, ist `$request->route` null, was bedeutet, dass bei Verwendung der Standardrouten `$request->route` null ist.

## 404 behandeln
Wenn die Route nicht gefunden wird, wird standardmäßig der HTTP-Statuscode 404 zurückgegeben und der Inhalt der Datei `public/404.html` ausgegeben.

Wenn Entwickler den Geschäftsablauf behandeln möchten, wenn die Route nicht gefunden wird, können sie die von webman bereitgestellte Fallback-Route `Route::fallback($callback)`-Methode verwenden. Zum Beispiel leitet der folgende Code den Benutzer zur Startseite um, wenn die Route nicht gefunden wird.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Ein weiteres Beispiel ist die Rückgabe von JSON-Daten, wenn die Route nicht gefunden wird. Dies ist besonders nützlich, wenn webman als API-Schnittstelle verwendet wird.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Verwandte Verknüpfungen [Benutzerdefinierte 404-500 Seiten](others/custom-error-page.md)
## Routen-Schnittstelle
```php
// Setze die Route für jede beliebige Methodenanfrage von $uri
Route::any($uri, $callback);
// Setze die Route für GET-Anfragen von $uri
Route::get($uri, $callback);
// Setze die Route für POST-Anfragen von $uri
Route::post($uri, $callback);
// Setze die Route für PUT-Anfragen von $uri
Route::put($uri, $callback);
// Setze die Route für PATCH-Anfragen von $uri
Route::patch($uri, $callback);
// Setze die Route für DELETE-Anfragen von $uri
Route::delete($uri, $callback);
// Setze die Route für HEAD-Anfragen von $uri
Route::head($uri, $callback);
// Setze gleichzeitig Routen für verschiedene Anfragearten
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Gruppiere Routen
Route::group($path, $callback);
// Ressourcenrouten
Route::resource($path, $callback, [$options]);
// Deaktiviere standardmäßige Routen
Route::disableDefaultRoute($plugin = '');
// Fallback-Route, setze die Standard-Route
Route::fallback($callback, $plugin = '');
```
Wenn keine Route für $uri vorhanden ist (einschließlich der Standardroute), und auch keine Fallback-Route festgelegt ist, wird ein 404-Fehler zurückgegeben.

## Mehrere Routenkonfigurationsdateien
Wenn Sie mehrere Routenkonfigurationsdateien zur Verwaltung von Routen verwenden möchten, z.B. [Mehrere Anwendungen](multiapp.md), wobei jede Anwendung ihre eigene Routenkonfiguration hat, können Sie externe Routenkonfigurationsdateien durch `require`-Anweisungen laden.
Zum Beispiel in `config/route.php`:
```php
<?php

// Lade die Routenkonfiguration der Anwendung "admin"
require_once app_path('admin/config/route.php');
// Lade die Routenkonfiguration der Anwendung "api"
require_once app_path('api/config/route.php');
```

