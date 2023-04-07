## 路由配置文件
插件的路由配置文件在 `plugin/插件名/config/route.php`

## 默认路由
应用插件url地址路径都以`/app`开头，例如`plugin\foo\app\controller\UserController`url地址是 `http://127.0.0.1:8787/app/foo/user`

## 禁用默认路由
如果想禁用某个应用插件的默认路由，在路由配置中设置类似
```php
Route::disableDefaultRoute('foo');
```

## 处理404回调
如果想给某个应用插件设置fallback，需要通过第二个参数传递插件名，例如
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```