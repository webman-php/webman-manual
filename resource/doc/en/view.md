## View

By default, webman uses native PHP syntax as the template and performs best when `opcache` is enabled. In addition to the native PHP template, webman also provides support for [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), and [think-template](https://www.kancloud.cn/manual/think-template/content) template engines.

## Enable opcache
When using the view, it is strongly recommended to enable the `opcache.enable` and `opcache.enable_cli` options in the `php.ini` file to ensure the best performance of the template engine.

## Install Twig
1. Install via composer:

   `composer require twig/twig`

2. Modify the `config/view.php` configuration to:

   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **Note**
   > Other configuration options can be passed through `options`, for example:

   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
    ]
   ];
   ```

## Install Blade
1. Install via composer:

   `composer require psr/container ^1.1.1 webman/blade`

2. Modify the `config/view.php` configuration to:

   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## Install think-template
1. Install via composer:

   `composer require topthink/think-template`

2. Modify the `config/view.php` configuration to:

   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **Note**
   > Other configuration options can be passed through `options`, for example:

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

## Example of Native PHP Template Engine
Create the file `app/controller/UserController.php` as follows:

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

Create a new file `app/view/user/hello.html` as follows:

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

## Example of Twig Template Engine
Modify the `config/view.php` configuration to:

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` as follows:

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

The `app/view/user/hello.html` file should be as follows:

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

For more documentation, refer to [Twig](https://twig.symfony.com/doc/3.x/).

## Example of Blade Template Engine
Modify the `config/view.php` configuration to:

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` as follows:

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

The file `app/view/user/hello.blade.php` should be as follows:

> Note that the Blade template has a `.blade.php` file extension.

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

For more documentation, refer to [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Example of ThinkPHP Template Engine
Modify the `config/view.php` configuration to:

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` as follows:

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

The file `app/view/user/hello.html` should be as follows:

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

For more documentation, refer to [think-template](https://www.kancloud.cn/manual/think-template/content).

## Template Assignment
In addition to using `view(template, data)` to assign values to the template, we can also assign values to the template at any position by calling `View::assign()`. For example:

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

`View::assign()` is very useful in some scenarios, such as when every page in a system needs to display the information of the currently logged-in user. In this case, getting the user information in the middleware and then assigning it to the template using `View::assign()` can be a solution.

## About the View File Path

#### Controller
When the controller calls `view('template_name', [])`, the view file is searched according to the following rules:

1. In a single application, use the corresponding view file under `app/view/`.
2. In a [multi-app](multiapp.md) scenario, use the corresponding view file under `app/app_name/view/`.

In summary, if `$request->app` is empty, use the view file under `app/view/`, otherwise use the view file under `app/{$request->app}/view/`.

#### Closure Function
The closure function with `$request->app` being empty, does not belong to any application, so it uses the view file under `app/view/`. For example, defining a route in `config/route.php` as follows:

```php
Route::any('/admin/user/get', function (Request $request) {
    return view('user', []);
});
```

This will use `app/view/user.html` as the template file (when using blade template, the template file is `app/view/user.blade.php`).

#### Specify an Application
In order to reuse templates in a multi-app mode, the `view($template, $data, $app = null)` provides a third parameter `$app` to specify which application directory to use for the template. For example, `view('user', [], 'admin')` will force the use of the view file under `app/admin/view/`.

## Extend Twig

> **Note**
> This feature requires webman-framework>=1.4.8.

We can extend the Twig view instance by providing a `view.extension` callback in the configuration `config/view.php`, for example:

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Add Extension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Add Filter
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Add Function
    }
];
```

## Extend Blade

> **Note**
> This feature requires webman-framework>=1.4.8.

Similarly, we can extend the Blade view instance by providing a `view.extension` callback in the configuration `config/view.php`, for example:

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Add directives to Blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Use Blade Component

> **Note**
> Requires webman/blade>=1.5.2.

Suppose we need to add an Alert component.

**Create `app/view/components/Alert.php`**

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

**Create `app/view/components/alert.blade.php`**

```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` similar to the following code**

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

Blade component Alert is now set up. When using it in the template, it looks like this:

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


## Extend think-template
Think-template uses `view.options.taglib_pre_load` to extend the tag library, for example:

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

For more details, refer to [think-template tag extension](https://www.kancloud.cn/manual/think-template/1286424).