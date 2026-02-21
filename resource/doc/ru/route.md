## Маршрутизация
## Стандартные правила маршрутизации
Стандартным правилом маршрутизации для webman является `http://127.0.0.1:8787/{контроллер}/{действие}`.

Стандартный контроллер - `app\controller\IndexController`, стандартное действие - `index`.

Например, при доступе по следующим URL-адресам:
- `http://127.0.0.1:8787` будет вызван метод `index` класса `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` будет вызван метод `index` класса `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` будет вызван метод `test` класса `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` будет вызван метод `test` класса `app\admin\controller\FooController` (см. [Мультиприложение](multiapp.md))

Также начиная с версии 1.4, webman поддерживает более сложные стандартные маршруты, например:
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

Если вы хотите изменить маршрут для определенного запроса, измените файл конфигурации `config/route.php`.

Если вы хотите отключить стандартную маршрутизацию, добавьте следующую конфигурацию в конце файла конфигурации `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Замыкание маршрута
Добавьте следующий код маршрута в файл `config/route.php`:
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});
```
> **Примечание**
> Поскольку замыкание не относится к какому-либо контроллеру, `$request->app`, `$request->controller`, и `$request->action` будут равны пустой строке.

При доступе по адресу `http://127.0.0.1:8787/test` будет возвращена строка "test".

> **Примечание**
> Путь маршрута должен начинаться с `/`, например:

```php
use support\Request;
// Неправильное использование
Route::any('test', function (Request $request) {
    return response('test');
});

// Правильное использование
Route::any('/test', function (Request $request) {
    return response('test');
});
```

## Маршрут класса
Добавьте следующий код маршрута в файл `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
При доступе по адресу `http://127.0.0.1:8787/testclass` будет возвращено значение метода `test` класса `app\controller\IndexController`.

## Маршрутизация по аннотациям

Определяйте маршруты с помощью аннотаций на методах контроллера, без настройки в `config/route.php`.

> **Примечание**
> Для этой функции требуется webman-framework >= v2.2.0

### Базовое использование

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

Доступные аннотации: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (любой метод). Путь должен начинаться с `/`. Второй параметр может указывать имя маршрута, используемое в `route()` для генерации URL.

### Аннотации без параметров: ограничение HTTP-метода маршрута по умолчанию

Без пути ограничивает только разрешённые HTTP-методы для данного действия, при этом используется путь маршрута по умолчанию:

```php
#[Post]
public function create() { ... }  // Только POST разрешён, путь остаётся /user/create

#[Get]
public function index() { ... }   // Только GET разрешён
```

Можно комбинировать несколько аннотаций для разрешения нескольких методов запроса:

```php
#[Get]
#[Post]
public function form() { ... }  // Разрешены GET и POST
```

Методы, не объявленные в аннотациях, вернут 405.

Несколько аннотаций с путём регистрируют независимые маршруты: `#[Get('/a')] #[Post('/b')]` создаёт маршруты GET /a и POST /b.

### Префикс группы маршрутов

Используйте `#[RouteGroup]` в классе для добавления префикса ко всем маршрутам методов:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // Фактический путь /api/v1/user/{id}
    public function show($id) { ... }
}
```

### Пользовательские HTTP-методы и имя маршрута

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### Промежуточное ПО

`#[Middleware]` в контроллере или методе применяется к маршрутам с аннотациями, используется как `support\annotation\Middleware`.

## Параметры маршрута
Если в маршруте присутствуют параметры, они могут быть сопоставлены с помощью `{ключ}` и переданы в соответствующие аргументы метода контроллера (начиная со второго аргумента), например:
```php
// Сопоставление /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('Received parameter' . $id);
    }
}
```

Дополнительные примеры:
```php
use support\Request;
// Сопоставление /user/123, но не /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// Сопоставление /user/foobar, но не /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// Сопоставление /user /user/123 и /user/abc   [] означает опционально
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// Сопоставление любого запроса с префиксом /user/
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// Сопоставление всех запросов options   : указывает regex для именованного параметра
Route::options('[{path:.+}]', function () {
    return response('');
});
```

Резюме расширенного использования

> Синтаксис `[]` в маршрутах Webman в основном используется для опциональных частей пути или динамического сопоставления; позволяет определять более сложные структуры путей и правила сопоставления
>
> `:` используется для указания регулярного выражения


## Группировка маршрутов
Иногда маршруты содержат много общих префиксов, в таком случае можно использовать группировку маршрутов для упрощения определения. Например:
```php
use support\Request;
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('create');});
   Route::any('/edit', function (Request $request) {return response('edit');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
Эквивалентно:
```php
Route::any('/blog/create', function (Request $request) {return response('create');});
Route::any('/blog/edit', function (Request $request) {return response('edit');});
Route::any('/blog/view/{id}', function (Request $request, $id) {return response("view $id");});
```

Вложенное использование групп
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
});
```

## Промежуточные обработчики маршрутов
Мы можем назначить промежуточные обработчики для одного или нескольких маршрутов.
Например:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Пример неправильного использования (данная практика действительна для webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Пример правильного использования
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```
## Ресурсный маршрут
```php
Route::resource('/test', app\controller\IndexController::class);

// Указать ресурсный маршрут
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Неопределенный ресурсный маршрут
// Например, при доступе к адресу notify, это любой тип маршрута /test/notifyили/test/notify/{id} routeName - test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Глагол  | URI                | Действие | Имя маршрута |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## Генерация URL
> **Примечание** 
> В настоящее время не поддерживается генерация URL для вложенных маршрутов.

Например, маршрут:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Мы можем использовать следующий метод для генерации URL для этого маршрута.
```php
route('blog.view', ['id' => 100]); // Результат /blog/100
```

Когда используется URL маршрута в представлении, такой подход позволяет автоматически генерировать URL, избегая необходимости внесения изменений в файлы представлений при изменении правил маршрута.

## Получение информации о маршруте

С помощью объекта `$request->route` можно получить информацию о текущем маршруте запроса, например

```php
$route = $request->route; // Эквивалентно $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **Примечание**
> Если текущий запрос не соответствует ни одному из маршрутов, указанных в файле config/route.php, то `$request->route` будет равен null, то есть по умолчанию, когда маршрут не найден, `$request->route` равен null.

## Обработка ошибки 404
Когда маршрут не найден, по умолчанию возвращается код состояния 404 и выводится соответствующее содержимое 404.

Если разработчику нужно вмешаться в обработку ненайденного маршрута, он может использовать метод отката маршрута `Route::fallback($callback)`. Например, ниже приведен пример кода логики, когда маршрут не найден, перенаправляется на домашнюю страницу.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Например, если маршрут отсутствует, можно вернуть JSON-данные, что очень удобно при использовании webman в качестве API-интерфейса.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## Добавление middleware к 404

По умолчанию запросы 404 не проходят через какой-либо middleware. Если нужно добавить middleware к запросам 404, см. следующий код:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

Соответствующая ссылка [Настройка страниц ошибок 404 и 500](others/custom-error-page.md)

## Отключение маршрута по умолчанию

```php
// Отключить маршрут по умолчанию основного проекта, не влияет на плагины
Route::disableDefaultRoute();
// Отключить маршрут admin основного проекта, не влияет на плагины
Route::disableDefaultRoute('', 'admin');
// Отключить маршрут по умолчанию плагина foo, не влияет на основной проект
Route::disableDefaultRoute('foo');
// Отключить маршрут admin плагина foo, не влияет на основной проект
Route::disableDefaultRoute('foo', 'admin');
// Отключить маршрут по умолчанию контроллера [\app\controller\IndexController::class, 'index']
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## Аннотация отключения маршрута по умолчанию

Можно использовать аннотации для отключения маршрута по умолчанию контроллера:

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

Точно так же можно использовать аннотации для отключения маршрута по умолчанию метода контроллера:

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

## Маршрутный интерфейс
```php
// Установить маршрут с любым методом запроса для $uri
Route::any($uri, $callback);
// Установить маршрут с методом GET для $uri
Route::get($uri, $callback);
// Установить маршрут с методом POST для $uri
Route::post($uri, $callback);
// Установить маршрут с методом PUT для $uri
Route::put($uri, $callback);
// Установить маршрут с методом PATCH для $uri
Route::patch($uri, $callback);
// Установить маршрут с методом DELETE для $uri
Route::delete($uri, $callback);
// Установить маршрут с методом HEAD для $uri
Route::head($uri, $callback);
// Установить маршрут с методом OPTIONS для $uri
Route::options($uri, $callback);
// Установить маршрут с несколькими типами запросов
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Групповой маршрут
Route::group($path, $callback);
// Ресурсный маршрут
Route::resource($path, $callback, [$options]);
// Отключить маршрут по умолчанию
Route::disableDefaultRoute($plugin = '');
// Маршрут отката, установить маршрут по умолчанию
Route::fallback($callback, $plugin = '');
// Получить всю информацию о маршрутах
Route::getRoutes();
```
Если нет соответствующего маршрута для uri (включая маршрут по умолчанию), и маршрут отката не установлен, будет возвращен статус 404.

## Несколько файлов конфигурации маршрутов
Если вы хотите использовать несколько файлов конфигурации маршрутов для управления маршрутами, например, [многоаппаратность](multiapp.md), когда у каждого приложения есть собственный файл конфигурации маршрутов, вы можете загрузить внешние файлы конфигурации маршрутов с помощью `require`.
Например, в `config/route.php`
```php
<?php

// Загрузить файл конфигурации маршрута для приложения admin
require_once app_path('admin/config/route.php');
// Загрузить файл конфигурации маршрута для приложения api
require_once app_path('api/config/route.php');

```
