## Routing
## Default Routing Rules
The default routing rule for webman is  `http://127.0.0.1:8787/{controller}/{action}`.

The default controller is `app\controller\IndexController`, and the default action is `index`.

For example, when accessing:
- `http://127.0.0.1:8787`, it will by default access the `index` method of the `app\controller\IndexController` class.
- `http://127.0.0.1:8787/foo`, it will by default access the `index` method of the `app\controller\FooController` class.
- `http://127.0.0.1:8787/foo/test`, it will by default access the `test` method of the `app\controller\FooController` class.
- `http://127.0.0.1:8787/admin/foo/test`, it will by default access the `test` method of the `app\admin\controller\FooController` class (see [Multiple Applications](multiapp.md)).

Additionally, starting from webman 1.4, it supports more complex default routing, such as
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

When you want to change a request route, please modify the configuration file `config/route.php`.

If you want to disable the default route, add the following configuration to the end of the `config/route.php` file:
```php
Route::disableDefaultRoute();
```

## Closure Routes
Add the following route code to `config/route.php`:
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});

```
> **Note**
> Because closure functions do not belong to any controller, `$request->app`, `$request->controller`, and `$request->action` are all empty strings.

When accessing the address `http://127.0.0.1:8787/test`, it will return the string `test`.

> **Note**
> The route path must start with `/`, for example:

```php
use support\Request;
// Incorrect usage
Route::any('test', function (Request $request) {
    return response('test');
});

// Correct usage
Route::any('/test', function (Request $request) {
    return response('test');
});
```


## Class Routes
Add the following route code to `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```

When accessing the address `http://127.0.0.1:8787/testclass`, it will return the result of the `test` method of the `app\controller\IndexController` class.


## Annotation Routing

Define routes using annotations on controller methods without configuring in `config/route.php`.

> **Note**
> This feature requires webman-framework >= v2.2.0

### Basic Usage

```php
namespace app\controller;
use support\annotation\route\Get;
use support\annotation\route\Post;

class UserController
{
    #[Get('/user/{id}')]
    public function show($id)
    {
        return "user $id";
    }

    #[Post('/user')]
    public function store()
    {
        return 'created';
    }
}
```

Available annotations: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (any method). Paths must start with `/`. The second parameter can specify a route name for `route()` URL generation.

### Parameterless Annotations: Restrict HTTP Methods for Default Routes

When no path is specified, only the allowed HTTP methods for that action are restricted; the default route path is still used:

```php
#[Post]
public function create() { ... }  // POST only, path still /user/create

#[Get]
public function index() { ... }   // GET only
```

Multiple annotations can be combined to allow several methods:

```php
#[Get]
#[Post]
public function form() { ... }  // Allows GET and POST
```

Undeclared HTTP methods will return 405.

Multiple path annotations register as separate routes: `#[Get('/a')] #[Post('/b')]` creates both GET /a and POST /b.

### Route Group Prefix

Use `#[RouteGroup]` on a class to add a prefix to all method routes:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // Actual path: /api/v1/user/{id}
    public function show($id) { ... }
}
```

### Custom HTTP Methods and Route Name

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### Middleware

`#[Middleware]` on a controller or method applies to annotation routes, same usage as `support\annotation\Middleware`.


## Route Parameters
If there are parameters in the route, they can be matched using `{key}` and the matched result will be passed to the corresponding controller method parameter (starting from the second parameter), for example:
```php
// Matching /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('Received parameter'.$id);
    }
}
```

More examples:
```php
use support\Request;
// Matching /user/123, not matching /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// Matching /user/foobar, not matching /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// Matching /user, /user/123, and /user/abc   [] indicates optional
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// Matching all requests with /user/ prefix
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// Matching all options requests   : followed by regex specifies the pattern for the named parameter
Route::options('[{path:.+}]', function () {
    return response('');
});
```
Advanced usage summary

> The `[]` syntax in Webman routing mainly handles optional path segments or dynamic route matching, allowing you to define more complex path structures and matching rules
>
> `:` is used to specify regular expressions


## Route Groups
Sometimes, routes contain a lot of the same prefixes. In this case, we can use route groups to simplify the definition. For example:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Equivalent to
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Nested group usage
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## Route Middleware
We can set a middleware for a specific route or a group of routes. For example:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Incorrect usage example (valid in webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Correct usage example
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## Resourceful Routing
```php
Route::resource('/test', app\controller\IndexController::class);

// Specify resource routes
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Non-defining resource routes
// If accessing notify, it will be any type of route /test/notify or /test/notify/{id} and the routeName will be test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verb   | URI                 | Action   | Route Name    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## URL Generation
> **Note**
> Route URL generation for nested groups is not supported yet

For example, with the route:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
We can use the following method to generate the URL for this route:
```php
route('blog.view', ['id' => 100]); // Result is /blog/100
```

When using route URLs in a view, this method can be used so that regardless of changes to the routing rules, the URL will be generated automatically, avoiding the need to modify a large number of view files due to changes in route addresses.
## Obtain Route Information

We can obtain the current request route information through the `$request->route` object, for example:

```php
$route = $request->route; // Equivalent to $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **Note**
> If the current request does not match any routes configured in `config/route.php`, `$request->route` will be null, meaning that the default route will result in `$request->route` being null.

## Handling 404 Errors
When the route is not found, the default behavior is to return a 404 status code and output 404 content.

If developers want to intervene in the business process when a route is not found, they can use the fallback route provided by webman using the `Route::fallback($callback)` method. For example, the following code logic redirects to the homepage when the route is not found.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Another example is returning a JSON response when the route does not exist, which is very useful when webman is used as an API endpoint.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## Add Middleware to 404

By default, 404 requests do not go through any middleware. If you need to add middleware to 404 requests, refer to the following code.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

Related link: [Custom 404 and 500 Pages](others/custom-error-page.md)

## Disable Default Route

```php
// Disable main project default route, does not affect application plugins
Route::disableDefaultRoute();
// Disable admin application route of main project, does not affect application plugins
Route::disableDefaultRoute('', 'admin');
// Disable foo plugin default route, does not affect main project
Route::disableDefaultRoute('foo');
// Disable foo plugin admin application default route, does not affect main project
Route::disableDefaultRoute('foo', 'admin');
// Disable default route for controller [\app\controller\IndexController::class, 'index']
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## Annotation Disable Default Route

You can disable the default route for a controller using annotations, for example:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

#[DisableDefaultRoute]
class IndexController
{
    public function index()
    {
        return 'index';
    }
}
```

Likewise, you can also disable the default route for a specific method of a controller using annotations, for example:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

class IndexController
{
    #[DisableDefaultRoute]
    public function index()
    {
        return 'index';
    }
}
```

## Route Interface
```php
// Set a route for any method request for $uri
Route::any($uri, $callback);
// Set a route for a get request for $uri
Route::get($uri, $callback);
// Set a route for a post request for $uri
Route::post($uri, $callback);
// Set a route for a put request for $uri
Route::put($uri, $callback);
// Set a route for a patch request for $uri
Route::patch($uri, $callback);
// Set a route for a delete request for $uri
Route::delete($uri, $callback);
// Set a route for a head request for $uri
Route::head($uri, $callback);
// Set a route for an options request for $uri
Route::options($uri, $callback);
// Set routes for multiple request types simultaneously
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Group routes
Route::group($path, $callback);
// Resource routes
Route::resource($path, $callback, [$options]);
// Disable the default route
Route::disableDefaultRoute($plugin = '');
// Fallback route, set the default route fallback
Route::fallback($callback, $plugin = '');
// Get all route information
Route::getRoutes();
```
If there is no corresponding route for the URI (including the default route), and no fallback route is set, a 404 response will be returned.

## Multiple Route Configuration Files
If you want to manage routes using multiple route configuration files, for example, in the case of [multiple applications](multiapp.md) where each application has its own route configuration, you can load external route configuration files using the `require` statement.
For example, in `config/route.php`:

```php
<?php

// Load the route configuration for the admin application
require_once app_path('admin/config/route.php');
// Load the route configuration for the api application
require_once app_path('api/config/route.php');
```
