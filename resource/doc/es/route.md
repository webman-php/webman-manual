## Enrutamiento
## Reglas de enrutamiento predeterminadas
La regla de enrutamiento predeterminada de webman es `http://127.0.0.1:8787/{controlador}/{acción}`.

El controlador predeterminado es `app\controller\IndexController`, y la acción predeterminada es `índice`.

Por ejemplo, al acceder a:
- `http://127.0.0.1:8787` se accederá de forma predeterminada al método `índice` de la clase `app\controller\IndexController`.
- `http://127.0.0.1:8787/foo` se accederá de forma predeterminada al método `índice` de la clase `app\controller\FooController`.
- `http://127.0.0.1:8787/foo/test` se accederá de forma predeterminada al método `test` de la clase `app\controller\FooController`.
- `http://127.0.0.1:8787/admin/foo/test` se accederá de forma predeterminada al método `test` de la clase `app\admin\controller\FooController` (consulte [aplicaciones múltiples](multiapp.md)).

Además, a partir de la versión 1.4 de webman, se admiten reglas de enrutamiento predeterminadas más complejas, por ejemplo:
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

Cuando desee cambiar una ruta de solicitud, modifique el archivo de configuración `config/route.php`.

Si desea deshabilitar el enrutamiento predeterminado, agregue la siguiente configuración a la última línea del archivo de configuración `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Enrutamiento de cierre
Agregue el siguiente código de enrutamiento al archivo `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Nota**
> Dado que la función de cierre no pertenece a ningún controlador, `$request->app`, `$request->controller` y `$request->action` estarán todos vacíos.

Cuando la dirección de acceso es `http://127.0.0.1:8787/test`, devolverá la cadena `test`.

> **Nota**
> La ruta de enrutamiento debe comenzar con `/`, por ejemplo:

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

## Enrutamiento de clase
Agregue el siguiente código de enrutamiento al archivo `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Cuando la dirección de acceso es `http://127.0.0.1:8787/testclass`, devolverá el valor devuelto del método `test` de la clase `app\controller\IndexController`.

## Parámetros de enrutamiento
Si hay parámetros en la ruta, se pueden emparejar con `{clave}` y el resultado del emparejamiento se pasará como argumento al método del controlador correspondiente (a partir del segundo argumento), por ejemplo:
```php
// Emparejar /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Recibido el parámetro'.$id);
    }
}
```

Más ejemplos:
```php
// Emparejar /user/123, no emparejar /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Emparejar /user/foobar, no emparejar /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Emparejar /user /user/123 y /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Emparejar todas las solicitudes de opciones
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Grupos de enrutamiento
A veces, las rutas contienen un gran número de prefijos similares, en estos casos, podemos usar grupos de enrutamiento para simplificar la definición. Por ejemplo:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("ver $id");});
});
```
Es equivalente a
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("ver $id");});
```

Uso anidado de grupos
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("ver $id");});
   });  
});
```

## Middlewares de enrutamiento
Podemos establecer middlewares para una o un grupo de rutas específicas.
Por ejemplo:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'índice'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('crear');});
   Route::any('/edit', function () {return response('editar');});
   Route::any('/view/{id}', function ($request, $id) {response("ver $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Nota**:
> En webman-framework <= 1.5.6, cuando el middleware de enrutamiento `->middleware()` se aplica después del grupo, la ruta actual debe estar dentro de ese grupo específico.

```php
# Ejemplo de uso incorrecto (este uso es válido en webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('crear');});
      Route::any('/edit', function ($request) {return response('editar');});
      Route::any('/view/{id}', function ($request, $id) {return response("ver $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Ejemplo de uso correcto
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('crear');});
      Route::any('/edit', function ($request) {return response('editar');});
      Route::any('/view/{id}', function ($request, $id) {return response("ver $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## Enrutamiento de recursos
```php
Route::resource('/test', app\controller\IndexController::class);

//Ruta de recurso específica
Route::resource('/test', app\controller\IndexController::class, ['índice','crear']);

//Ruta de recurso no definida
// Si accede a notify, la dirección será cualquier ruta /test/notify o /test/notify/{id}, y el nombre de la ruta será test.notify
Route::resource('/test', app\controller\IndexController::class, ['índice','crear','notify']);
```
| Verbo  | URI                 | Acción   | Nombre de la ruta |
|--------|---------------------|----------|-------------------|
| GET    | /test               | índice   | test.index        |
| GET    | /test/crear         | crear    | test.create       |
| POST   | /test               | almacenar| test.store        |
| GET    | /test/{id}          | mostrar  | test.show         |
| GET    | /test/{id}/editar   | editar   | test.edit         |
| PUT    | /test/{id}          | actualizar| test.update       |
| DELETE | /test/{id}          | destruir | test.destroy      |
| PUT    | /test/{id}/recuperación | recuperación | test.recovery |

## Generación de URL
> **Nota**
> La generación de rutas anidadas actualmente no es compatible.

Por ejemplo, con la ruta:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'ver'])->name('blog.view');
```
Podemos usar el siguiente método para generar la URL de esta ruta.
```php
route('blog.view', ['id' => 100]); // Resulta en /blog/100
```

Cuando se utiliza este método para generar la URL de una ruta en una vista, independientemente de cómo cambie la regla de enrutamiento, la URL se generará automáticamente, evitando así la necesidad de realizar cambios masivos en los archivos de vista debido a cambios en las direcciones de enrutamiento.
## Obtener información de la ruta
> **Nota**
> Se requiere webman-framework >= 1.3.2

A través del objeto `$request->route`, podemos obtener información sobre la ruta actual. Por ejemplo:

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
> Si la solicitud actual no coincide con ninguna de las rutas configuradas en `config/route.php`, entonces `$request->route` será null, lo que significa que al usar la ruta predeterminada, `$request->route` será null.


## Manejo de error 404
Cuando no se encuentra la ruta, por defecto se devuelve el código de estado 404 y se muestra el contenido del archivo `public/404.html`.

Si un desarrollador desea intervenir en el flujo de negocio cuando no se encuentra la ruta, puede utilizar el método de ruta de recuperación proporcionado por webman `Route::fallback($callback)`. Por ejemplo, la lógica de código a continuación redirige a la página de inicio cuando no se encuentra la ruta.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Otro ejemplo sería devolver datos JSON cuando no se encuentra la ruta, lo cual es muy útil cuando se usa webman como una API.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Enlaces relacionados [Página de error personalizada 404 500](others/custom-error-page.md)

## Interfaz de ruta
```php
// Establecer la ruta para cualquier método de solicitud en $uri
Route::any($uri, $callback);
// Establecer la ruta GET en $uri
Route::get($uri, $callback);
// Establecer la ruta POST en $uri
Route::post($uri, $callback);
// Establecer la ruta PUT en $uri
Route::put($uri, $callback);
// Establecer la ruta PATCH en $uri
Route::patch($uri, $callback);
// Establecer la ruta DELETE en $uri
Route::delete($uri, $callback);
// Establecer la ruta HEAD en $uri
Route::head($uri, $callback);
// Establecer múltiples tipos de solicitud para la ruta simultáneamente
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Rutas de grupo
Route::group($path, $callback);
// Rutas de recurso
Route::resource($path, $callback, [$options]);
// Deshabilitar la ruta predeterminada
Route::disableDefaultRoute($plugin = '');
// Ruta de recuperación, establece la ruta predeterminada de respaldo
Route::fallback($callback, $plugin = '');
```
Si no hay una ruta correspondiente para la uri (incluida la ruta predeterminada) y no se ha establecido una ruta de recuperación, se devolverá un error 404.

## Múltiples archivos de configuración de ruta
Si deseas gestionar las rutas utilizando varios archivos de configuración de ruta, por ejemplo, en un [aplicación múltiple](multiapp.md) donde cada aplicación tiene su propio archivo de configuración de ruta, puedes cargar archivos de configuración de ruta externos utilizando `require`.
Por ejemplo, en `config/route.php`:
```php
<?php

// Cargar el archivo de configuración de ruta de la aplicación admin
require_once app_path('admin/config/route.php');
// Cargar el archivo de configuración de ruta de la aplicación api
require_once app_path('api/config/route.php');
```
