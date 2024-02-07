## 路由設定檔
插件的路由設定檔位於 `plugin/插件名/config/route.php`

## 預設路由
應用插件的URL地址路徑都以`/app`開頭，例如`plugin\foo\app\controller\UserController`的URL地址是 `http://127.0.0.1:8787/app/foo/user`

## 禁用預設路由
如果想要禁用某個應用插件的預設路由，在路由設定中設定類似
```php
Route::disableDefaultRoute('foo');
```

## 處理404回調
如果想要給某個應用插件設定fallback，需要透過第二個參數傳遞插件名稱，例如
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
