## Archivo de configuración de rutas
El archivo de configuración de rutas del complemento se encuentra en `plugin/nombre_del_complemento/config/route.php`

## Ruta predeterminada
El camino de la URL de la aplicación del complemento comienza con `/app`, por ejemplo, la URL del controlador `plugin\foo\app\controller\UserController` es `http://127.0.0.1:8787/app/foo/user`

## Deshabilitar la ruta predeterminada
Si desea deshabilitar la ruta predeterminada de un complemento de la aplicación, configure algo similar en la configuración de la ruta
```php
Route::disableDefaultRoute('foo');
```

## Manejo de la devolución de 404
Si desea establecer un fallback para un complemento de la aplicación, debe pasar el nombre del complemento como segundo parámetro, por ejemplo
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
