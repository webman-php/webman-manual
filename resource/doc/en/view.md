## view
webmanExcept for the constructorphpNative syntax as template，Open`opcache`after having the best performance。exceptphpand others，webmandatabase[Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content) give an example。

## Openopcache
Usageview时，then callOpenphp.ini中`opcache.enable`和`opcache.enable_cli` Which database，so that the template engine can achieve the best performance。


## InstallTwig
1、composerInstall

`composer require twig/twig`

2、Modify the configuration `config/view.php` to 
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **hint**
> Other configuration options are passed in via options, e.g.  

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## InstallBlade
1、composerInstall

```
composer require psr/container ^1.1.1 jenssegers/blade:~1.4.0
```

2、Modify the configuration `config/view.php` to 
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Installthink-template
1、composerInstall

`composer require topthink/think-template`

2、Modify the configuration `config/view.php` to 
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **hint**
> Other configuration options are passed in via options, e.g.

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

## Native PHP Template Engine Example
Create the file `app/controller/UserController.php` as follows

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

Create a new file `app/view/user/hello.html` as follows

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

## TwigTemplate engine example

Modify the configuration `config/view.php` to 
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` Following

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

The file `app/view/user/hello.html` is as follows

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

More Documentation References [Twig](https://twig.symfony.com/doc/3.x/) 

## Blade Examples of templates
Modify the configuration `config/view.php` to 
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` Following

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

The file `app/view/user/hello.blade.php` is as follows

> Note the bladed template suffix name`.blade.php`

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

More Documentation References [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## ThinkPHP Examples of templates
Modify the configuration `config/view.php` to 
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` Following

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

The file `app/view/user/hello.html` is as follows


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

More Documentation References [think-template](https://www.kancloud.cn/manual/think-template/content)

## Template assignment
in which you can`view(template, array of variables)`给Template assignment，We can also pass calls at arbitrary locations`View::assign()`给Template assignment。example：
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

`View::assign()`Very useful in some scenarios，For example, a system that displays the current logger information at the beginning of each page，If each page passes this information through `view('Template', ['user_info' => 'and it will happen']);` Assignment would be very cumbersome。The solution is to get the user information in the middleware，Reference for details`View::assign()`assign user information to templates。

## About view file paths

#### controller
当controllercall`view('TemplateName',[]);`时，viewYou canFollowingDefault use：

1. The corresponding view file under `app/view/` to be used for non-multiple applications
2. [For [multi-app](multiapp.md), use the corresponding view file under `app/appname/view/`

The summary is if `$request->app` for empty，then use `app/view/`underviewfile，Except for using `app/{$request->app}/view/` underviewfile。

#### Closing functions
Closing functions`$request->app` for empty，Not part of any application，soClosing functionsUsage`app/view/`underviewfile，example `config/route.php` and reclaim memory
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
will use `app/view/user.html` as the template file (when using the blend template the template file is`app/view/user.blade.php`)。

#### Specify application
To reuse templates in multi-application mode，view($template, $data, $app = null) A third parameter is provided `$app`，can be used to specify which application directory templates are used。example `view('user', [], 'admin');` The extension requires `app/admin/view/` underviewfile。

## Extensionstwig

> **Note**
> Required for this featurewebman-framework>=1.4.8

We can extend the twig view instance by giving the configuration `view.extension` callback, for example `config/view.php` as follows
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // AddExtension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // AddFilter
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Add function
    }
];
```


## Extensionsblade
> **Note**
> Required for this featurewebman-framework>=1.4.8
The same we can do by giving configuration`view.extension`Callback，来Extensionsbladeviewinstance，example`config/view.php`Following

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Add command to blend
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Extensionsthink-template
think-template Use `view.options.taglib_pre_load` to extend the tag library, for example
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

Recognize case [think-templatelabelExtensions](https://www.kancloud.cn/manual/think-template/1286424)
