# 多应用
有时一个项目可能分为多个子项目，例如一个商城可能分为商城主项目、商城api接口、商城管理后台3个子项目，他们都使用相同的数据库配置。

webman允许你这样规划app目录：
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
当访问地址 `http://127.0.0.1:8787/shop/{控制器}/{方法}` 时访问`app/shop/controller`下的控制器与方法。

当访问地址 `http://127.0.0.1:8787/api/{控制器}/{方法}` 时访问`app/api/controller`下的控制器与方法。

当访问地址 `http://127.0.0.1:8787/admin/{控制器}/{方法}` 时访问`app/admin/controller`下的控制器与方法。

在webman中，甚至可以这样规划app目录。
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```

这样当地址访问 `http://127.0.0.1:8787/{控制器}/{方法}` 时访问的是`app/controller`下的控制器与方法。当路径里以api或者admin开头时访问的是相对应目录里的控制器与方法。

多应用时类的命名空间需符合`psr4`，例如`app/api/controller/Foo.php` 文件类似如下：

```php
<?php
namespace app\api\controller;

use support\Request;

class Foo
{
    
}

```

## 多应用路由自动解析
有时候app目录结构可能更加复杂，webman无法自动解析路由，例如
```
app
└── api
   ├── v1
   │   ├── controller
   │   ├── model
   │   └── view
   └── admin
       └── v2
           ├── controller
           ├── model
           └── view

```

这时候我们可以通过反射来自动解析路由，例如在`config/route.php`中填写如下代码。

```php
$dir_iterator = new \RecursiveDirectoryIterator(app_path());
$iterator = new \RecursiveIteratorIterator($dir_iterator);
foreach ($iterator as $file) {
    // 忽略目录和非php文件
    if (is_dir($file) || $file->getExtension() != 'php') {
        continue;
    }

    $file_path = str_replace('\\', '/',$file->getPathname());
    // 文件路径里不带controller的文件忽略
    if (strpos($file_path, 'controller') === false) {
        continue;
    }

    // 根据文件路径计算uri
    $uri_path = strtolower(str_replace('controller/', '',substr(substr($file_path, strlen(base_path())), 0, -4)));
    // 根据文件路径是被类名
    $class_name = str_replace('/', '\\',substr(substr($file_path, strlen(base_path())), 0, -4));

    if (!class_exists($class_name)) {
        echo "Class $class_name not found, skip route for it\n";
        continue;
    }

    // 通过反射找到这个类的所有共有方法作为action
    $class = new ReflectionClass($class_name);
    $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

    $route = function ($uri, $cb) {
        //echo "Route $uri [{$cb[0]}, {$cb[1]}]\n";
        Route::any($uri, $cb);
        Route::any($uri.'/', $cb);
    };

    // 设置路由
    foreach ($methods as $item) {
        $action = $item->name;
        if (in_array($action, ['__construct', '__destruct'])) {
            continue;
        }
        // action为index时uri里末尾/index可以省略
        if ($action === 'index') {
            // controller也为index时可以uri里可以省略/index/index
            if (substr($uri_path, -6) === '/index') {
                $route(substr($uri_path, 0, -6), [$class_name, $action]);
            }
            $route($uri_path, [$class_name, $action]);
        }
        $route($uri_path.'/'.$action, [$class_name, $action]);
    }

}
```
以上路由脚本会自动检测app下所有controller类并设置对应的路由，这样不管你的app目录层级多深多复杂，都可以通过对应的url地址访问到。

## 多应用中间件配置
有时候你想为不同应用配置不同的中间件，例如`api`应用可能需要一个跨域中间件，`admin`需要一个检查管理员登录的中间件，则配置`config/midlleware.php`可能类似下面这样：
```php
return [
    // 全局中间件
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // api应用中间件
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // admin应用中间件
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> 以上中间件可能并不存在，这里仅仅是作为示例讲述如何按应用配置中间件

中间件执行顺序为 `全局中间件`->`应用中间件`。

中间件开发参考[中间件章节](middleware.md)

## 多应用异常处理配置
同样的，你想为不同的应用配置不同的异常处理类，例如`shop`应用里出现异常你可能想提供一个友好的提示页面；`api`应用里出现异常时你想返回的并不是一个页面，而是一个json字符串。为不同应用配置不同的异常处理类的配置文件`config/exception.php`类似如下：
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> 不同于中间件，每个应用只能配置一个异常处理类。

> 以上异常处理类可能并不存在，这里仅仅是作为示例讲述如何按应用配置异常处理

异常处理开发参考[异常处理章节](exception.md)