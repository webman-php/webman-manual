## Manejo de archivos estáticos

webman admite el acceso a archivos estáticos, que se colocan en el directorio `public`. Por ejemplo, al acceder a `http://127.0.0.8787/upload/avatar.png`, en realidad se accede a `{directorio del proyecto principal}/public/upload/avatar.png`.

> **Nota**
> A partir de webman 1.4, se admite la funcionalidad de complementos de la aplicación. El acceso a archivos estáticos que comienza con `/app/xx/nombre de archivo` en realidad accede al directorio `public` del complemento de la aplicación. Esto significa que webman >=1.4.0 no admite el acceso al directorio `{directorio del proyecto principal}/public/app/`.
> Para más información, consulta [Complementos de la aplicación](./plugin/app.md).

### Deshabilitar el soporte de archivos estáticos
Si no se necesita el soporte de archivos estáticos, puedes abrir `config/static.php` y cambiar la opción `enable` a `false`. Una vez deshabilitado, cualquier acceso a archivos estáticos devolverá un código de estado 404.

### Cambiar el directorio de archivos estáticos
Por defecto, webman utiliza el directorio `public` como directorio de archivos estáticos. Si necesitas cambiarlo, modifica la función de ayuda `public_path()` en `support/helpers.php`.

### Middleware de archivos estáticos
webman incluye un middleware para archivos estáticos, ubicado en `app/middleware/StaticFile.php`.
A veces es necesario realizar cierto procesamiento en los archivos estáticos, como agregar encabezados de HTTP para permitir el acceso desde otros dominios o prohibir el acceso a archivos que comienzan con punto (`.`).

El contenido de `app/middleware/StaticFile.php` es el siguiente:
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
        // Prohibir el acceso a archivos ocultos que comienzan con .
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 prohibido</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Agregar encabezados de HTTP para permitir el acceso desde otros dominios
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Si necesitas este middleware, debes habilitarlo en la opción `middleware` en `config/static.php`.
