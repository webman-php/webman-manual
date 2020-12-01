## 路由
## 默认路由规则
webman默认路由规则是 `http://127.0.0.1:8787/{控制器}/{动作}`。

默认控制器为`app\controller\Index`，默认动作为`index`。

例如访问：
- `http://127.0.0.1:8787` 将默认访问`app\controller\Index`类的`index`方法
- `http://127.0.0.1:8787/foo` 将默认访问`app\controller\Foo`类的`index`方法
- `http://127.0.0.1:8787/foo/test` 将默认访问`app\controller\Foo`类的`test`方法
- `http://127.0.0.1:8787/admin/foo/test` 将默认访问`app\admin\controller\Foo`类的`test`方法 (参考[多应用](multiapp.md))

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
如果路由中存在参数，通过`{key}`来匹配，匹配结果将传递到对应的控制器方法参数中(从第二个参数开始依次传递)，例如：
```php
// 匹配 /user/123 /user/abc
Route::any('/user/{id}', 'app\controller\User@get');
```
```php
namespace app\controller;
class User
{
    public function get($request, $id)
    {
        return response('接收到参数'.$id);
    }
}
```

更多例子：
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
   return response($name ?? 'tom');
});
```

## 路由分组
> 注意：分组路由需要 webman-framework版本 >= 1.0.9

有时候路由包含了大量相同的前缀，这时候我们可以用路由分组来简化定义。例如：

```php
Route::group('/blog', function () {
   Route::any('/create', function ($rquest) {return response('create');});
   Route::any('/edit', function ($rquest) {return response('edit');});
   Route::any('/view/{id}', function ($rquest, $id) {return response("view $id");});
});
```
等价与
```php
Route::any('/blog/create', function ($rquest) {return response('create');});
Route::any('/blog/edit', function ($rquest) {return response('edit');});
Route::any('/blog/view/{id}', function ($rquest, $id) {return response("view $id");});
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
// 分组路由
Route::group($path, $callback);
// 回退路由，设置默认的路由兜底
Route::fallback($callback);
```

如果uri没有对应方法的路由，并且默认路由也不存在，且回退路由也未设置，则会返回404。