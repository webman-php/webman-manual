## ルート構成ファイル
プラグインのルート構成ファイルは `plugin/プラグイン名/config/route.php` にあります。

## デフォルトのルート
アプリケーションプラグインのURLパスはすべて `/app` で始まります。例えば、`plugin\foo\app\controller\UserController` のURLは `http://127.0.0.1:8787/app/foo/user` です。

## デフォルトのルートを無効にする
特定のアプリケーションプラグインのデフォルトルートを無効にしたい場合は、ルート構成で以下のように設定します。
```php
Route::disableDefaultRoute('foo');
```

## 404エラーのハンドリング
特定のアプリケーションプラグインにfallbackを設定したい場合は、第二引数でプラグイン名を渡す必要があります。例えば、以下のようにします。
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
