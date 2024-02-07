## Archivo de configuración de rutas
El archivo de configuración de rutas del plugin se encuentra en `plugin/nombre_del_plugin/config/route.php`.

## Ruta por defecto
Las direcciones URL de los plugins de la aplicación comienzan con `/app`, por ejemplo, la dirección URL para `plugin\foo\app\controller\UserController` es `http://127.0.0.1:8787/app/foo/user`.

## Deshabilitar la ruta por defecto
Si desea deshabilitar la ruta por defecto de un plugin de la aplicación, configure lo siguiente en la configuración de la ruta:
```php
Route::disableDefaultRoute('foo');
```

## Manejar la devolución de error 404
Si desea establecer un fallback para un plugin de la aplicación, debe pasar el nombre del plugin como segundo parámetro, por ejemplo:
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
