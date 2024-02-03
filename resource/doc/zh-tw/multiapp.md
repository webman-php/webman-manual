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

多应用时类的命名空间需符合`psr4`，例如`app/api/controller/FooController.php` 文件类似如下：

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

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
