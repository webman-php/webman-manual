## 视图
webman默认使用的是php原生语法作为模版，在打开`opcache`后具有最好的性能。除了php原生模版，webman还提供了[Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content) 模版引擎，其中推荐`Twig`，在易用性、扩展性及性能上是比较优秀的一款视图模版引擎。

## 开启opcache
使用视图时，强烈建议开启php.ini中`opcache.enable`和`opcache.enable_cli` 两个选项，以便模版引擎达到最好性能。


## 安装Twig
1、composer安装

`composer require twig/twig`

2、修改配置`config/view.php`为
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

## 安装Blade
1、composer安装

```
composer remove jenssegers/blade
composer require jenssegers/blade ~1.4.0
```


2、修改配置`config/view.php`为
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## 安装think-template
1、composer安装

`composer require topthink/think-template`

2、修改配置`config/view.php`为
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

## 原生PHP模版引擎例子
创建文件 `app/controller/User.php` 如下

```php
<?php
namespace app\controller;

use support\Request;

class User
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

新建文件 `app/view/user/hello.html` 如下

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

## Twig模版引擎例子

修改配置`config/view.php`为
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/User.php` 如下

```php
<?php
namespace app\controller;

use support\Request;

class User
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

文件 `app/view/user/hello.html` 如下

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

更多文档参考 [Twig](https://twig.symfony.com/doc/3.x/) 

## Blade 模版的例子
修改配置`config/view.php`为
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/User.php` 如下

```php
<?php
namespace app\controller;

use support\Request;

class User
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

文件 `app/view/user/hello.blade.php` 如下

> 注意blade模版后缀名为`.blade.php`

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

更多文档参考 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## ThinkPHP 模版的例子
修改配置`config/view.php`为
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/User.php` 如下

```php
<?php
namespace app\controller;

use support\Request;

class User
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

文件 `app/view/user/hello.html` 如下


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

更多文档参考 [think-template](https://www.kancloud.cn/manual/think-template/content)

## 模版赋值
除了使用`view(模版, 变量数组)`给模版赋值，我们还可以在任意位置通过调用`View::assign()`给模版赋值。例如：
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class User
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

`View::assign()`在某些场景下非常有用，例如某系统每个页面首部都要显示当前登录者信息，如果每个页面都将此信息通过 `view('模版', ['user_info' => '用户信息']);` 赋值将非常麻烦。解决办法就是在中间件中获得用户信息，然后通过`View::assign()`将用户信息赋值给模版，