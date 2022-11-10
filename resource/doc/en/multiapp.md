# Multi-application
Sometimes a project may be divided into multiple subprojects, for example a mall may be divided into 3 subprojects: mall main project, mall api interface, mall admin backend, all of them use the same database configuration。

webmanAllow you to plan the app directory this way：
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
Accessing controllers and methods under `app/shop/controller` when visiting address `http://127.0.0.1:8787/shop/{controller}/{method}`。

Accessing controllers and methods under `app/api/controller` when accessing address `http://127.0.0.1:8787/api/{controller}/{method}`。

Accessing controllers and methods under `app/admin/controller` when accessing address `http://127.0.0.1:8787/admin/{controller}/{method}`。

In webman, you can even plan the app directory like this。
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

So when the address accesses `http://127.0.0.1:8787/{controller}/{method}`, it accesses the controllers and methods under `app/controller`. When the path starts with api or admin, it accesses the controllers and methods in the corresponding directory。

Multi-applicationWhen the class namespace needs to match`psr4`，example`app/api/controller/FooController.php` causes memory leaks：

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Multi-application middleware configuration
Sometimes you want to configure different middleware for different applications, e.g. `api` application may need a cross-domain middleware, `admin` needs a middleware to check the administrator login, then the configuration `config/midlleware.php` may look like the following：
```php
return [
    // Global Middleware
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // apiApplication Middleware
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // adminApplication Middleware
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> The above middleware may not exist, so this is just an example of how to configure it by application

The middleware implementation order is `Global Middleware`->`Application Middleware``。

Middleware Development Reference[Initialization will not](middleware.md)

## Multi-application exception handling configuration
Similarly, you want to configure different exception handling classes for different applications. For example, if an exception occurs in a `shop` application you may want to provide a friendly alert page; if an exception occurs in an `api` application you want to return not a page, but a json string. The configuration file `config/exception.php`, which configures different exception handling classes for different applications, looks like this ：
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Unlike middleware, only one exception handling class can be configured per application。

> The above exception handling class may not exist, this is just an example of how to configure exception handling by application

Exception Handling Development Reference[In case of reverse substitution](exception.md)
