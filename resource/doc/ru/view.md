## Представление
По умолчанию webman использует нативный синтаксис PHP в качестве шаблона с лучшей производительностью при включенном `opcache`. Кроме нативного шаблона PHP, webman также предоставляет шаблонизаторы [Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content).

## Включение opcache
При использовании представлений настоятельно рекомендуется включить опцию `opcache.enable` и `opcache.enable_cli` в php.ini, чтобы шаблонизаторы достигли наилучшей производительности.

## Установка Twig
1. Установка через composer

   `composer require twig/twig`

2. Измените конфигурацию `config/view.php` на следующую:
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```

   > **Подсказка**
   > Другие параметры конфигурации передаются через опцию `options`, например
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

   ```
   composer require psr/container ^1.1.1 webman/blade
   ```

2. Измените конфигурацию `config/view.php` на следующую:
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

2. Измените конфигурацию `config/view.php` на следующую:
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```

   > **Подсказка**
   > Другие параметры конфигурации передаются через опцию `options`, например
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

## Пример шаблона на нативном PHP
Создайте файл `app/controller/UserController.php` следующего содержания:

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

Создайте файл `app/view/user/hello.html` следующего содержания:

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

## Пример шаблона на Twig
Измените конфигурацию `config/view.php` на следующую:
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

Файл `app/controller/UserController.php` следующего содержания:

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

Файл `app/view/user/hello.html` следующего содержания:

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

Дополнительную документацию можно найти по ссылке [Twig](https://twig.symfony.com/doc/3.x/).

## Пример шаблона на Blade
Измените конфигурацию `config/view.php` на следующую:
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

Файл `app/controller/UserController.php` следующего содержания:

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

Файл `app/view/user/hello.blade.php` следующего содержания:

> Обратите внимание, что у шаблона Blade расширение файла `.blade.php`

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

Дополнительную документацию можно найти по ссылке [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Пример шаблона на think-template
Измените конфигурацию `config/view.php` на следующую:
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

Файл `app/controller/UserController.php` следующего содержания:

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

Файл `app/view/user/hello.html` следующего содержания:

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

Дополнительную документацию можно найти по ссылке [think-template](https://www.kancloud.cn/manual/think-template/content).

## Присвоение значений шаблону
Помимо использования `view(шаблон, массив_переменных)` для присвоения значений шаблону, мы также можем использовать `View::assign()` в любом месте для присвоения значений шаблону. Например:
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

`View::assign()` очень полезен в некоторых ситуациях, например, если на каждой странице системы нужно отображать информацию о текущем пользователе, то заносить эту информацию в шаблон через `view('шаблон', ['user_info' => 'Информация о пользователе']);` на каждой странице будет очень неудобно. Решением будет получить информацию о пользователе в промежуточном обработчике, а затем использовать `View::assign()` для присвоения информации о пользователе шаблону.

## О путях к файлам представлений

#### Контроллер
Когда контроллер вызывает `view('название_шаблона',[]);`, файл представления ищется в соответствии со следующими правилами:

1. В случае отсутствия множества приложений используется файл представления из `app/view/`
2. [В множестве приложений](multiapp.md) используется файл представления из `app/имя_приложения/view/`

В общем, если `$request->app` пуст, используется файл представления из `app/view/`, в противном случае используется файл представления из `app/{$request->app}/view/`.

#### Замыкание
Замыкание `$request->app` не относится к какому-либо приложению, поэтому замыкание использует файл представления из `app/view/`, например, определенный маршрут в `config/route.php`:
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
использует файл представления `app/view/user.html` в случае использования шаблонизатора Blade файл представления будет `app/view/user.blade.php`.

#### Указание приложения
Чтобы шаблоны могли повторно использоваться в режиме множества приложений, метод `view($template, $data, $app = null)` предоставляет третий параметр `$app`, который можно использовать для указания использования шаблона из каталога определенного приложения. Например, `view('user', [], 'admin')` является обязательным использованием файлов представления из `app/admin/view/`.

## Расширение twig

> **Обратите внимание**
> Эта функция требует webman-framework>=1.4.8

Мы можем расширить экземпляр представления twig, добавив обратный вызов к конфигурации `view.extension`, например, `config/view.php`:
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // добавление расширения
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // добавление фильтра
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // добавление функции
    }
];
```
## Расширение Blade
> **Примечание**
> Данная функция требует webman-framework>=1.4.8
Точно так же мы можем расширить экземпляр представления Blade, добавив обратный вызов `view.extension` в файл конфигурации `config/view.php`, как показано ниже:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Добавление директив в Blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Использование компонента в Blade

> **Примечание**
> Требуется webman/blade>=1.5.2

Предположим, что нужно добавить компонент Alert

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

**Файл `/config/view.php` должен быть примерно следующим:**

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

Таким образом, компонент Alert Blade настроен полностью, и его использование в шаблоне будет следующим образом:
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

Подробности см. в [расширении тегов think-template](https://www.kancloud.cn/manual/think-template/1286424)
