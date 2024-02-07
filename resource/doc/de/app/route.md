## Routenkonfigurationsdatei
Die Routenkonfigurationsdatei für das Plugin befindet sich unter `plugin/PluginName/config/route.php`.

## Standardroute
Die URL-Pfade für die Plugin-Anwendungen beginnen alle mit `/app`, zum Beispiel ist die URL-Adresse für `plugin\foo\app\controller\UserController` `http://127.0.0.1:8787/app/foo/user`.

## Deaktivierung der Standardroute
Wenn Sie die Standardroute für eine bestimmte Plugin-Anwendung deaktivieren möchten, setzen Sie in der Routenkonfiguration etwas Ähnliches wie:
```php
Route::disableDefaultRoute('foo');
```

## Behandlung von 404-Fallbacks
Wenn Sie für eine bestimmte Plugin-Anwendung ein Fallback festlegen möchten, müssen Sie den Plugin-Namen als zweiten Parameter übergeben, zum Beispiel:
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
