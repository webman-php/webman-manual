## Manejo de archivos estáticos
webman admite el acceso a archivos estáticos, los cuales se almacenan en el directorio `public`. Por ejemplo, acceder a `http://127.0.0.8787/upload/avatar.png` en realidad es acceder a `{directorio_principal_del_proyecto}/public/upload/avatar.png`.

> **Nota**
> A partir de la versión 1.4, webman admite complementos de la aplicación. El acceso a archivos estáticos que comienzan con `/app/xx/nombre_archivo` en realidad accede al directorio `public` del complemento de la aplicación. Es decir, webman >=1.4.0 no es compatible con el acceso a directorios en `{directorio_principal_del_proyecto}/public/app/`.
> Para más información, consulta [Complementos de la aplicación](./plugin/app.md).

### Desactivar el soporte de archivos estáticos
Si no se necesita el soporte de archivos estáticos, cambia la opción `enable` a false en `config/static.php`. Después de desactivar esta opción, cualquier intento de acceder a archivos estáticos devolverá un error 404.

### Cambiar el directorio de archivos estáticos
Por defecto, webman utiliza el directorio public como el directorio de archivos estáticos. Si necesitas cambiar esto, modifica la función auxiliar `public_path()` en `support/helpers.php`.

### Middleware de archivos estáticos
webman viene con un middleware de archivos estáticos, ubicado en `app/middleware/StaticFile.php`. A veces es necesario realizar algunas acciones en los archivos estáticos, como agregar encabezados de CORS o prohibir el acceso a archivos que comienzan con punto (`.`).

El contenido de `app/middleware/StaticFile.php` es similar al siguiente:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Prohibir el acceso a archivos ocultos que comienzan con punto
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Agregar encabezados de CORS
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Si necesitas utilizar este middleware, asegúrate de habilitarlo en la opción `middleware` en `config/static.php`.
