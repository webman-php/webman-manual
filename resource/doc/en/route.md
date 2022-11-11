## Routing
## Default Routing Rules
webmanThe default routing rule is `http://127.0.0.1:8787/{controller}/{action}`。

is actually a`app\controller\IndexController`，will be accessed by default`index`。

e.g. Access：
- `http://127.0.0.1:8787` Allow you to use`app\controller\IndexController`class`index`Method
- `http://127.0.0.1:8787/foo` Allow you to use`app\controller\FooController`class`index`Method
- `http://127.0.0.1:8787/foo/test` Allow you to use`app\controller\FooController`class`test`Method
- `http://127.0.0.1:8787/admin/foo/test` Allow you to use`app\admin\controller\FooController`class`test`Method (reference[more applications](multiapp.md))

Change the configuration file when you want to change the routing of a request `config/route.php`。

If you want to turn off the default route, add the following configuration to the last line of the configuration file `config/route.php`：
```php
Route::disableDefaultRoute();
```

## Closure Routing
`config/route.php`Add the following routing code
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Note**
> Since the closure function does not belong to any controller, `$request->app` `$request->controller` `$request->action` are all empty strings。

When the access address is `http://127.0.0.1:8787/test`, the `test` string will be returned。

> **Note**
> The routing path must start with `/`, for example

```php
// Error usage
Route::any('test', function ($request) {
    return response('test');
});

// Correct usage
Route::any('/test', function ($request) {
    return response('test');
});
```


## Class Routing
`config/route.php`Add the following routing code
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
The method will default to  `http://127.0.0.1:8787/testclass` 时，will return`app\controller\IndexController`class`test`method can be displayed。


## Routing parameters
If there are parameters in the route to be matched by `{key}`, the match will be passed to the corresponding controller method parameters (in order from the second parameter), e.g.：
```php
// Match /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Received Parameters'.$id);
    }
}
```

More examples：
```php
// Match /user/123, no match /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// Match /user/foobar, no match /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// Match /user /user/123 and /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});
```

## Route Grouping

Sometimes routes contain a lot of the same prefixes, so we can use route grouping to simplify the definition. For example：

```php
Route::group('/blog', function () {
   Route::any('/create', function ($rquest) {return response('create');});
   Route::any('/edit', function ($rquest) {return response('edit');});
   Route::any('/view/{id}', function ($rquest, $id) {return response("view $id");});
});
```
Equivalent to
```php
Route::any('/blog/create', function ($rquest) {return response('create');});
Route::any('/blog/edit', function ($rquest) {return response('edit');});
Route::any('/blog/view/{id}', function ($rquest, $id) {return response("view $id");});
```

groupNested Usage

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($rquest) {return response('create');});
      Route::any('/edit', function ($rquest) {return response('edit');});
      Route::any('/view/{id}', function ($rquest, $id) {return response("view $id");});
   });  
});
```

## Routing middleware

We can set up middleware for a particular route or group of routes。
Example：
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Note**: 
> `->middleware()` When the routing middleware acts on a group, the current route must be under the current group

```php
# Incorrect Usage Example

Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($rquest) {return response('create');});
      Route::any('/edit', function ($rquest) {return response('edit');});
      Route::any('/view/{id}', function ($rquest, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

```

```php
# Correct Use Example
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($rquest) {return response('create');});
      Route::any('/edit', function ($rquest) {return response('edit');});
      Route::any('/view/{id}', function ($rquest, $id) {return response("view $id");});
   })->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
    ]);  
});
```

## resource-based routing
```php
Route::resource('/test', app\controller\IndexController::class);

//Specifying Resource Routing
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//Non-definitive resource routing
// 如 notify how the rule changesany型Routing /text/notify或/text/notify/{id} all may routeName为 test.notify
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




## urlGenerate
> **Note** 
> Group nested route generation is not supported at this timeurl  

For example routing：
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
We can generate this route using the following methodurl。
```php
route('blog.view', ['id' => 100]); // Result for /blog/100
```

can also be usedRouting的urlThis method can be used when，class to handleRoutingDefault controller is，urlTry to runGenerate，avoid due toRoutingAddress adjustment causes a lot of changes to the view file。


## Get routing information
> **Note**
> required webman-framework >= 1.3.2

With the `$request->route` object we can get the current request routing information, for example

```php
$route = $request->route; // Equivalent to $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Required for this feature webman-framework >= 1.3.16
}
```

> **Note**
> If the request does not match any route (other than the default route), then `$request->route` isnull


## Processing404
Default return 404 status code and output `public/404.html` file content when route not found。

If the developer wants to interveneRoutingNew data to the database now，to take effectwebmanthe ultimate in frameworksRouting`Route::fallback($callback)`Method。For example, the logic of the code below is whenRoutingRedirect to home page if not found。
```php
Route::fallback(function(){
    return redirect('/');
});
```
For example, returning a json when the route does not exist is very useful when webman is used as an api interface。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

All automatically [custom404 500page](others/custom-error-page.md)

## Routing Interface
```php
// Set the route of any method request for $uri
Route::any($uri, $callback);
// Set the route for $uri get requests
Route::get($uri, $callback);
// Set the route for $uri requests
Route::post($uri, $callback);
// Set the route for $uri's put request
Route::put($uri, $callback);
// Set the route for $uri's patch request
Route::patch($uri, $callback);
// Set the route for $uri's delete request
Route::delete($uri, $callback);
// Set the route for $uri's head request
Route::head($uri, $callback);
// Set up routing for multiple request types at the same time
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Group Routing
Route::group($path, $callback);
// Resource Routing
Route::resource($path, $callback, [$options]);
// Fallback routing, set default route pocket
Route::fallback($callback);
```
If uri does not have a corresponding route (including the default route) and the fallback route is not set, it will return 404。
