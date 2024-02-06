## Представление
По умолчанию webman использует синтаксис PHP в качестве шаблона с лучшей производительностью при включенном `opcache`. Помимо шаблонов PHP, webman также предоставляет шаблонные движки [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content).

## Включение opcache
При использовании представлений настоятельно рекомендуется включить опцию `opcache.enable` и `opcache.enable_cli` в php.ini, чтобы шаблонный движок работал наилучшим образом. 

## Установка Twig
1. Установка через composer

   `composer require twig/twig`
   
2. Измените файл конфигурации `config/view.php` следующим образом

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Подсказка**
> Другие параметры конфигурации передаются через опцию, например

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```

## Установка Blade
1. Установка через composer

   `composer require psr/container ^1.1.1 webman/blade`
   
2. Измените файл конфигурации `config/view.php` следующим образом

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Установка think-template
1. Установка через composer

   `composer require topthink/think-template`
   
2. Измените файл конфигурации `config/view.php` следующим образом

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Подсказка**
> Другие параметры конфигурации передаются через опцию, например

```php
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'view_suffix' => 'html',
        'tpl_begin' => '{',
        'tpl_end' => '}'
    ]
];
```

## Пример шаблона PHP
Создайте файл `app/controller/UserController.php` со следующим содержимым

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Создайте файл `app/view/user/hello.html` со следующим содержимым

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Пример шаблона Twig
Измените файл конфигурации `config/view.php` следующим образом

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php`:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Файл `app/view/user/hello.html`:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

Более подробную информацию можно найти [здесь](https://twig.symfony.com/doc/3.x/)

## Пример шаблона Blade
Измените файл конфигурации `config/view.php` следующим образом

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php`:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Файл `app/view/user/hello.blade.php`:

> Обратите внимание, что расширение файла для шаблона Blade - `.blade.php`

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

Более подробную информацию можно найти [здесь](https://learnku.com/docs/laravel/8.x/blade/9377)

## Пример шаблона think-template
Измените файл конфигурации `config/view.php` следующим образом

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php`:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Файл `app/view/user/hello.html`:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

Более подробную информацию можно найти [здесь](https://www.kancloud.cn/manual/think-template/content)

## Присвоение значений шаблону
Помимо использования `view(шаблон, массив_переменных)` для присвоения значений шаблону, мы также можем использовать вызов `View::assign()` в любом месте. Например:

```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` очень полезен в некоторых ситуациях, например, если каждая страница в системе должна отображать информацию о текущем пользователе в верхней части, использование `view(шаблон, ['user_info' => 'информация о пользователе']);` будет неудобным. Решением будет получение информации о пользователе через промежуточный слой и затем присвоение информации о пользователе шаблону с помощью `View::assign()`.

## О расположении файлов представлений

#### Контроллер
При вызове контроллером `view('название_шаблона',[]);`, файл представления ищется в соответствии с следующими правилами:

1. При отсутствии множественных приложений используется файл представления в `app/view/`.
2. При наличии [множественных приложений](multiapp.md) используется файл представления в `app/имя_приложения/view/`.

В общем, если `$request->app` пустой, используется файл представления в `app/view/`, в противном случае используется файл представления в `app/{$request->app}/view/`.

#### Замыкание
Так как `$request->app` пустой и не принадлежит ни к одному приложению, для замыкания используется файл представления в `app/view/`, например, когда маршрут определен в файле конфигурации `config/route.php` так:

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
используется файл представления `app/view/user.html` (если используется шаблон Blade, файл представления будет `app/view/user.blade.php`).

#### Указанное приложение
Для многоприложенной модели, чтобы представление могло быть использовано повторно, `view(шаблон, данные, приложение = null)` предоставляет третий параметр `$app`, который можно использовать для указания, какое приложение должно использоваться для файла представления. Например, `view('user', [], 'admin');` принудительно использует файл представления в `app/admin/view/`.

## Расширение twig

> **Обратите внимание**
> Эта функция доступна с webman-framework>=1.4.8

Мы можем расширить экземпляр представления twig, присвоив обратный вызов `view.extension` в конфигурации, например, в `config/view.php`:

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Добавить расширение
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Добавить фильтр
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Добавить функцию
    }
];
```

## Расширение blade
> **Обратите внимание**
> Эта функция доступна с webman-framework>=1.4.8

Аналогично, мы можем расширить экземпляр представления blade, присвоив обратный вызов `view.extension` в конфигурации, например, в `config/view.php`:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Добавить директивы в blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Использование компонента blade

> **Обратите внимание**
> Необходим webman/blade>=1.5.2

Предположим, нам нужно добавить компонент Alert

**Создайте файл `app/view/components/Alert.php`**

```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**Создайте файл `app/view/components/alert.blade.php`**

```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**Файл `/config/view.php` примет следующий вид**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

Таким образом, компонент Blade Alert настроен, и в шаблоне его можно использовать так:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```

## Расширение think-template
think-template использует `view.options.taglib_pre_load` для расширения библиотеки тегов, например:
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

Подробнее см. [Расширение тегов think-template](https://www.kancloud.cn/manual/think-template/1286424)
