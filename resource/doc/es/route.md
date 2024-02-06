## Enrutamiento
## Reglas de enrutamiento por defecto
La regla de enrutamiento por defecto de webman es `http://127.0.0.1:8787/{controlador}/{acción}`.

El controlador por defecto es `app\controller\IndexController` y la acción por defecto es `index`.

Por ejemplo, al acceder a:
- `http://127.0.0.1:8787` se accederá por defecto al método `index` de la clase `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` se accederá por defecto al método `index` de la clase `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` se accederá por defecto al método `test` de la clase `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` se accederá por defecto al método `test` de la clase `app\admin\controller\FooController` (vea [Aplicaciones Múltiples](multiapp.md))

Además, a partir de la versión 1.4, webman admite enrutamientos por defecto más complejos, por ejemplo:
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

Si desea cambiar la ruta de una solicitud, modifique el archivo de configuración `config/route.php`.

Si desea deshabilitar el enrutamiento por defecto, agregue la siguiente configuración en la última línea del archivo de configuración `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Enrutamiento con funciones anónimas
Agregue el siguiente código de enrutamiento a `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Nota**
> Debido a que la función anónima no pertenece a ningún controlador, las variables `$request->app`, `$request->controller` y `$request->action` estarán todas vacías.

Al acceder a la dirección `http://127.0.0.1:8787/test`, se devolverá la cadena `test`.

> **Nota**
> La ruta de enrutamiento debe comenzar con `/`. Por ejemplo:

```php
// Uso incorrecto
Route::any('test', function ($request) {
    return response('test');
});

// Uso correcto
Route::any('/test', function ($request) {
    return response('test');
});
```


## Enrutamiento con clases
Agregue el siguiente código de enrutamiento a `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Al acceder a la dirección `http://127.0.0.1:8787/testclass`, se devolverá el valor devuelto del método `test` de la clase `app\controller\IndexController`.


## Parámetros de enrutamiento
Si hay parámetros en la ruta, pueden coincidir usando `{clave}` y el resultado coincidente se pasará como argumento al método del controlador correspondiente (a partir del segundo parámetro en adelante), por ejemplo:
```php
// Coincide con /user/123 y /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Parámetro recibido: '.$id);
    }
}
```

Más ejemplos:
```php
// Coincide con /user/123, no con /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Coincide con /user/foobar, no con /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Coincide con /user, /user/123 y /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Coincide con todas las peticiones de tipo options
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Grupos de enrutamiento
A veces, las rutas tienen muchos prefijos similares, en cuyo caso podemos usar grupos de enrutamiento para simplificar la definición. Por ejemplo:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Equivalente a
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Uso anidado de grupos


```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## Middleware de enrutamiento
Podemos agregar middleware a una sola ruta o a un grupo de rutas.
Por ejemplo:
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

> **Nota**:
> En webman-framework <= 1.5.6, cuando se usaba `->middleware()` en un grupo después de la función `Route::group`, la ruta actual debía estar dentro de dicho grupo para que el middleware tuviera efecto.

```php
# Ejemplo incorrecto (válido en webman-framework >= 1.5.7)
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
# Ejemplo correcto
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

## Enrutamiento de recursos
```php
Route::resource('/test', app\controller\IndexController::class);

// Rutas de recursos específicas
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Rutas de recursos no definidas
// Si se accede a notify, la ruta es /test/notify o /test/notify/{id} (ambas son válidas). routeName es test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verbo   | URI                 | Acción   | Nombre de Ruta    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## Generación de URL
> **Nota**
> La generación de URL no es compatible temporalmente con la generación de URL para rutas anidadas de grupos.

Por ejemplo, para la ruta:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Podemos usar el siguiente método para generar la URL de esta ruta.
```php
route('blog.view', ['id' => 100]); // Resultado: /blog/100
```

Cuando se utilizan URL de rutas en las vistas con este método, independientemente de cualquier cambio en las reglas de enrutamiento, las URL se generarán automáticamente, evitando tener que modificar los archivos de vista por cambios en las direcciones de enrutamiento.

## Obtener información de la ruta
> **Nota**
> Requiere webman-framework >= 1.3.2

A través del objeto `$request->route` podemos obtener información sobre la ruta de la solicitud actual, por ejemplo:

```php
$route = $request->route; // Equivalente a $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Esta característica requiere webman-framework >= 1.3.16
}
```

> **Nota**
> Si la solicitud actual no coincide con ninguna ruta definida en `config/route.php`, entonces `$request->route` será nulo, es decir, cuando se utiliza la ruta por defecto, `$request->route` será nulo.

## Manejo del error 404
Cuando no se encuentra una ruta, webman devolverá por defecto un código de estado 404 y mostrará el contenido del archivo `public/404.html`.

Si un desarrollador desea intervenir en el flujo de negocio cuando una ruta no se encuentra, puede utilizar el método de ruta de fallback de webman `Route::fallback($callback)`. Por ejemplo, el siguiente código redirige a la página de inicio cuando no se encuentra una ruta.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Otra alternativa sería devolver un dato en formato json cuando una ruta no se encuentre, lo cual es muy útil cuando webman se utiliza como una API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Enlaces relacionados [Personalizar página 404 y 500](others/custom-error-page.md)
## Interfaz de enrutamiento
```php
// Establece la ruta para cualquier método de solicitud en $uri
Route::any($uri, $callback);
// Establece la ruta para la solicitud GET en $uri
Route::get($uri, $callback);
// Establece la ruta para la solicitud POST en $uri
Route::post($uri, $callback);
// Establece la ruta para la solicitud PUT en $uri
Route::put($uri, $callback);
// Establece la ruta para la solicitud PATCH en $uri
Route::patch($uri, $callback);
// Establece la ruta para la solicitud DELETE en $uri
Route::delete($uri, $callback);
// Establece la ruta para la solicitud HEAD en $uri
Route::head($uri, $callback);
// Establece varias rutas para diferentes tipos de solicitud al mismo tiempo
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Rutas de grupo
Route::group($path, $callback);
// Rutas de recursos
Route::resource($path, $callback, [$options]);
// Deshabilitar la ruta predeterminada
Route::disableDefaultRoute($plugin = '');
// Ruta de fallback, establece la ruta predeterminada
Route::fallback($callback, $plugin = '');
```
Si no hay una ruta correspondiente para $uri (incluida la ruta predeterminada) y no se ha establecido una ruta de fallback, se devolverá un código 404.

## Múltiples archivos de configuración de ruta
Si desea administrar las rutas utilizando múltiples archivos de configuración de ruta, por ejemplo, [multiapp](multiapp.md) cuando cada aplicación tiene su propio archivo de configuración de ruta, puede cargar archivos de configuración de ruta externos utilizando `require`.
Por ejemplo, en `config/route.php`:
```php
<?php

// Carga el archivo de configuración de ruta de la aplicación admin
require_once app_path('admin/config/route.php');
// Carga el archivo de configuración de ruta de la aplicación api
require_once app_path('api/config/route.php');

```
