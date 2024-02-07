## 路由設定檔
插件的路由設定檔位於`plugin/插件名/config/route.php`

## 默認路由
應用插件的URL地址都以`/app`開頭，例如`plugin\foo\app\controller\UserController`的URL地址是 `http://127.0.0.1:8787/app/foo/user`

## 禁用默認路由
如果想禁用某個應用插件的默認路由，可在路由設定中設置類似於
```php
Route::disableDefaultRoute('foo');
```

## 處理404回調
如果想為某個應用插件設置fallback，需要透過第二個參數傳遞插件名，例如
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
