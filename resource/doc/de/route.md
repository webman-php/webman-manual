## Routen
## Standard-Routing-Regeln
Die Standard-Routing-Regel für webman ist `http://127.0.0.1:8787/{Controller}/{Aktion}`.

Der Standard-Controller ist `app\controller\IndexController`, und die Standardaktion ist `index`.

Beispiel für einen Zugriff:
- `http://127.0.0.1:8787` führt standardmäßig zur Methode `index` der Klasse `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` führt standardmäßig zur Methode `index` der Klasse `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` führt standardmäßig zur Methode `test` der Klasse `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` führt standardmäßig zur Methode `test` der Klasse `app\admin\controller\FooController` (siehe [Mehrere Anwendungen](multiapp.md))

Webman unterstützt seit Version 1.4 auch komplexere Standardrouten, wie zum Beispiel:
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

Wenn Sie die Standardroute für einen bestimmten Request ändern möchten, bearbeiten Sie bitte die Konfigurationsdatei `config/route.php`.

Falls Sie die Standardroute deaktivieren möchten, fügen Sie bitte folgende Konfiguration in die Konfigurationsdatei `config/route.php` ein:
```php
Route::disableDefaultRoute();
```

## Closure-Routen
Fügen Sie in der Datei `config/route.php` den folgenden Routen-Code hinzu:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Hinweis**
> Da Closure-Funktionen keinen bestimmten Controller haben, sind `$request->app`, `$request->controller` und `$request->action` alle leer.

Wenn Sie die Adresse `http://127.0.0.1:8787/test` aufrufen, wird der String `test` zurückgegeben.

> **Hinweis**
> Der Routenpfad muss mit `/` beginnen, zum Beispiel:

```php
// Falsche Verwendung
Route::any('test', function ($request) {
    return response('test');
});

// Richtige Verwendung
Route::any('/test', function ($request) {
    return response('test');
});
```

## Class-Routen
Fügen Sie in der Datei `config/route.php` den folgenden Routen-Code hinzu:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Wenn Sie die Adresse `http://127.0.0.1:8787/testclass` aufrufen, wird der Rückgabewert der Methode `test` der Klasse `app\controller\IndexController` zurückgegeben.

## Routenparameter
Wenn in der Route Parameter vorhanden sind, so werden diese mit `{Schlüssel}` zugewiesen und das Ergebnis wird an die entsprechenden Methodenparameter des Controllers übergeben (beginnend ab dem zweiten Parameter), zum Beispiel:
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
        return response('Parameter erhalten'.$id);
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

// Matcht alle Option-Anfragen
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Routen-Gruppierung
Manchmal enthalten Routen viele gleiche Präfixe. In solchen Fällen kann die Definition durch Routen-Gruppierung vereinfacht werden. Zum Beispiel:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Äquivalent zu
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Verschachtelte Gruppierungen:

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
Es ist möglich, einzelnen oder eine Gruppe von Routen Middleware zuzuweisen. Zum Beispiel:

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
> In webman-framework <= 1.5.6, wenn `->middleware()` auf eine Gruppe von Routen angewendet wird, muss die aktuelle Route in dieser Gruppe enthalten sein.

```php
# Falsches Beispiel (ab webman-framework >= 1.5.7 ist diese Verwendung gültig)
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

## Ressourcenorientierte Routen
```php
Route::resource('/test', app\controller\IndexController::class);

// Spezifische ressourcenorientierte Routen
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Nicht-standardmäßige ressourcenorientierte Routen
// Wenn beispielsweise auf die Adresse notify zugegriffen wird, ist eine any-Typ-Route mit /test/notify oder /test/notify/{id} zulässig. Die routeName wird test.notify sein.
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
> **Hinweis**: 
> Verschachtelte Gruppen-Routen für die URL-Generierung werden derzeit nicht unterstützt.

Beispiel-Route:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Sie können die URL dieser Route mit der folgenden Methode generieren:
```php
route('blog.view', ['id' => 100]); // Ergebnis: /blog/100
```

Wenn Sie beim Verwenden der Routen-URLs in Ansichten diese Methode verwenden, wird die URL automatisch generiert, unabhängig davon, wie sich die Routenadresse ändert, um Veränderungen in den Ansichtsdateien zu vermeiden.
## Routeninformationen abrufen
> **Hinweis**
> Erfordert webman-framework >= 1.3.2

Über das `$request->route`-Objekt können wir die aktuellen Routeninformationen der Anforderung abrufen, zum Beispiel

```php
$route = $request->route; // Gleichbedeutend mit $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Dieses Feature erfordert webman-framework >= 1.3.16
}
```

> **Hinweis**
> Wenn die aktuelle Anfrage keiner der Routen in der Konfigurationsdatei `config/route.php` entspricht, ist `$request->route` null, d.h. bei Verwendung der Standardroute ist `$request->route` null.


## Behandlung von 404-Fehlern
Wenn die Route nicht gefunden wird, wird standardmäßig der Statuscode 404 zurückgegeben und der Inhalt der Datei `public/404.html` ausgegeben.

Wenn ein Entwickler in den Geschäftsprozess eingreifen möchte, wenn die Route nicht gefunden wird, kann er die Methode `Route::fallback($callback)` von webman nutzen. Zum Beispiel leitet der folgende Code die Anfrage auf die Startseite um, wenn die Route nicht gefunden wird.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Beispielsweise kann bei einer API-Schnittstelle von webman ein JSON-Datenobjekt zurückgegeben werden, wenn die Route nicht vorhanden ist, was sehr nützlich ist.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Verknüpfung [Anpassung von 404- und 500-Seiten](others/custom-error-page.md)

## Routen-Schnittstelle
```php
// Legt eine Route für beliebige Methodenanfragen $uri fest
Route::any($uri, $callback);
// Legt eine Route für die GET-Anfrage $uri fest
Route::get($uri, $callback);
// Legt eine Route für die POST-Anfrage $uri fest
Route::post($uri, $callback);
// Legt eine Route für die PUT-Anfrage $uri fest
Route::put($uri, $callback);
// Legt eine Route für die PATCH-Anfrage $uri fest
Route::patch($uri, $callback);
// Legt eine Route für die DELETE-Anfrage $uri fest
Route::delete($uri, $callback);
// Legt eine Route für die HEAD-Anfrage $uri fest
Route::head($uri, $callback);
// Legt gleichzeitig Routen für mehrere Anfragetypen fest
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Gruppenroute
Route::group($path, $callback);
// Ressourcenroute
Route::resource($path, $callback, [$options]);
// Deaktiviert die Standardroute
Route::disableDefaultRoute($plugin = '');
// Fallback-Route, legt einen Standard-Fallback für die Route fest
Route::fallback($callback, $plugin = '');
```
Wenn es keine entsprechende Route für die URI gibt (einschließlich der Standardroute) und keine Fallback-Route festgelegt ist, wird ein 404 zurückgegeben.

## Mehrere Routenkonfigurationsdateien
Wenn Sie mehrere Routenkonfigurationsdateien zur Verwaltung der Routen verwenden möchten, z.B. [mehrere Anwendungen](multiapp.md), in denen jede Anwendung eine eigene Routenkonfiguration hat, können Sie externe Routenkonfigurationsdateien mit `require` laden.
Zum Beispiel in `config/route.php`
```php
<?php

// Lädt die Routenkonfiguration der Admin-Anwendung
require_once app_path('admin/config/route.php');
// Lädt die Routenkonfiguration der API-Anwendung
require_once app_path('api/config/route.php');

```
