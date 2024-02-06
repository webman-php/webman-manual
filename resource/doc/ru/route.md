## Маршрутизация
## Стандартные правила маршрутизации
Стандартные правила маршрутизации в webman составляют `http://127.0.0.1:8787/{контроллер}/{действие}`.

Стандартный контроллер - `app\controller\IndexController`, стандартное действие - `index`.

Например, при посещении:
- `http://127.0.0.1:8787` будет вызван метод `index` класса `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` будет вызван метод `index` класса `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` будет вызван метод `test` класса `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` вызовет метод `test` класса `app\admin\controller\FooController` (см. [Мультиприложение](multiapp.md))

Также webman начиная с версии 1.4 поддерживает более сложные стандартные маршруты, например
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

Если вы хотите изменить маршрут запроса, вам следует изменить файл настроек `config/route.php`.

Если вы хотите отключить стандартную маршрутизацию, вы можете добавить следующую конфигурацию в файл настроек `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Замыкание маршрута
Добавьте следующий код маршрута в файл `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Примечание**
> Поскольку замыкание не принадлежит никакому контроллеру, все переменные `$request->app`, `$request->controller` и `$request->action` будут пустыми строками.

При посещении адреса `http://127.0.0.1:8787/test` будет возвращена строка `test`.

> **Примечание**
> Путь маршрута должен начинаться с `/`, например

```php
// Неправильное использование
Route::any('test', function ($request) {
    return response('test');
});

// Правильное использование
Route::any('/test', function ($request) {
    return response('test');
});
```


## Маршрут класса
Добавьте следующий код маршрута в файл `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
При посещении адреса `http://127.0.0.1:8787/testclass` будет возвращено значение метода `test` класса `app\controller\IndexController`.

## Параметры маршрута
Если в маршруте есть параметры, они соответствуют шаблону `{ключ}`, и результат соответствия будет передан в соответствующие параметры метода контроллера (начиная с второго параметра), например:
```php
// Соответствие /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Полученный параметр ' . $id);
    }
}
```

Дополнительные примеры:
```php
// Соответствие /user/123, но не /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Соответствие /user/foobar, но не /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Соответствие /user, /user/123 и /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Соответствие для всех запросов options
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Групповой маршрут
Иногда маршруты содержат много одинаковых префиксов, в таком случае мы можем использовать групповой маршрут для упрощения определения. Например:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})
```
равносильно
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Использование вложенных групповых маршрутов

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})
```

## Промежуточные маршруты
Мы можем настроить промежуточные маршруты для определенного маршрута или группы маршрутов.
Например:
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

> **Примечание**:
> В webman-framework <= 1.5.6 когда метод `->middleware()` применяется к групповым маршрутам, текущий маршрут должен находиться внутри этой группы маршрутов.

```php
# Неправильный пример (этот метод действителен с webman-framework >= 1.5.7)
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
# Правильный пример
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

## Маршруты ресурсов
```php
Route::resource('/test', app\controller\IndexController::class);

// Указание маршрута ресурсов
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Маршрут ресурсов, не имеющий определения
// Например, при обращении по адресу notify запрос будет any-типа и/или иметь форму /test/notify или /test/notify/{id}. Имя маршрута - test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Глагол   | URI                 | Действие      | Имя маршрута    |
|----------|---------------------|---------------|-----------------|
| GET      | /test               | index         | test.index      |
| GET      | /test/create        | create        | test.create     |
| POST     | /test               | store         | test.store      |
| GET      | /test/{id}          | show          | test.show       |
| GET      | /test/{id}/edit     | edit          | test.edit       |
| PUT      | /test/{id}          | update        | test.update     |
| DELETE   | /test/{id}          | destroy       | test.destroy    |
| PUT      | /test/{id}/recovery | recovery      | test.recovery   |

## Генерация URL
> **Примечание** 
> В настоящее время не поддерживается генерация вложенных групповых маршрутов

Например, для маршрута:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
мы можем использовать следующий метод для генерации URL для этого маршрута.
```php
route('blog.view', ['id' => 100]); // Результат будет /blog/100
```

Когда адреса маршрутов используются в представлениях, использование этого метода позволит автоматически генерировать URL, что предотвратит необходимость внесения изменений в файлы представлений при изменении адресов маршрутов.

## Получение информации о маршруте
> **Примечание**
> Требуется webman-framework >= 1.3.2

Через объект `$request->route` мы можем получить информацию о текущем маршруте, например

```php
$route = $request->route; // Эквивалентно $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Для этой функции необходим webman-framework >= 1.3.16
}
```

> **Примечание**
> Если в текущем запросе не находится ни одного совпадения с настроенными маршрутами в config/route.php, тогда `$request->route` будет равен null, то есть при использовании стандартной маршрутизации `$request->route` будет равен null.

## Обработка 404
При отсутствии совпадения маршрута по умолчанию возвращается код 404 и выводится содержимое файла `public/404.html`.

Если разработчик хочет вмешаться, когда маршрут не найден, он может использовать метод отката маршрута `Route::fallback($callback)`. Например, следующая логика перенаправляет на главную страницу, если маршрут не найден.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Например, если маршрут не найден, вернуть JSON-данные, это очень удобно для использования webman в качестве API-интерфейса.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Ссылка на связанный документ [Пользовательская страница 404 500 ошибок](others/custom-error-page.md)
## Маршрутинг интерфейса
```php
// Устанавливает маршрут для любого метода запроса $uri
Route::any($uri, $callback);
// Устанавливает маршрут для GET-запроса $uri
Route::get($uri, $callback);
// Устанавливает маршрут для POST-запроса $uri
Route::post($uri, $callback);
// Устанавливает маршрут для PUT-запроса $uri
Route::put($uri, $callback);
// Устанавливает маршрут для PATCH-запроса $uri
Route::patch($uri, $callback);
// Устанавливает маршрут для DELETE-запроса $uri
Route::delete($uri, $callback);
// Устанавливает маршрут для HEAD-запроса $uri
Route::head($uri, $callback);
// Устанавливает маршрут для нескольких типов запросов одновременно
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Группировка маршрутов
Route::group($path, $callback);
// Ресурсные маршруты
Route::resource($path, $callback, [$options]);
// Отключение маршрутов по умолчанию
Route::disableDefaultRoute($plugin = '');
// Резервный маршрут, устанавливает маршрут по умолчанию
Route::fallback($callback, $plugin = '');
```
Если для uri нет соответствующих маршрутов (включая маршруты по умолчанию), и резервный маршрут не установлен, то будет возвращен код 404.

## Несколько файлов конфигурации маршрутов
Если вам нужно управлять маршрутами с использованием нескольких файлов конфигураций маршрутов, например, при [использовании нескольких приложений](multiapp.md), где у каждого приложения есть свой файл конфигурации маршрутов, вы можете загружать внешние файлы конфигураций маршрутов с помощью `require`.
Пример из файла `config/route.php`:
```php
<?php

// Загрузка файла конфигурации маршрутов для приложения admin
require_once app_path('admin/config/route.php');
// Загрузка файла конфигурации маршрутов для приложения api
require_once app_path('api/config/route.php');

```
