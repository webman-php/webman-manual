# Multiple Applications
Sometimes a project may be divided into multiple sub-projects, for example, a shopping mall may consist of three sub-projects: the main shopping mall, shopping mall API interface, and shopping mall admin panel, all of which use the same database configuration.

Webman allows you to organize the app directory in the following manner:
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

In webman, you can even organize the app directory as follows:
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
In this case, when accessing the URL `http://127.0.0.1:8787/{controller}/{method}`, it will access the controller and method under `app/controller`. When the path starts with `api` or `admin`, it will access the controller and method under the corresponding directory.

For multiple applications, the class namespace needs to comply with `PSR4`. For example, the file `app/api/controller/FooController.php` would have a class structure like this:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}
```

## Middleware Configuration for Multiple Applications
Sometimes, you may want to configure different middleware for different applications. For example, the `api` application may require a cross-origin middleware, while the `admin` application may need a middleware to check admin login. In this case, the `config/middleware.php` configuration might look something like this:
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
> The above middleware may not exist; this is just an example to demonstrate how to configure middleware by application.

The execution order of middleware is `global middleware`->`application middleware`.

For middleware development, refer to the [Middleware Section](middleware.md).

## Exception Handling Configuration for Multiple Applications
Similarly, you may want to configure different exception handling classes for different applications. For example, if an exception occurs in the `shop` application, you may want to provide a friendly error page, while in the `api` application, you may want to return a JSON string instead of a page. The configuration file for setting different exception handling classes for different applications would look like this in `config/exception.php`:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Unlike middleware, each application can only configure one exception handling class.

> The above exception handling classes may not exist; this is just an example to demonstrate how to configure exception handling by application.

For exception handling development, refer to the [Exception Handling Section](exception.md).