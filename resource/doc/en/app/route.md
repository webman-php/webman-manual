## Routing Configuration File
The routing configuration file for a plugin is located at `plugin/plugin_name/config/route.php`.

## Default Routing
The URL path for plugin applications starts with `/app`. For example, the URL for `plugin\foo\app\controller\UserController` is `http://127.0.0.1:8787/app/foo/user`.

## Disabling Default Routing
If you want to disable default routing for a specific plugin application, you can set it in the routing configuration file. For example:
```php
Route::disableDefaultRoute('foo');
```

## Handling 404 Error Callback
If you want to set a fallback for a specific plugin application, you can pass the plugin name as the second parameter in the fallback function. For example:
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
