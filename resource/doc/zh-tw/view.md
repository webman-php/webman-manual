## 樣板
webman預設使用原生PHP語法作為樣板，在開啟 `opcache` 後具有最佳的效能。除了PHP原生樣板，webman還提供了 [Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content) 樣板引擎。

## 開啟opcache
使用樣板時，強烈建議開啟php.ini中 `opcache.enable` 和 `opcache.enable_cli` 兩個選項，以便樣板引擎達到最佳效能。

## 安裝Twig
1、使用composer安裝

```
composer require twig/twig
```

2、修改配置`config/view.php` 為
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **提示**
> 其他配置選項通過options傳入，例如  

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```

## 安裝Blade
1、使用composer安裝

```
composer require psr/container ^1.1.1 webman/blade
```

2、修改配置`config/view.php` 為
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## 安裝think-template
1、使用composer安裝

```
composer require topthink/think-template
```

2、修改配置`config/view.php` 為
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **提示**
> 其他配置選項通過options傳入，例如

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

## 原生PHP樣板引擎例子
創建文件 `app/controller/UserController.php` 如下

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

## Twig樣板引擎例子

修改配置`config/view.php` 為
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` 如下

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

更多文檔參考 [Twig](https://twig.symfony.com/doc/3.x/) 

## Blade 樣板的例子
修改配置`config/view.php` 為
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` 如下

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

文件 `app/view/user/hello.blade.php` 如下

> 注意blade樣板後綴名為`.blade.php`

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

更多文檔參考 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## ThinkPHP 樣板的例子
修改配置`config/view.php` 為
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` 如下

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

更多文檔參考 [think-template](https://www.kancloud.cn/manual/think-template/content)

## 樣版賦值
除了使用`view(模版, 變數陣列)`給樣板賦值，我們還可以在任意位置通過調用`View::assign()`給樣板賦值。例如：
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

`View::assign()`在某些場景下非常有用，例如某系統每個頁面首部都要顯示當前登入者資訊，如果每個頁面都將此資訊通過 `view('模版', ['user_info' => '用戶資訊']);` 賦值將非常麻煩。解決辦法就是在中間件中獲得使用者信息，然后通過`View::assign()`將使用者信息賦值給樣板。

## 關於樣板檔案路徑

#### 控制器
當控制器調用`view('模版名',[]);`時，樣板檔案按照如下規則查找：

1. 非多應用時，使用 `app/view/` 下對應的樣板檔案
2. [多應用](multiapp.md)時，使用 `app/應用名/view/` 下對應的樣板檔案

總結來說就是如果 `$request->app` 為空，則使用 `app/view/` 下的樣板檔案，否則使用 `app/{$request->app}/view/` 下的樣板檔案。

#### 闭包函數
闭包函數`$request->app` 為空，不屬於任何應用，所以闭包函數使用`app/view/`下的樣板檔案，例如 `config/route.php` 裡定義路由
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
會使用`app/view/user.html`作為樣板檔案(當使用blade樣板時樣板檔案為`app/view/user.blade.php`)。

#### 指定應用
為了多應用模式下樣板可以複用，view($template, $data, $app = null) 提供了第三個參數 `$app`，可以用來指定使用哪個應用目錄下的樣板。例如 `view('user', [], 'admin');` 會強制使用 `app/admin/view/` 下的樣板檔案。

## 擴展twig

> **注意**
> 此特性需要webman-framework>=1.4.8

我們可以通過給配置`view.extension`回調，來擴展twig樣板實例，例如`config/view.php`如下
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // 增加Extension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // 增加Filter
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // 增加函數
    }
];
```

## 擴展blade
> **注意**
> 此特性需要webman-framework>=1.4.8
同樣的我們可以通過給配置`view.extension`回調，來擴展blade樣板實例，例如`config/view.php`如下

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // 給blade添加指令
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## blade使用component組件

> **注意
> 需要webman/blade>=1.5.2**

假設需要添加一個Alert組件

**新建 `app/view/components/Alert.php`**
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

**新建 `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php`類似如下程式碼**

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

至此，Blade組件Alert設定完畢，樣板裡使用時類似如下
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