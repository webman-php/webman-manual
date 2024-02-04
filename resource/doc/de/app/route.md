## Routenkonfigurationsdatei
Die Routenkonfigurationsdatei des Plugins befindet sich unter `plugin/PluginName/config/route.php`.

## Standardroute
Die URL-Pfade der Plug-in-Anwendungen beginnen alle mit `/app`, z. B. ist die URL-Adresse von `plugin\foo\app\controller\UserController` `http://127.0.0.1:8787/app/foo/user`.

## Deaktivierte Standardroute
Wenn Sie die Standardroute einer bestimmten Plug-in-Anwendung deaktivieren möchten, legen Sie dies in der Routenkonfiguration fest, beispielsweise so:
```php
Route::disableDefaultRoute('foo');
```

## Behandlung des 404-Rückrufs
Wenn Sie einer bestimmten Plug-in-Anwendung ein Fallback geben möchten, übergeben Sie den Plugin-Namen als zweiten Parameter, zum Beispiel:
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
