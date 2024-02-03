## ルート設定ファイル
プラグインのルート設定ファイルは `plugin/プラグイン名/config/route.php` にあります。

## デフォルトルート
アプリケーションプラグインのURLパスはすべて `/app` で始まります。例えば、`plugin\foo\app\controller\UserController` のURLは `http://127.0.0.1:8787/app/foo/user` です。

## デフォルトルートの無効化
特定のアプリケーションプラグインのデフォルトルートを無効化したい場合は、ルート設定で以下のように設定します。
```php
Route::disableDefaultRoute('foo');
```

## 404コールバックの処理
特定のアプリケーションプラグインにfallbackを設定したい場合は、第2引数を使用して以下のように設定します。
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
