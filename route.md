## 路由
## 默认路由规则
webman默认路由规则是 `http://127.0.0.1:8787/{控制器}/{动作}`。

默认控制器为`app\controller\Index`，默认动作为`index`。

例如访问`http://127.0.0.1:8787/foo/test` 将默认访问`app\controller\Foo`类的`test`方法。 

当您想改变某个请求路由时请更改配置文件 `config/route.php`。

## 闭包路由
`config/route.php`里添加如下路由代码
```php
Route::any('/test', function ($request) {
    return response('test');
});

```

当访问地址为 `http://127.0.0.1:8787/test` 时，将返回`test`字符串。

## 类路由
`config/route.php`里添加如下路由代码
```php
Route::any('/testclass', 'app\controller\Index@test');
```

当访问地址为 `http://127.0.0.1:8787/testclass` 时，将返回`app\controller\Index`类的`test`方法的返回值。

## 路由参数
```php
// 匹配 /user/123 /user/abc
Route::any('/user/{id}', function ($request, $id) {
    return response($id);
});
```

```php
// 匹配 /user/123, 不匹配 /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// 匹配 /user/foobar, 不匹配 /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// 匹配 /user /user/123 和 /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tome');
});
```


## 路由接口
```php
// 设置$uri的任意方法请求的路由
Route::any($uri, $callback);
// 设置$uri的get请求的路由
Route::get($uri, $callback);
// 设置$uri的请求的路由
Route::post($uri, $callback);
// 设置$uri的put请求的路由
Route::put($uri, $callback);
// 设置$uri的patch请求的路由
Route::patch($uri, $callback);
// 设置$uri的delete请求的路由
Route::delete($uri, $callback);
// 设置$uri的head请求的路由
Route::head($uri, $callback);
// 回退路由，设置默认的路由兜底
Route::fallback($callback);
```

如果uri没有对应方法的路由，并且默认路由也不存在，且回退路由也未设置，则会返回404。