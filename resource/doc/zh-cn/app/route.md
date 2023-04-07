## 路由
应用插件url地址路径都以`/app`开头，例如`plugin\foo\app\controller\UserController`url地址是 `http://127.0.0.1:8787/app/foo/user`。
其它与webman开发体验一致。

## 禁用插件的默认路由
如果想禁用某个应用插件的路由，在`plugin/插件名/config/route.php`中设置类似
```php
Route::disableDefaultRoute('foo');
```
因为路由是全局的，所以也可以在主项目的路由配置里禁用某个插件的默认路由。
