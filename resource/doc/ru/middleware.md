# Промежуточное ПО
Промежуточное ПО обычно используется для перехвата запросов или ответов. Например, выполнение единой проверки подлинности пользователей перед выполнением контроллера, перенаправление на страницу входа, если пользователь не вошел в систему, добавление определенного заголовка в ответ и т. д. Например, подсчет процента запросов для определенного URI и многое другое.

## Модель «луковицы» промежуточного ПО

```plaintext
                                                      
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Запрос ─────────────────────> Контроллер ─ Ответ ───────────────────────────> Клиент
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Промежуточное ПО и контроллер образуют классическую модель "луковицы", где промежуточное ПО подобно слоям луковой шелухи, а контроллер - это сердцевина луковицы. Как показано на рисунке, запрос проходит через промежуточные слои 1, 2, 3 и достигает контроллера, затем контроллер возвращает ответ, который снова проходит через промежуточные слои в обратном порядке, прежде чем возвращается клиенту. Иными словами, в каждом промежуточном ПО мы можем получить как запрос, так и ответ.

## Перехват запросов
Иногда нам не нужно, чтобы определенный запрос достиг контроллера, например, если мы обнаруживаем в middleware2, что текущий пользователь не вошел в систему, то мы можем непосредственно перехватить запрос и вернуть ответ для входа. В таком случае процесс будет выглядеть примерно следующим образом:

```plaintext
                                                      
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Запрос ──────────┐     │    │    Controller    │      │      │     │
            │     │  Ответ   │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

Как показано на рисунке, запрос достиг middleware2, после чего генерируется ответ для входа, ответ проходит через middleware1 и возвращается клиенту.

## Интерфейс промежуточного ПО
Промежуточное ПО должно реализовывать интерфейс `Webman\MiddlewareInterface`.
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
Это означает, что необходимо реализовать метод `process`, который должен возвращать объект `support\Response`. По умолчанию этот объект генерируется при помощи `$handler($request)` (запрос будет продолжать проходить через сердцевину луковицы), также можно использовать вспомогательные функции для генерации ответа, такие как `response()`, `json()`, `xml()`, `redirect()` и т. д. (запрос будет прекращать продвижение через сердцевину луковицы).
## Получение запроса и ответа в промежуточном ПО

В промежуточном ПО мы можем получить запрос и получить ответ после выполнения контроллера, поэтому внутри промежуточного ПО есть три части.

1. Этап прохождения запроса, то есть этап перед обработкой запроса
2. Обработка запроса контроллером, то есть этап обработки запроса
3. Этап выхода ответа, то есть этап после обработки запроса

Отражение этих трех этапов в промежуточном ПО выглядит следующим образом
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
        echo 'Это этап прохождения запроса, то есть этап перед обработкой запроса';
        
        $response = $handler($request); // Продолжаем прохождение по луковице до получения ответа от контроллера
        
        echo 'Это этап выхода ответа, то есть этап после обработки запроса';
        
        return $response;
    }
}
```
 
## Пример: промежуточное ПО аутентификации

Создайте файл `app/middleware/AuthCheckTest.php` (если каталога нет, создайте его вручную) следующего содержания:
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
            // Пользователь уже вошел в систему, запрос продолжает проходить по луковице
            return $handler($request);
        }

        // Получаем методы контроллера, для которых не требуется вход
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Метод требует входа
        if (!in_array($request->action, $noNeedLogin)) {
            // Запрос перехвачен, возвращаем ответ о перенаправлении, прекращаем прохождение по луковице
            return redirect('/user/login');
        }

        // Вход не требуется, запрос продолжает прохождение по луковице
        return $handler($request);
    }
}
```

Создайте контроллер `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Методы, для которых не требуется вход
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

> **Примечание**
> В `$noNeedLogin` записаны методы текущего контроллера, к которым можно получить доступ без входа в систему

Добавьте глобальное промежуточное ПО в файле `config/middleware.php` следующим образом:
```php
return [
    // Глобальное промежуточное ПО
    '' => [
        // ... Здесь опущены другие промежуточные ПО
        app\middleware\AuthCheckTest::class,
    ]
];
```

С помощью промежуточного ПО аутентификации мы можем сосредоточиться на написании бизнес-логики в уровне контроллера, не беспокоясь о том, залогинен ли пользователь.

## Пример: промежуточное ПО для кросс-доменных запросов

Создайте файл `app/middleware/AccessControlTest.php` (если каталога нет, создайте его вручную) следующего содержания:
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
        // Если это запрос типа options, возвращаем пустой ответ, в противном случае продолжаем прохождение по луковице и получаем ответ
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Добавляем к ответу связанные с кросс-доменными запросами заголовки http
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

> **Примечание**
> Кросс-доменный запрос может вызвать запрос типа OPTIONS. Мы не хотим, чтобы запрос OPTIONS попадал в контроллер, поэтому для запроса OPTIONS мы возвращаем пустой ответ (`response('')`). Если вашему интерфейсу нужен маршрут, используйте `Route::any(..)` или установите `Route::add(['POST', 'OPTIONS'], ..)`.

Добавьте глобальное промежуточное ПО в файле `config/middleware.php` следующим образом:
```php
return [
    // Глобальное промежуточное ПО
    '' => [
        // ... Здесь опущены другие промежуточные ПО
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Примечание**
> Если ajax-запрос использовал заголовок, который нужно установить вручную, вам нужно добавить этот заголовок в поле `Access-Control-Allow-Headers` в промежуточном ПО, в противном случае произойдет ошибка `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## Пояснения
  
 - Промежуточное ПО разделяется на глобальное промежуточное ПО, промежуточное ПО приложения (действительно только в режиме множества приложений, см. [Множественные приложения](multiapp.md)) и промежуточное ПО маршрутизатора
 - В настоящее время не поддерживается промежуточное ПО для отдельного контроллера (но можно реализовать функционал промежуточного ПО в контроллере, используя проверку `if($request->controller)`)
 - Файл конфигурации промежуточного ПО находится в `config/middleware.php`
 - Глобальное промежуточное ПО настраивается под ключом `''`
 - Промежуточное ПО приложения настраивается в конкретном имени приложения, например

```php
return [
    // Глобальное промежуточное ПО
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Промежуточное ПО приложения api (действительно только в режиме множества приложений)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Промежуточное ПО маршрутизатора

Мы можем назначить промежуточное ПО для одного или нескольких маршрутов.
Например, в файле `config/route.php` добавим следующую конфигурацию:
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
## Конструкторы промежуточного слоя с передачей параметров

> **Примечание**
> Эта функция требует webman-framework >= 1.4.8

Начиная с версии 1.4.8, конфигурационный файл поддерживает прямую инстанциацию промежуточного слоя или анонимные функции, что позволяет удобно передавать параметры в промежуточный слой через конструктор.
Например, конфигурация в `config/middleware.php` может выглядеть следующим образом:
```php
return [
    // Глобальное промежуточное ПО
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Промежуточное ПО для приложения API (промежуточное ПО для приложений работает только в режиме множественных приложений)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Аналогично, промежуточное ПО маршрута также может передавать параметры через конструктор, например, в `config/route.php`:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Порядок выполнения промежуточного слоя
- Порядок выполнения промежуточного слоя следующий: `глобальное промежуточное ПО` -> `промежуточное ПО приложения` -> `промежуточное ПО маршрута`.
- При наличии нескольких глобальных промежуточных слоев они будут выполняться в порядке, определенном их фактической конфигурацией (аналогично для промежуточного ПО приложения и промежуточного ПО маршрута).
- Запросы 404 не вызовут выполнение ни одного промежуточного слоя, включая глобальное промежуточное ПО.

## Передача параметров в промежуточный слой маршрута (route->setParams)

**Конфигурация маршрута `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Промежуточное ПО (предположим, это глобальное промежуточное ПО)**
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
        // По умолчанию, $request->route будет равен null, поэтому нужно проверить, не пустой ли $request->route
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Передача параметров из промежуточного слоя в контроллер

Иногда контроллеру нужно использовать данные, созданные в промежуточном слое. В этом случае мы можем передать параметры в контроллер, добавляя свойства к объекту `$request`. Например:

**Промежуточный слой**
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

**Контроллер:**
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

## Получение информации о текущем маршруте в промежуточном слое

> **Примечание**
> Требуется webman-framework >= 1.3.2

Мы можем использовать `$request->route` для получения объекта маршрута и вызывать соответствующие методы для получения информации.

**Конфигурация маршрута**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Промежуточный слой**
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
        // Если запрос не соответствует ни одному маршруту (кроме маршрута по умолчанию), то $request->route будет равен null
        // Например, при посещении веб-адреса /user/111, будет выведена следующая информация
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

> **Примечание**
> Метод `$route->param()` требуется webman-framework >= 1.3.16

## Получение исключения промежуточным слоем

> **Примечание**
> Требуется webman-framework >= 1.3.15

В процессе обработки бизнес-логики может возникнуть исключение. В промежуточном слое можно использовать `$response->exception()` для получения исключения.

**Конфигурация маршрута**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**Промежуточный слой:**
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

## Суперглобальный промежуточный слой

> **Примечание**
> Эта функция требует webman-framework >= 1.5.16

Глобальное промежуточное ПО основного проекта влияет только на основной проект и не влияет на [приложения-плагины](app/app.md). Иногда мы хотим добавить промежуточный слой, который повлияет на все, включая все плагины, в этом случае можно использовать суперглобальный промежуточный слой.

В файле `config/middleware.php` настроим его следующим образом:
```php
return [
    '@' => [ // Добавляем глобальное промежуточное ПО для основного проекта и всех плагинов
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Добавляем глобальное промежуточное ПО только для основного проекта
];
```

> **Подсказка**
> Суперглобальное промежуточное ПО `@` можно добавлять не только в основном проекте, но и в каком-либо плагине, например, конфигурация `plugin/ai/config/middleware.php` содержит суперглобальное промежуточное ПО, что также повлияет на основной проект и все плагины.

## Добавление промежуточных слоев в определенный плагин

> **Примечание**
> Эта функция требует webman-framework >= 1.5.16

Иногда мы хотим добавить промежуточный слой для какого-то [приложения-плагина](app/app.md) без изменения кода плагина (поскольку обновление может привести к потере изменений), в этом случае мы можем настроить промежуточный слой в основном проекте для плагина.

В файле `config/middleware.php` настроим его следующим образом:
```php
return [
    'plugin.ai' => [], // Добавление промежуточного слоя для плагина ai
    'plugin.ai.admin' => [], // Добавление промежуточного слоя для модуля admin плагина ai
];
```

> **Подсказка**
> Конечно же, можно также добавить подобную настройку в каком-то плагине, влияющем на другие плагины. Например, добавив такую конфигурацию в `plugin/foo/config/middleware.php`, вы также повлияете на плагин ai.
