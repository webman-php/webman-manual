## View

By default, webman uses the native PHP syntax as the template and achieves the best performance when `opcache` is enabled. In addition to the PHP native template, webman also provides support for template engines such as [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), and [think-template](https://www.kancloud.cn/manual/think-template/content).

### Enable opcache
When using the view, it is strongly recommended to enable the `opcache.enable` and `opcache.enable_cli` options in the php.ini file to achieve the best performance for the template engine.

### Install Twig
1. Install via composer

   `composer require twig/twig`

2. Modify the `config/view.php` as follows:
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```

   > **Note**
   > Other configuration options can be passed through the `options` variable, for example:
   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

### Install Blade
1. Install via composer
   ```shell
   composer require psr/container ^1.1.1 webman/blade
   ```

2. Modify the `config/view.php` as follows:
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

### Install think-template
1. Install via composer

   `composer require topthink/think-template`

2. Modify the `config/view.php` as follows:
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```

   > **Note**
   > Other configuration options can be passed through the `options` variable, for example:
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

### Example of Native PHP Template Engine
Create a file `app/controller/UserController.php` as follows:
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

### Example of Twig Template Engine
Modify the `config/view.php` as follows:
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` should be as follows:
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
hello {{name}}
</body>
</html>
```

For more information, refer to [Twig](https://twig.symfony.com/doc/3.x/).

### Example of Blade Template Engine
Modify the `config/view.php` as follows:
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` should be as follows:
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

> Note that the blade template extension is `.blade.php`

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

For more information, refer to [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

### Example of ThinkPHP Template Engine
Modify the `config/view.php` as follows:
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` should be as follows:
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

For more information, refer to [think-template](https://www.kancloud.cn/manual/think-template/content).

### Template Assignment
In addition to using `view(template, variables)` to assign values to the template, we can also assign values to the template at any location by calling `View::assign()`. For example:
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

In some scenarios, `View::assign()` is very useful. For example, if the user information needs to be displayed at the top of each page in a system, assigning user information to the template using `View::assign()` will be much more convenient than passing it through `view('template', ['user_info' => 'user information']);`. The solution is to obtain user information in a middleware and then assign the user information to the template using `View::assign()`.

### About View File Paths

#### Controller
When the controller calls `view('templateName', [])`, the view file is searched according to the following rules:

1. In a single application, use the corresponding view file under `app/view/`.
2. [In a multi-application](multiapp.md), use the corresponding view file under `app/applicationName/view/`.

In summary, if `$request->app` is empty, use the view file under `app/view/`, otherwise use the view file under `app/{$request->app}/view/`.

#### Closure Function
Since the closure function `$request->app` is empty and does not belong to any application, the closure function uses the view file under `app/view/`. For example, defining a route in `config/route.php` as follows:
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
This will use `app/view/user.html` as the template file (if using blade template, the template file should be `app/view/user.blade.php`).

#### Specify Application
In order for templates to be reusable in a multi-application mode, the `view($template, $data, $app = null)` function provides a third parameter `$app` to specify which application directory's template to use. For example, `view('user', [], 'admin')` will force the use of the view files under `app/admin/view/`.

### Extend Twig

> **Note**
> This feature requires webman-framework>=1.4.8.

We can extend the Twig view instance by providing a `view.extension` callback in the configuration. For example, the `config/view.php` would be as follows:
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Add extension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Add filter
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Add function
    }
];
```

### Extend Blade

> **Note**
> This feature requires webman-framework>=1.4.8.

Similarly, we can extend the Blade view instance by providing a `view.extension` callback in the configuration. For example, the `config/view.php` would be as follows:
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Add directives to blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```
## Using blade component in webman

**Note
Requires webman/blade>=1.5.2**

Assuming you need to add an Alert component

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
```html
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

By now, the Blade component Alert is set up, and it can be used in the template as follows:
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


## Extending think-template
think-template uses `view.options.taglib_pre_load` to extend tag libraries, for example
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

For details, please refer to [think-template tag extension](https://www.kancloud.cn/manual/think-template/1286424)
