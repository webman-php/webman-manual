## Routing

## Default Routing Rules
The default routing rule for webman is `http://127.0.0.1:8787/{Controller}/{Action}`.

The default controller is `app\controller\IndexController`, and the default action is `index`.

For example, when accessing:
- `http://127.0.0.1:8787`, it will default to access the `index` method of the `app\controller\IndexController` class.
- `http://127.0.0.1:8787/foo`, it will default to access the `index` method of the `app\controller\FooController` class.
- `http://127.0.0.1:8787/foo/test`, it will default to access the `test` method of the `app\controller\FooController` class.
- `http://127.0.0.1:8787/admin/foo/test`, it will default to access the `test` method of the `app\admin\controller\FooController` class (refer to [Multi-Application](multiapp.md)).

Additionally, from webman version 1.4, more complex default routing is supported, for example:
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

If you want to disable the default routing, add the following configuration to the end of the configuration file `config/route.php`:
```php
Route::disableDefaultRoute();
```

## Closure Routes
Add the following route code in `config/route.php`:
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **Note**
> Since the closure function does not belong to any controller, the variables such as `$request->app`, `$request->controller`, and `$request->action` are all empty strings.

When the address accessed is `http://127.0.0.1:8787/test`, it will return the string `test`.

> **Note**
> Routing paths must start with `/`, for example:

```php
// Incorrect usage
Route::any('test', function ($request) {
    return response('test');
});

// Correct usage
Route::any('/test', function ($request) {
    return response('test');
});
```

## Class Routes
Add the following route code in `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
When the address accessed is `http://127.0.0.1:8787/testclass`, it will return the result of the `test` method of the `app\controller\IndexController` class.

## Route Parameters
If there are parameters in the route, they can be matched using `{key}`, and the matched result will be passed to the corresponding controller method parameters (starting from the second parameter), for example:
```php
// Match /user/123 and /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Received parameter '.$id);
    }
}
```

More examples:
```php
// Match /user/123, but not /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Match /user/foobar, but not /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Match /user, /user/123, and /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Match all options requests
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Route Groups

Sometimes routes contain a lot of the same prefixes, in such cases, we can use route groups to simplify the definition. For example:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Equivalent to:
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Nested usage of the group:

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

We can set middleware for a specific route or a group of routes.
For example:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Note**:
> In webman-framework <= 1.5.6, when using `->middleware()` for group middleware after the group, the current route must be within the current group.

```php
# Incorrect usage example (valid from webman-framework >= 1.5.7)
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

## Resourceful Routes
```php
Route::resource('/test', app\controller\IndexController::class);

// Specify resourceful routes
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Non-definitive resourceful routes
// For example, if accessing notify the address will be any type /test/notify or /test/notify/{id}, and both will work. The route name is test.notify.
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
> Nesting of group routes for URL generation is not supported at the moment.

For example, for the route:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
We can use the following method to generate the URL for this route.
```php
route('blog.view', ['id' => 100]); // Result will be /blog/100
```

When using the route's URL in views, this method can be used to generate it. This ensures that the URL is generated automatically regardless of any changes in the route rules, thus avoiding extensive changes in view files due to changes in route addresses.

## Getting Route Information
> **Note**
> Requires webman-framework >= 1.3.2

We can obtain the current request route information through the `$request->route` object, for example:
```php
$route = $request->route; // Equivalent to $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // This feature requires webman-framework >= 1.3.16
}
```

> **Note**
> If the current request does not match any routes configured in `config/route.php`, then `$request->route` will be null, meaning that when using default routes, `$request->route` will be null.

## Handling 404
When a route is not found, webman defaults to returning a 404 status code and outputting the contents of `public/404.html`.

If developers wish to intervene in the business flow when a route is not found, they can use the fallback route provided by webman with the `Route::fallback($callback)` method. For example, the logic in the following code is redirected to the homepage when the route is not found.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Another example is to return JSON data when the route does not exist, which is very useful when webman is used as an API interface.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

Related Links: [Custom 404 and 500 Pages](others/custom-error-page.md)

## Route Interface
```php
// Set a route for any method request to $uri
Route::any($uri, $callback);
// Set a route for a GET request to $uri
Route::get($uri, $callback);
// Set a route for a POST request to $uri
Route::post($uri, $callback);
// Set a route for a PUT request to $uri
Route::put($uri, $callback);
// Set a route for a PATCH request to $uri
Route::patch($uri, $callback);
// Set a route for a DELETE request to $uri
Route::delete($uri, $callback);
// Set a route for a HEAD request to $uri
Route::head($uri, $callback);
// Set multiple types of request routes simultaneously
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Group routes
Route::group($path, $callback);
// Resource routes
Route::resource($path, $callback, [$options]);
// Disable routes
Route::disableDefaultRoute($plugin = '');
// Fallback route, set a default route fallback
Route::fallback($callback, $plugin = '');
```
If there is no corresponding route for the URI (including default routes) and no fallback route is set, a 404 response will be returned. 

## Multiple Route Configuration Files
If you want to use multiple route configuration files to manage routes, for example, when each application in [Multi-Application](multiapp.md) has its own route configuration, external route configuration files can be loaded using the `require` method. For example, in `config/route.php`:
```php
<?php

// Load the route configuration for the admin application
require_once app_path('admin/config/route.php');
// Load the route configuration for the api application
require_once app_path('api/config/route.php');