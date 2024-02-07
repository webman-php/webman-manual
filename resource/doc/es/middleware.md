# Middlewares
Los middlewares generalmente se utilizan para interceptar solicitudes o respuestas. Por ejemplo, para verificar la identidad del usuario de manera unificada antes de ejecutar el controlador, como redirigir a la página de inicio de sesión si el usuario no ha iniciado sesión, o para agregar una cabecera específica a la respuesta. También se pueden utilizar para realizar estadísticas de la proporción de solicitudes para una URI determinada, entre otras cosas.

## Modelo de cebolla de middleware
```plaintext
                                          
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Solicitud ───────────────────────> Controlador ─ Respuesta ───────────────────────────> Cliente
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Los middlewares y los controladores forman un modelo clásico de cebolla, donde los middlewares son como las capas externas de una cebolla y el controlador es el núcleo de la cebolla. Como se muestra en el diagrama, la solicitud atraviesa los middlewares 1, 2, 3 para llegar al controlador. El controlador devuelve una respuesta y luego la respuesta atraviesa los middleware en el orden inverso (3, 2, 1) antes de ser devuelta al cliente. Es decir, en cada middleware podemos obtener tanto la solicitud como la respuesta.

## Intercepción de solicitudes
A veces queremos evitar que una solicitud llegue a la capa del controlador, por ejemplo, si en el middleware 2 descubrimos que el usuario actual no ha iniciado sesión, entonces podemos interceptar directamente la solicitud y devolver una respuesta de inicio de sesión. El proceso sería similar al siguiente diagrama:

```plaintext
                                          
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Solicitud ─────────┐   │    │    Controlador   │      │      │     │
            │     │ Respuesta│    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

Como se muestra en el diagrama, la solicitud llega al middleware 2 y se genera una respuesta de inicio de sesión. La respuesta atraviesa el middleware 1 y luego se devuelve al cliente.

## Interfaz de middleware
Los middlewares deben implementar la interfaz `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Procesa una solicitud entrante del servidor.
     *
     * Procesa una solicitud entrante del servidor para producir una respuesta.
     * Si no puede producir la respuesta por sí mismo, puede delegar al controlador de solicitud proporcionado para que lo haga.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Esto significa que deben implementar el método `process`, el cual debe devolver un objeto `support\Response`. Por defecto, este objeto es generado por `$handler($request)` (la solicitud continuará a través de la cebolla), aunque también puede ser generado por las funciones auxiliares `response()`, `json()`, `xml()` o `redirect()` (la solicitud se detiene y no continúa a través de la cebolla).

## Obtener la solicitud y la respuesta en el middleware
Dentro del middleware, podemos obtener tanto la solicitud como la respuesta después de que se ha ejecutado el controlador. Por lo tanto, el middleware se divide en tres partes internas:
1. Fase de atravesar la solicitud, es decir, antes de procesar la solicitud
2. Fase de procesar la solicitud del controlador, es decir, durante el procesamiento de la solicitud
3. Fase de atravesar la respuesta, es decir, después de procesar la solicitud

Estas tres fases se reflejan en el middleware de la siguiente manera:
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
        echo 'Esta es la fase de atravesar la solicitud, es decir, antes del procesamiento de la solicitud';
        
        $response = $handler($request); // Continuar a través de la cebolla, hasta que el controlador genere una respuesta
        
        echo 'Esta es la fase de atravesar la respuesta, es decir, después del procesamiento de la solicitud';
        
        return $response;
    }
}
```
## Ejemplo: Middleware de autenticación
Cree el archivo `app/middleware/AuthCheckTest.php` (si el directorio no existe, créelo usted mismo) como se muestra a continuación:
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
            // Ya ha iniciado sesión, el pedido continúa atravesando las capas de la cebolla
            return $handler($request);
        }

        // Obtener mediante reflexión los métodos del controlador que no requieren inicio de sesión
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // El método solicitado requiere inicio de sesión
        if (!in_array($request->action, $noNeedLogin)) {
            // Intercepta la solicitud, devuelve una respuesta de redirección y detiene la penetración a través de las capas de la cebolla
            return redirect('/user/login');
        }

        // No se requiere inicio de sesión, la solicitud continúa atravesando las capas de la cebolla
        return $handler($request);
    }
}
```

Cree el controlador `app/controller/UserController.php`:
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Métodos que no requieren inicio de sesión
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'inicio de sesión exitoso']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **Nota**
> La variable `$noNeedLogin` registra los métodos a los que se puede acceder en el controlador sin necesidad de iniciar sesión.

Agregue el middleware global en `config/middleware.php` de la siguiente manera:
```php
return [
    // Middleware global
    '' => [
        // ... otros middlewares omitidos aquí
        app\middleware\AuthCheckTest::class,
    ]
];
```

Con el middleware de autenticación, podemos enfocarnos en escribir el código empresarial en la capa del controlador sin preocuparnos por si el usuario ha iniciado sesión o no.

## Ejemplo: Middleware de solicitud de origen cruzado (CORS)
Cree el archivo `app/middleware/AccessControlTest.php` (si el directorio no existe, créelo usted mismo) como se muestra a continuación:
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
        // Si es una solicitud OPTIONS devuelve una respuesta vacía, de lo contrario continúa atravesando las capas de la cebolla y obtiene una respuesta
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Agrega encabezados HTTP relacionados con CORS a la respuesta
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

> **Nota**
> Las solicitudes de origen cruzado pueden generar solicitudes OPTIONS. No queremos que las solicitudes OPTIONS lleguen al controlador, por lo que devolvemos una respuesta vacía directamente (`response('')`) para interceptar la solicitud. Si su API necesita configurar rutas, use `Route::any(..)` o `Route::add(['POST', 'OPTIONS'], ..)`.

Agregue el middleware global en `config/middleware.php` de la siguiente manera:
```php
return [
    // Middleware global
    '' => [
        // ... otros middlewares omitidos aquí
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Nota**
> Si la solicitud AJAX personaliza encabezados, es necesario agregar el campo `Access-Control-Allow-Headers` con este encabezado personalizado en el middleware, de lo contrario se generará el mensaje de error `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## Notas
- Los middlewares se dividen en middlewares globales, middlewares de aplicación (solo son efectivos en modo de múltiples aplicaciones, consulte [Múltiples aplicaciones](multiapp.md)) y middlewares de ruta.
- Actualmente no es compatible con los middlewares de controladores individuales (pero puede implementar funcionalidades similares a los middlewares de controladores mediante la verificación de `$request->controller` en el middleware).
- La ubicación del archivo de configuración de middlewares es `config/middleware.php`.
- La configuración de middlewares globales se encuentra en la clave `''`.
- La configuración de middlewares de aplicación se encuentra en el nombre de la aplicación específica, por ejemplo:
```php
return [
    // Middleware global
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware de aplicación 'api' (los middlewares de aplicación solo son efectivos en el modo de múltiples aplicaciones)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware de ruta
Podemos asignar middlewares a una ruta específica o a un grupo de rutas. Por ejemplo, agregue la siguiente configuración en `config/route.php`:
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

## Pasar parámetros a los middlewares a través del constructor
> **Nota**
> Esta característica requiere webman-framework >= 1.4.8

Después de la versión 1.4.8, el archivo de configuración admite la instanciación directa de middlewares o funciones anónimas, lo que facilita pasar parámetros al middleware a través del constructor. Por ejemplo, en `config/middleware.php`, también puede configurarlo de la siguiente manera:
```php
return [
    // Middleware global
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware de aplicación 'api' (los middlewares de aplicación solo son efectivos en el modo de múltiples aplicaciones)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Del mismo modo, los middlewares de ruta también pueden pasar parámetros al middleware a través del constructor. Por ejemplo, en `config/route.php`:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```
## Orden de ejecución de los middlewares
- El orden de ejecución de los middlewares es: `Middleware global` -> `Middleware de aplicación` -> `Middleware de ruta`.
- Cuando hay múltiples middlewares globales, se ejecutan en el orden configurado (lo mismo ocurre para los middlewares de aplicación y de ruta).
- Las solicitudes 404 no activan ningún middleware, incluidos los middleware globales.

## Pasar parámetros a los middlewares desde la ruta (route->setParams)

**Configuración de la ruta `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (suponiendo que es un middleware global)**
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
        // El valor predeterminado de $request->route es null, por lo que es necesario comprobar si $request->route está vacío
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```
## Pasar parámetros desde el middleware al controlador

A veces, el controlador necesita usar datos generados en el middleware. En este caso, podemos pasar parámetros al controlador agregando propiedades al objeto `$request`. Por ejemplo:

**Middleware:**
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

**Controlador:**
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

## Obtener información de la ruta actual desde el middleware
> **Nota**
> Requiere webman-framework >= 1.3.2

Podemos usar `$request->route` para obtener el objeto de la ruta y obtener información correspondiente llamando a los métodos respectivos.

**Configuración de ruta:**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Middleware:**
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
        // Si la solicitud no coincide con ninguna ruta (excepto la ruta predeterminada), $request->route será null
        // Suponiendo que la dirección a la que accede el navegador es /user/111, se imprimirá la siguiente información
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

> **Nota**
> El método `$route->param()` requiere webman-framework >= 1.3.16


## Obtener excepciones desde el middleware
> **Nota**
> Requiere webman-framework >= 1.3.15

Durante el proceso de manejo de la solicitud, es posible que se produzcan excepciones. Podemos usar `$response->exception()` en el middleware para obtener la excepción.

**Configuración de ruta:**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('prueba de excepción');
});
```

**Middleware:**
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

## Middleware global

> **Nota**
> Esta característica requiere webman-framework >= 1.5.16

El middleware global del proyecto principal solo afecta al proyecto principal y no afectará a los [plugins de la aplicación](app/app.md). A veces queremos agregar un middleware que afecte globalmente, incluyendo todos los plugins. En este caso, podemos usar el middleware global. 

Configuración en `config/middleware.php`:
```php
return [
    '@' => [ // Agregar middleware global al proyecto principal y a todos los plugins
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Añadir middleware global solo al proyecto principal
];
```

> **Nota**
> El middleware global `@` no solo se puede configurar en el proyecto principal, sino que también se puede configurar en algún plugin. Por ejemplo, si se configura el middleware global `@` en `plugin/ai/config/middleware.php`, también afectará al proyecto principal y a todos los plugins.


## Agregar middleware a un plugin

> **Nota**
> Esta característica requiere webman-framework >= 1.5.16

A veces queremos agregar un middleware a un [plugin de la aplicación](app/app.md), pero no queremos cambiar el código del plugin (ya que se sobrescribirá al actualizar). En este caso, podemos agregar el middleware al plugin desde el proyecto principal.

Configuración en `config/middleware.php`:
```php
return [
    'plugin.ai' => [], // Agregar middleware al plugin ai
    'plugin.ai.admin' => [], // Agregar middleware al módulo admin del plugin ai
];
```

> **Nota**
> Por supuesto, también se puede agregar una configuración similar en algún plugin para afectar a otros plugins. Por ejemplo, si se agrega la misma configuración en `plugin/foo/config/middleware.php`, afectará al plugin ai.
