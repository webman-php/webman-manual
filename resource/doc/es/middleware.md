# Middlewares
Los middlewares generalmente se utilizan para interceptar solicitudes o respuestas. Por ejemplo, para verificar la identidad del usuario de manera uniforme antes de ejecutar el controlador, como redireccionar a la página de inicio de sesión si el usuario no ha iniciado sesión, o para agregar una cabecera específica a la respuesta. También se pueden utilizar para realizar un seguimiento de la proporción de solicitudes para una URI específica, entre otros usos.

## Modelo de cebolla de middlewares

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── Solicitud ─────────────────> Controlador ─ Respuesta ───────────────────────────> Cliente
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Los middlewares y los controladores forman un modelo de cebolla clásico, donde los middlewares son capas concéntricas de la cebolla y el controlador es el núcleo de la cebolla. Como se muestra en el diagrama, la solicitud atraviesa los middlewares 1, 2 y 3 para llegar al controlador. El controlador devuelve una respuesta, la cual luego atraviesa los middlewares en el orden inverso (3, 2, 1) antes de llegar finalmente al cliente. Es decir, en cada middleware podemos acceder tanto a la solicitud como a la respuesta.

## Intercepción de solicitudes
A veces no queremos que una determinada solicitud llegue a la capa del controlador. Por ejemplo, si descubrimos a través de un middleware de autenticación de identidad que el usuario actual no ha iniciado sesión, podemos interceptar directamente la solicitud y devolver una respuesta de inicio de sesión. En este caso, el flujo sería similar al siguiente:

```
                              
            ┌────────────────────────────────────────────────────────────┐
            │                     middleware1                            │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │              Middleware de autenticación       │     │
            │     │           ┌──────────────────────────────┐     │     │
            │     │           │         middleware3          │     │     │       
            │     │           │     ┌──────────────────┐     │     │     │
            │     │           │     │                  │     │     │     │
   ── Solicitud ───────┐      │     │    Controlador   │     │     │     │
            │     │ Respuesta │     │                  │     │     │     │
   <───────────────────┘      │     └──────────────────┘     │     │     │
            │     │           │                              │     │     │
            │     │           └──────────────────────────────┘     │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

Como se muestra en el diagrama, la solicitud llega al middleware de autenticación, el cual genera una respuesta de inicio de sesión. La respuesta atraviesa el middleware 1 y luego se devuelve al navegador.

## Interfaz de middleware
Los middlewares deben implementar la interfaz `Webman\MiddlewareInterface`.
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
Esto significa que deben implementar el método `process`. Este método debe devolver un objeto `Webman\Http\Response`. Por defecto, este objeto es generado por `$handler($request)` (la solicitud continúa atravesando las capas de la cebolla), pero también puede ser generado por las funciones auxiliares `response()`, `json()`, `xml()` o `redirect()`, deteniendo así el avance de la solicitud a través de las capas de la cebolla.

## Obtención de solicitudes y respuestas en los middlewares
En los middlewares, es posible obtener tanto la solicitud como la respuesta generada después de ejecutar el controlador. Por lo tanto, el middleware se divide internamente en tres partes.
1. Fase de atravesar la solicitud, es decir, la fase antes del procesamiento de la solicitud.
2. Fase de procesamiento de la solicitud por el controlador.
3. Fase de salida de la respuesta, es decir, la fase después de procesar la solicitud.

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
        echo 'Esta es la fase de atravesar la solicitud, es decir, antes del procesamiento de la solicitud.';
        
        $response = $handler($request); // La solicitud continúa atravesando las capas de cebolla, hasta que se obtiene la respuesta del controlador.
        
        echo 'Esta es la fase de salida de la respuesta, es decir, después del procesamiento de la solicitud.';
        
        return $response;
    }
}
```
## Ejemplo: Middleware de autenticación
Cree el archivo `app/middleware/AuthCheckTest.php` (si el directorio no existe, créelo) de la siguiente manera:

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
            // Ya ha iniciado sesión, continúa el procesamiento de la solicitud
            return $handler($request);
        }

        // Obtener los métodos del controlador que no requieren autenticación a través de reflexión
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // El método al que se intenta acceder requiere autenticación
        if (!in_array($request->action, $noNeedLogin)) {
            // Interrumpir la solicitud y redirigir a una respuesta de redirección, deteniendo el procesamiento de la solicitud
            return redirect('/user/login');
        }

        // No se requiere autenticación, continúa el procesamiento de la solicitud
        return $handler($request);
    }
}
```

Cree un controlador `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Métodos que no requieren autenticación
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
> `$noNeedLogin` registra los métodos que se pueden acceder en el controlador actual sin necesidad de autenticación.

Agregue un middleware global en `config/middleware.php` de la siguiente manera:
```php
return [
    // Middleware global
    '' => [
        // ... Otros middlewares omitidos
        app\middleware\AuthCheckTest::class,
    ]
];
```

Con el middleware de autenticación, podemos enfocarnos en escribir el código comercial en la capa del controlador sin preocuparnos por si el usuario está iniciado sesión o no.

## Ejemplo: Middleware de solicitud de origen cruzado
Cree el archivo `app/middleware/AccessControlTest.php` (si el directorio no existe, créelo) de la siguiente manera:

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
        // Si es una solicitud de opciones, devuelve una respuesta vacía, de lo contrario, continúa el procesamiento de la solicitud y obtiene una respuesta
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Agrega encabezados HTTP relacionados con el origen cruzado a la respuesta
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

> **Sugerencia**
> Las solicitudes de origen cruzado pueden generar solicitudes OPTIONS. No queremos que las solicitudes OPTIONS lleguen al controlador, por lo que devolvemos directamente una respuesta vacía (`response('')`) para interceptar la solicitud.
> Si su API necesita rutas especificadas, use `Route::any(..)` o `Route::add(['POST', 'OPTIONS'], ..)` para configurarlas.

Agregue un middleware global en `config/middleware.php` de la siguiente manera:
```php
return [
    // Middleware global
    '' => [
        // ... Otros middlewares omitidos
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Nota**
> Si una solicitud AJAX personaliza los encabezados, debe incluir este encabezado personalizado en el campo `Access-Control-Allow-Headers` del middleware, de lo contrario, se generará un error `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response`.

## Explicación

- Los middlewares se dividen en middlewares globales, middlewares de aplicación (solo válidos en el modo de múltiples aplicaciones, consulte [Múltiples aplicaciones](multiapp.md)) y middlewares de rutas.
- Actualmente, no se admite el middleware individual del controlador (pero se puede lograr la funcionalidad del middleware del controlador mediante la verificación de `$request->controller` en el middleware).
- El archivo de configuración del middleware está en `config/middleware.php`.
- La configuración del middleware global está en la clave `''`.
- La configuración del middleware de la aplicación está bajo el nombre de la aplicación específica, por ejemplo:

```php
return [
    // Middleware global
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware de la aplicación "api" (solo válido en el modo de múltiples aplicaciones)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware de ruta

Se pueden asignar middlewares a una ruta específica o a un grupo de rutas.
Por ejemplo, agregue la siguiente configuración en `config/route.php`:

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

## Pasar parámetros al constructor del middleware

> **Nota**
> Esta característica requiere webman-framework >= 1.4.8

Después de la versión 1.4.8, el archivo de configuración admite la instanciación directa del middleware o funciones anónimas, lo que facilita pasar parámetros al constructor del middleware.
Por ejemplo, la configuración en `config/middleware.php` también se puede realizar de la siguiente manera:
```
return [
    // Middleware global
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware de la aplicación "api" (solo válido en el modo de múltiples aplicaciones)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

De manera similar, los middleware de ruta también pueden pasar parámetros al constructor del middleware. Por ejemplo, en `config/route.php`:

```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Orden de ejecución del middleware
- El orden de ejecución del middleware es `middleware global` -> `middleware de la aplicación` -> `middleware de la ruta`.
- Cuando hay varios middlewares globales, se ejecutan en el orden en que se configuraron en el middleware real (lo mismo para los middlewares de la aplicación y de la ruta).
- Las solicitudes 404 no activan ningún middleware, incluidos los middleware globales.

## Pasar parámetros a través de la ruta (route->setParams)

**Configuración de ruta `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (suponga que es un middleware global)**
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
        // Por defecto, $request->route es nulo, por lo que es necesario verificar si $request->route está vacío
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

 
## Pasar parámetros del middleware al controlador

A veces, el controlador necesita usar datos generados por el middleware. En este caso, podemos pasar los parámetros al controlador mediante la adición de propiedades al objeto `$request`. Por ejemplo:

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

## Obteniendo información de ruta actual en el middleware
> **Nota**
> Requiere webman-framework >= 1.3.2

Podemos utilizar `$request->route` para obtener el objeto de la ruta y obtener la información correspondiente llamando a los métodos respectivos.

**Configuración de la ruta**
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
        // Si la solicitud no coincide con ninguna ruta (excepto la ruta por defecto), entonces $request->route será null
        // Suponiendo que el navegador accede a la dirección /user/111, se imprimirá la siguiente información
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


## Obtener excepciones en el middleware
> **Nota**
> Requiere webman-framework >= 1.3.15

Durante el proceso de manejo de la solicitud, puede surgir una excepción. En el middleware, se puede usar `$response->exception()` para obtener la excepción.

**Configuración de la ruta**
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

Los middleware globales del proyecto principal solo afectan al proyecto principal y no afectarán a los [plugins de la aplicación](app/app.md). A veces queremos agregar un middleware que afecte globalmente, incluyendo todos los plugins, podemos usar un middleware global.

Configuración en `config/middleware.php`:
```php
return [
    '@' => [ // Agregar middleware global al proyecto principal y todos los plugins
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Agregar middleware global solo al proyecto principal
];
```

> **Nota**
> El middleware global `@` no solo se puede configurar en el proyecto principal, también puede configurarse en algún plugin, por ejemplo, si se configura el middleware global `@` en `plugin/ai/config/middleware.php`, también afectará al proyecto principal y a todos los plugins.

## Agregar middleware a un plugin específico

> **Nota**
> Esta característica requiere webman-framework >= 1.5.16

A veces queremos agregar un middleware a un [plugin de la aplicación](app/app.md), pero no queremos modificar el código del plugin (porque se sobrescribirá al actualizarse). En este caso, podemos configurar el middleware en el proyecto principal.

Configuración en `config/middleware.php`:
```php
return [
    'plugin.ai' => [], // Agregar middleware al plugin ai
    'plugin.ai.admin' => [], // Agregar middleware al módulo admin del plugin ai
];
```

> **Nota**
> Por supuesto, también se puede agregar una configuración similar en algún plugin para afectar a otros plugins, por ejemplo, si se agrega la configuración mencionada anteriormente en `plugin/foo/config/middleware.php`, afectaría al plugin ai.
