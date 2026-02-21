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
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});
```
> **Nota**
> Dado que la función de cierre no pertenece a ningún controlador, `$request->app`, `$request->controller` y `$request->action` estarán todos vacíos.

Cuando la dirección de acceso es `http://127.0.0.1:8787/test`, devolverá la cadena `test`.

> **Nota**
> La ruta de enrutamiento debe comenzar con `/`, por ejemplo:

```php
use support\Request;
// Uso incorrecto
Route::any('test', function (Request $request) {
    return response('test');
});

// Uso correcto
Route::any('/test', function (Request $request) {
    return response('test');
});
```

## Enrutamiento de clase
Agregue el siguiente código de enrutamiento al archivo `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
Cuando la dirección de acceso es `http://127.0.0.1:8787/testclass`, devolverá el valor devuelto del método `test` de la clase `app\controller\IndexController`.

## Enrutamiento por anotaciones

Defina rutas mediante anotaciones en métodos del controlador, sin necesidad de configurar en `config/route.php`.

> **Nota**
> Esta funcionalidad requiere webman-framework >= v2.2.0

### Uso básico

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

Anotaciones disponibles: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (cualquier método). La ruta debe comenzar con `/`. El segundo parámetro puede especificar el nombre de la ruta, usado por `route()` para generar URL.

### Anotaciones sin parámetros: restringir método HTTP en ruta predeterminada

Sin ruta, solo restringe los métodos HTTP permitidos para esa acción, siguiendo usando la ruta predeterminada:

```php
#[Post]
public function create() { ... }  // Solo permite POST, la ruta sigue siendo /user/create

#[Get]
public function index() { ... }   // Solo permite GET
```

Se pueden combinar múltiples anotaciones para permitir varios métodos de solicitud:

```php
#[Get]
#[Post]
public function form() { ... }  // Permite GET y POST
```

Los métodos no declarados en anotaciones devolverán 405.

Varias anotaciones con ruta registrarán rutas independientes: `#[Get('/a')] #[Post('/b')]` generará las rutas GET /a y POST /b.

### Prefijo de grupo de rutas

Use `#[RouteGroup]` en la clase para añadir prefijo a todas las rutas de métodos:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // Ruta real /api/v1/user/{id}
    public function show($id) { ... }
}
```

### Métodos HTTP personalizados y nombre de ruta

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### Middleware

`#[Middleware]` en controlador o método afecta las rutas por anotaciones, uso igual que `support\annotation\Middleware`.

## Parámetros de enrutamiento
Si hay parámetros en la ruta, se pueden emparejar con `{clave}` y el resultado del emparejamiento se pasará como argumento al método del controlador correspondiente (a partir del segundo argumento), por ejemplo:
```php
// Emparejar /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('Recibido el parámetro'.$id);
    }
}
```

Más ejemplos:
```php
use support\Request;
// Emparejar /user/123, no emparejar /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// Emparejar /user/foobar, no emparejar /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// Emparejar /user /user/123 y /user/abc   [] indica opcional
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// Emparejar cualquier solicitud con prefijo /user/
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// Emparejar todas las solicitudes de opciones   : indica regex para el parámetro nombrado
Route::options('[{path:.+}]', function () {
    return response('');
});
```

Resumen de uso avanzado

> La sintaxis `[]` en rutas de Webman se usa principalmente para partes opcionales o coincidencias dinámicas; permite definir estructuras de ruta más complejas
>
> `:` se usa para especificar expresión regular

## Grupos de enrutamiento
A veces, las rutas contienen un gran número de prefijos similares, en estos casos, podemos usar grupos de enrutamiento para simplificar la definición. Por ejemplo:

```php
use support\Request;
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('create');});
   Route::any('/edit', function (Request $request) {return response('edit');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
Es equivalente a
```php
Route::any('/blog/create', function (Request $request) {return response('create');});
Route::any('/blog/edit', function (Request $request) {return response('edit');});
Route::any('/blog/view/{id}', function (Request $request, $id) {return response("view $id");});
```

Uso anidado de grupos
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
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
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Ejemplo de uso incorrecto (este uso es válido en webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('crear');});
      Route::any('/edit', function (Request $request) {return response('editar');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
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
      Route::any('/create', function (Request $request) {return response('crear');});
      Route::any('/edit', function (Request $request) {return response('editar');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
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

A través del objeto `$request->route`, podemos obtener información sobre la ruta actual. Por ejemplo:

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
> Si la solicitud actual no coincide con ninguna de las rutas configuradas en `config/route.php`, entonces `$request->route` será null, lo que significa que al usar la ruta predeterminada, `$request->route` será null.


## Manejo de error 404
Cuando no se encuentra la ruta, por defecto se devuelve el código de estado 404 y se muestra el contenido 404 correspondiente.

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

## Añadir middleware a 404

Por defecto las solicitudes 404 no pasan por ningún middleware. Si necesita añadir middleware a las solicitudes 404, consulte el siguiente código:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

Enlaces relacionados [Página de error personalizada 404 500](others/custom-error-page.md)

## Deshabilitar ruta predeterminada

```php
// Deshabilitar la ruta predeterminada del proyecto principal, no afecta a los plugins
Route::disableDefaultRoute();
// Deshabilitar la ruta del admin del proyecto principal, no afecta a los plugins
Route::disableDefaultRoute('', 'admin');
// Deshabilitar la ruta predeterminada del plugin foo, no afecta al proyecto principal
Route::disableDefaultRoute('foo');
// Deshabilitar la ruta del admin del plugin foo, no afecta al proyecto principal
Route::disableDefaultRoute('foo', 'admin');
// Deshabilitar la ruta predeterminada del controlador [\app\controller\IndexController::class, 'index']
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## Anotación para deshabilitar ruta predeterminada

Podemos usar anotaciones para deshabilitar la ruta predeterminada de un controlador, por ejemplo:

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

Del mismo modo, también podemos usar anotaciones para deshabilitar la ruta predeterminada de un método del controlador, por ejemplo:

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
// Establecer la ruta OPTIONS en $uri
Route::options($uri, $callback);
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
// Obtener toda la información de rutas
Route::getRoutes();
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
