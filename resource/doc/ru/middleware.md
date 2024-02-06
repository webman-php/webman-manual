# Промежуточное ПО
Промежуточное ПО обычно используется для перехвата запросов или ответов. Например, выполнение проверки подлинности пользователя перед выполнением контроллера, например, перенаправление на страницу входа, если пользователь не авторизован, добавление определенного заголовка в ответ и т. д.

## Модель промежуточного ПО "Луковица"

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── Запрос ───────────────────────> Контроллер ─ Ответ ───────────────────────────> Клиент
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Промежуточное ПО и контроллер образуют классическую модель "Луковицы". Похоже, что запрос движется как стрела через middleware1, 2, 3 к контроллеру, который возвращает ответ, после чего ответ проходит обратно через middleware3, 2, 1 и, в конечном итоге, возвращается клиенту. Другими словами, в каждом промежуточном ПО мы можем получить запрос и получить ответ.

## Перехват запроса
Иногда мы не хотим, чтобы определенный запрос доходил до уровня контроллера. Например, если мы обнаружили в промежуточном ПО аутентификации пользователя, что текущий пользователь не вошел в систему, то мы можем напрямую перехватить запрос и вернуть ответ о входе в систему. Тогда этот процесс будет выглядеть примерно так:

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 ПО аутентификации пользователя   │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── Запрос ───────────┐   │     │       Контроллер      │     │     │
            │     │ Ответ　│     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

Как показано на рисунке, после того, как запрос достигает промежуточного ПО аутентификации, создается ответ о входе, и этот ответ проходит обратно через промежуточное ПО 1, а затем возвращается браузеру.

## Интерфейс промежуточного ПО
Промежуточное ПО должно реализовать интерфейс `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Обрабатывает входящий серверный запрос.
     *
     * Обрабатывает входящий серверный запрос для получения ответа.
     * Если не удается сформировать ответ самостоятельно, он может передать управление предоставленному
     * обработчику запроса для этого.
     */
    public function process(Request $request, callable $handler): Response;
}
```
То есть, необходимо реализовать метод `process`. Метод `process` должен возвращать объект `support\Response`. По умолчанию этот объект генерируется с помощью `$handler($request)` (запрос продолжает движение через "Луковицу"), а также может быть сгенерирован с помощью помощников функций `response()` `json()` `xml()` `redirect()` и т. д. (запрос останавливается на этом этапе движения через "Луковицу).

## Получение запроса и ответа в промежуточном ПО
В промежуточном ПО мы можем получить запрос и получить ответ после выполнения контроллера, поэтому внутри промежуточного ПО можно выделить три части.
1. Этап прохождения запроса, то есть этап перед обработкой запроса
2. Этап обработки запроса контроллером, то есть этап обработки запроса
3. Этап выхода ответа, то есть этап после обработки запроса

Три этапа в промежуточном ПО представлены следующим образом
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
        echo 'Здесь происходит прохождение запроса, то есть обработка запроса перед началом';
        
        $response = $handler($request); // Проход дальше через "Луковицу", пока не будет получен ответ от контроллера
        
        echo 'Здесь происходит выход ответа, то есть обработка запроса после его выполнения контроллером';
        
        return $response;
    }
}
```
## Пример: Промежуточное ПО аутентификации

Создайте файл `app/middleware/AuthCheckTest.php` (если каталога не существует, создайте его) следующим образом:

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
            // Уже вошел в систему, запрос продолжает проходить через луковичные слои
            return $handler($request);
        }

        // Получение методов контроллера, для которых не требуется аутентификация, с помощью рефлексии
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Требуется вход для доступа к методу
        if (!in_array($request->action, $noNeedLogin)) {
            // Перехват запроса, возврат перенаправления, остановка прохождения запроса через луковичные слои
            return redirect('/user/login');
        }

        // Не требуется вход, запрос продолжает проходить через луковичные слои
        return $handler($request);
    }
}
```

Создайте контроллер `app/controller/UserController.php`:

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
> В `$noNeedLogin` записаны методы текущего контроллера, к которым можно обращаться без входа в систему

Добавьте глобальное промежуточное ПО в `config/middleware.php`:

```php
return [
    // Глобальное промежуточное ПО
    '' => [
        // ... здесь опущены другие промежуточные ПО
        app\middleware\AuthCheckTest::class,
    ]
];
```

С помощью промежуточного ПО аутентификации мы можем сосредоточиться на написании бизнес-логики в слое контроллеров, не беспокоясь о том, вошел ли пользователь в систему.

## Пример: Промежуточное ПО для запросов с других доменов

Создайте файл `app/middleware/AccessControlTest.php` (если каталога не существует, создайте его):

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
        // Если это запрос типа OPTIONS, вернуть пустой ответ, иначе продолжить прохождение запроса через луковичные слои и получить ответ
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Добавление HTTP-заголовков для кросс-доменных запросов к ответу
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
> Кросс-доменные запросы могут вызывать запросы типа OPTIONS, чтобы предотвратить их попадание в контроллер, мы возвращаем пустой ответ (`response('')`) в случае запроса OPTIONS.
> Если вашему API требуется настройка маршрутов, используйте `Route::any(..)` или `Route::add(['POST', 'OPTIONS'], ..)`.

Добавьте глобальное промежуточное ПО в `config/middleware.php`:

```php
return [
    // Глобальное промежуточное ПО
    '' => [
        // ... здесь опущены другие промежуточные ПО
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Примечание**
> Если запрос Ajax настраивает заголовок, необходимо добавить этот настраиваемый заголовок в поле `Access-Control-Allow-Headers` в промежуточном ПО, в противном случае будет ошибка `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## Объяснение

- Промежуточные ПО делятся на глобальные, приложения (действуют только в многоприложенчном режиме, см. [Многоприложений](multiapp.md)) и маршрутные.
- В настоящее время не поддерживается промежуточное ПО для отдельных контроллеров (но можно реализовать аналогичную функциональность промежуточного ПО контроллера, проверяя `$request->controller` внутри промежуточного ПО).
- Файл конфигурации промежуточного ПО находится в `config/middleware.php`.
- Глобальное промежуточное ПО настраивается под ключом `''`.
- Конфигурации промежуточного ПО приложений настраиваются под именем конкретного приложения, например:

```php
return [
    // Глобальное промежуточное ПО
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Промежуточное ПО приложения api (действует только в многоприложенчном режиме)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Передача параметров в промежуточное ПО через конструктор

> **Примечание**
> Эта функция доступна в версии webman-framework >= 1.4.8

После версии 1.4.8 файл конфигурации поддерживает прямое создание экземпляров промежуточного ПО или анонимные функции, что позволяет удобно передавать параметры через конструктор в промежуточное ПО.
Например, в `config/middleware.php` можно настроить следующим образом:
```
return [
    // Глобальное промежуточное ПО
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Промежуточное ПО приложения api (действует только в многоприложенчном режиме)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Точно так же маршрутное промежуточное ПО может передавать параметры в промежуточное ПО через конструктор, например, в `config/route.php`:
```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Порядок выполнения промежуточного ПО

- Порядок выполнения промежуточного ПО следующий: `глобальное промежуточное ПО` -> `промежуточное ПО приложения` -> `маршрутное промежуточное ПО`.
- При наличии нескольких глобальных промежуточных ПО они выполняются в порядке их фактической конфигурации (то же относится и к промежуточному ПО приложений и маршрутному промежуточному ПО).
- При запросе 404 не запускается ни одно промежуточное ПО, включая глобальное промежуточное ПО.

## Передача параметров в маршрутное промежуточное ПО (route->setParams)

**Настройка маршрута в `config/route.php`**

```php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Промежуточное ПО (предположим, что это глобальное промежуточное ПО)**

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
        // $request->route по умолчанию равен null, поэтому нужно проверять, не пустой ли $request->route
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Передача параметров из промежуточного ПО в контроллер

Иногда контроллеру может понадобиться использовать данные, полученные из промежуточного ПО. В этом случае мы можем передать параметры в контроллер путем добавления свойства в объект `$request`. Например:

**Промежуточное ПО**
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
## Получение информации о текущем маршруте с помощью промежуточного ПО
> **Примечание**
> Требуется webman-framework >= 1.3.2

Мы можем использовать `$request->route` для получения объекта маршрута и вызывать соответствующие методы для получения соответствующей информации.

**Конфигурация маршрута**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Промежуточное ПО**
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
        // Если запрос не соответствует ни одному маршруту (за исключением маршрута по умолчанию), то $request->route будет равен null
        // Предположим, что адрес для доступа к браузеру /user/111, в этом случае будет выведена следующая информация
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
> Метод `$route->param()` требует webman-framework >= 1.3.16


## Получение исключения в промежуточном ПО
> **Примечание**
> Требуется webman-framework >= 1.3.15

В процессе обработки бизнеса может возникнуть исключение. В промежуточном ПО можно использовать `$response->exception()` для получения исключения.

**Конфигурация маршрута**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**Промежуточное ПО:**
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

## Глобальное промежуточное ПО

> **Примечание**
> Эта функция требует webman-framework >= 1.5.16

Глобальное промежуточное ПО основного проекта влияет только на основной проект и не влияет на [плагины приложений](app/app.md). Иногда мы хотим добавить промежуточное ПО, которое влияет на все плагины, включая основной проект, в этом случае мы можем использовать глобальное промежуточное ПО.

Настройте в `config/middleware.php` следующим образом:
```php
return [
    '@' => [ // Добавить глобальное промежуточное ПО для основного проекта и всех плагинов
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Добавить глобальное промежуточное ПО только для основного проекта
];
```

> **Подсказка**
> Глобальное промежуточное ПО `@` может быть настроено не только в основном проекте, но и в каком-либо плагине. Например, если настроить глобальное промежуточное ПО `@` в `plugin/ai/config/middleware.php`, это также повлияет на основной проект и все плагины.

## Добавление промежуточного ПО для определенного плагина

> **Примечание**
> Эта функция требует webman-framework >= 1.5.16

Иногда мы хотим добавить промежуточное ПО для [плагина приложения](app/app.md), но не хотим изменять его код (поскольку обновление будет затерто). В этом случае мы можем настроить промежуточное ПО для него в основном проекте.

Настройте в `config/middleware.php` следующим образом:
```php
return [
    'plugin.ai' => [], // Добавить промежуточное ПО для плагина ai
    'plugin.ai.admin' => [], // Добавить промежуточное ПО для модуля admin плагина ai
];
```

> **Подсказка**
> Конечно же, можно также добавить подобную конфигурацию в каком-либо плагине, чтобы повлиять на другие плагины, например, в `plugin/foo/config/middleware.php` добавьте такую же конфигурацию, это также повлияет на плагин ai.
