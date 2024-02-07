# Multiple Applications
Sometimes a project may be divided into multiple sub-projects, such as a mall consisting of three sub-projects: main mall project, mall API interface, and mall management backend. They all use the same database configuration.

Webman allows you to organize the app directory in the following way:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
When accessing the URL `http://127.0.0.1:8787/shop/{controller}/{method}`, it will access the controller and method under `app/shop/controller`.

When accessing the URL `http://127.0.0.1:8787/api/{controller}/{method}`, it will access the controller and method under `app/api/controller`.

When accessing the URL `http://127.0.0.1:8787/admin/{controller}/{method}`, it will access the controller and method under `app/admin/controller`.

In webman, you can even organize the app directory like this:
```
app
├── controller
├── model
├── view

├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
In this case, when accessing the URL `http://127.0.0.1:8787/{controller}/{method}`, it will access the controller and method under `app/controller`. When the path starts with `api` or `admin`, it will access the corresponding directory's controller and method.

In multiple applications, the class namespace must comply with `PSR-4`. For example, the file `app/api/controller/FooController.php` will look similar to this:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Middleware Configuration for Multiple Applications
Sometimes you may want to configure different middlewares for different applications. For example, the `api` application may require a CORS middleware, and the `admin` application may require a middleware to check if the administrator is logged in. In this case, the configuration of `config/middleware.php` might look like this:
```php
return [
    // Global middleware
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware for the api application
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware for the admin application
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> The above middlewares may not exist, this is just an example to explain how to configure middlewares according to different applications.

The order of execution for middlewares is `global middleware` -> `application middleware`.

Middleware development can refer to the [Middleware section](middleware.md).

## Exception Handling Configuration for Multiple Applications
Similarly, if you want to configure different exception handlers for different applications, for example, you may want to provide a friendly error page when an exception occurs in the `shop` application, and return a JSON string instead of a page in the `api` application. The configuration file `config/exception.php` for configuring different exception handlers for different applications would look like this:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Unlike middlewares, each application can only configure one exception handler.

> The above exception handlers may not exist, this is just an example to explain how to configure exception handlers according to different applications.

Exception handling development can refer to the [Exception Handling section](exception.md).
