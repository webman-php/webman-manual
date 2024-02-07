# Documentación

## Obtener el objeto de solicitud
Webman inyectará automáticamente el objeto de solicitud en el primer parámetro del método de acción, por ejemplo

**Ejemplo**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obtener el parámetro name de la solicitud GET, si no se pasa name, se devuelve $default_name
        $name = $request->get('name', $default_name);
        // Devuelve una cadena al navegador
        return response('hola ' . $name);
    }
}
```

A través del objeto `$request` podemos obtener cualquier dato relacionado con la solicitud.

**A veces queremos obtener el objeto `$request` de la solicitud actual en otra clase, en este caso, solo necesitamos usar la función de ayuda `request()`*;

## Obtener parámetros de solicitud GET

**Obtener todo el array GET**
```php
$request->get();
```
Si la solicitud no tiene parámetros GET, devuelve un array vacío.

**Obtener un valor específico del array GET**
```php
$request->get('name');
```
Si el array GET no contiene este valor, devuelve nulo.

También puedes pasar un segundo parámetro a la función get con un valor predeterminado. Si el valor correspondiente no se encuentra en el array GET, se devuelve el valor predeterminado. Por ejemplo:
```php
$request->get('name', 'tom');
```

## Obtener parámetros de solicitud POST
**Obtener todo el array POST**
```php
$request->post();
```
Si la solicitud no tiene parámetros POST, devuelve un array vacío.

**Obtener un valor específico del array POST**
```php
$request->post('name');
```
Si el array POST no contiene este valor, devuelve nulo.

Al igual que el método get, también puedes pasar un segundo parámetro a la función post con un valor predeterminado. Si el valor correspondiente no se encuentra en el array POST, se devuelve el valor predeterminado. Por ejemplo:
```php
$request->post('name', 'tom');
```

## Obtener cuerpo de solicitud POST crudo
```php
$post = $request->rawBody();
```
Esta función es similar a la operación `file_get_contents("php://input");` en `php-fpm`. Se utiliza para obtener el cuerpo crudo de la solicitud HTTP. Es muy útil para obtener datos de solicitudes POST en formatos no `application/x-www-form-urlencoded`.

## Obtener encabezados
**Obtener todo el array de encabezados**
```php
$request->header();
```
Si la solicitud no tiene encabezados, devuelve un array vacío. Nota que todas las claves están en minúsculas.

**Obtener un valor específico del array de encabezados**
```php
$request->header('host');
```
Si el array de encabezados no contiene este valor, devuelve nulo. Nota que todas las claves están en minúsculas.

Al igual que el método get, también puedes pasar un segundo parámetro a la función header con un valor predeterminado. Si el array de encabezados no contiene el valor correspondiente, se devuelve el valor predeterminado. Por ejemplo:
```php
$request->header('host', 'localhost');
```

## Obtener cookies
**Obtener todo el array de cookies**
```php
$request->cookie();
```
Si la solicitud no tiene cookies, devuelve un array vacío.

**Obtener un valor específico del array de cookies**
```php
$request->cookie('name');
```
Si el array de cookies no contiene este valor, devuelve nulo.

Al igual que el método get, también puedes pasar un segundo parámetro a la función cookie con un valor predeterminado. Si el array de cookies no contiene el valor correspondiente, se devuelve el valor predeterminado. Por ejemplo:
```php
$request->cookie('name', 'tom');
```

## Obtener todas las entradas
Incluye la colección de `post` y `get`.
```php
$request->all();
```

## Obtener un valor de entrada específico
Obtener un valor específico de la colección de `post` y `get`.
```php
$request->input('name', $valor_predeterminado);
```

## Obtener datos de entrada parciales
Obtener datos parciales de la colección de `post` y `get`.
```php
// Obtener un array compuesto por username y password, si la clave correspondiente no existe, se ignora
$only = $request->only(['username', 'password']);
// Obtener todos los datos de entrada excepto avatar y age
$except = $request->except(['avatar', 'age']);
```

## Obtener archivos cargados
**Obtener el array completo de archivos cargados**
```php
$request->file();
```

Formulario similar a:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

El formato devuelto por `$request->file()` es similar a:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Es una matriz de instancias de `webman\Http\UploadFile`. La clase `webman\Http\UploadFile` hereda de la clase [`SplFileInfo`] (https://www.php.net/manual/zh/class.splfileinfo.php) nativa de PHP y proporciona algunos métodos útiles.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $clave => $spl_file) {
            var_export($spl_file->isValid()); // Si el archivo es válido, por ejemplo, true|false
            var_export($spl_file->getUploadExtension()); // Extensión del archivo cargado, por ejemplo, 'jpg'
            var_export($spl_file->getUploadMimeType()); // Tipo MIME del archivo cargado, por ejemplo, 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Obtener el código de error de carga, por ejemplo, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Nombre del archivo cargado, por ejemplo, 'mi-prueba.jpg'
            var_export($spl_file->getSize()); // Obtener el tamaño del archivo, por ejemplo, 13364, en bytes
            var_export($spl_file->getPath()); // Obtener el directorio de carga, por ejemplo, '/tmp'
            var_export($spl_file->getRealPath()); // Obtener la ruta del archivo temporal, por ejemplo, `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Nota:**

- Después de cargarse, el archivo se nombrará como un archivo temporal, por ejemplo, `/tmp/workerman.upload.SRliMu`
- El tamaño de archivo cargado está limitado por [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), por defecto 10M, se puede cambiar el valor predeterminado en el archivo `config/server.php` modificando `max_package_size`.
- Después de la solicitud, el archivo temporal se eliminará automáticamente.
- Si la solicitud no tiene archivos cargados, `$request->file()` devolverá una matriz vacía.
- Los archivos cargados no admiten el método `move_uploaded_file()`, utiliza el método `$file->move()` en su lugar, ver el ejemplo a continuación.

### Obtener un archivo cargado específico
```php
$request->file('avatar');
```
Si el archivo existe, devuelve la instancia correspondiente de `webman\Http\UploadFile`, de lo contrario, devuelve nulo.

**Ejemplo**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/miarchivo.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'subida exitosa']);
        }
        return json(['code' => 1, 'msg' => 'archivo no encontrado']);
    }
}
```

## Obtener anfitrión
Obtener la información de host de la solicitud.
```php
$request->host();
```
Si la dirección de la solicitud no es un estándar 80 o 443, la información de host puede incluir el puerto, por ejemplo, `ejemplo.com:8080`. Si no se necesita el puerto, se puede pasar `true` como primer parámetro.

```php
$request->host(true);
```

## Obtener el método de solicitud
```php
 $request->method();
```
El valor devuelto puede ser uno de `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Obtener la uri de la solicitud
```php
$request->uri();
```
Devuelve la uri de la solicitud, incluyendo la parte del path y queryString.

## Obtener la ruta de la solicitud
```php
$request->path();
```
Devuelve la parte del path de la solicitud.

## Obtener queryString de la solicitud
```php
$request->queryString();
```
Devuelve la parte queryString de la solicitud.
## Obtener la URL de la solicitud
El método `url()` devuelve la URL sin parámetros de consulta.
```php
$request->url();
```
Devuelve algo similar a `//www.workerman.net/workerman-chat`

El método `fullUrl()` devuelve la URL con parámetros de consulta.
```php
$request->fullUrl();
```
Devuelve algo similar a `//www.workerman.net/workerman-chat?type=download`

> **Nota**
> `url()` y `fullUrl()` no devuelven la parte del protocolo (no devuelven http o https).
> Esto se debe a que en un navegador, utilizar una dirección que comience con `//` reconoce automáticamente el protocolo del sitio actual, enviando la solicitud automáticamente como http o https.

Si estás utilizando un proxy nginx, agrega `proxy_set_header X-Forwarded-Proto $scheme;` a la configuración de nginx, [consultar proxy nginx](others/nginx-proxy.md), de esta manera se puede usar `$request->header('x-forwarded-proto');` para determinar si se trata de http o https, por ejemplo:
```php
echo $request->header('x-forwarded-proto'); // Muestra http o https
```

## Obtener la versión del protocolo de la solicitud

```php
$request->protocolVersion();
```
Devuelve la cadena `1.1` o `1.0`.


## Obtener ID de sesión de la solicitud

```php
$request->sessionId();
```
Devuelve una cadena compuesta por letras y números.


## Obtener la IP del cliente de la solicitud
```php
$request->getRemoteIp();
```

## Obtener el puerto del cliente de la solicitud
```php
request->getRemotePort();
```

## Obtener la IP real del cliente de la solicitud
```php
$request->getRealIp($safe_mode=true);
```

Cuando el proyecto utiliza un proxy (por ejemplo, nginx), al usar `$request->getRemoteIp()` generalmente se obtiene la IP del servidor proxy (similar a `127.0.0.1` `192.168.x.x`) en lugar de la IP real del cliente. En este caso, se puede intentar utilizar `$request->getRealIp()` para obtener la IP real del cliente.

`$request->getRealIp()` intentará obtener la IP real a partir de los encabezados HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip` y `via`.

> Debido a que los encabezados HTTP son muy fáciles de falsificar, la IP del cliente obtenida mediante este método no es completamente confiable, especialmente cuando `$safe_mode` es false. Una forma más confiable de obtener la IP real del cliente a través de un proxy es conocer la IP del servidor proxy segura y saber claramente qué encabezado HTTP lleva la IP real, si `$request->getRemoteIp()` devuelve la IP del servidor proxy segura conocida, entonces se puede obtener la IP real a través de `$request->header('encabezado HTTP que lleva la IP real')`.

## Obtener la IP del servidor
```php
$request->getLocalIp();
```

## Obtener el puerto del servidor
```php
$request->getLocalPort();
```

## Verificar si es una solicitud ajax
```php
$request->isAjax();
```

## Verificar si es una solicitud de pjax
```php
$request->isPjax();
```

## Verificar si se espera una respuesta en formato json
```php
$request->expectsJson();
```

## Verificar si el cliente acepta respuestas en formato json
```php
$request->acceptJson();
```

## Obtener el nombre del plugin de la solicitud
Para solicitudes que no son de plugin, devuelve una cadena vacía `''`.
```php
$request->plugin;
```
> Esta característica requiere webman>=1.4.0

## Obtener el nombre de la aplicación de la solicitud
Para una única aplicación, siempre devuelve una cadena vacía `''`, [múltiples aplicaciones](multiapp.md) devuelve el nombre de la aplicación.
```php
$request->app;
```
> Debido a que las funciones de cierre no pertenecen a ninguna aplicación, el nombre de la solicitud de la ruta de cierre `$request->app` siempre devuelve una cadena vacía `''`.
> Consultar ruta de cierre en [ruta](route.md)

## Obtener el nombre de la clase controladora de la solicitud
Obtiene el nombre de la clase correspondiente al controlador
```php
$request->controller;
```
Devuelve algo similar a `app\controller\IndexController`

> Debido a que las funciones de cierre no pertenecen a ningún controlador, el nombre de la solicitud de la ruta de cierre `$request->controller` siempre devuelve una cadena vacía `''`.
> Consultar ruta de cierre en [ruta](route.md)

## Obtener el nombre del método de la solicitud
Obtiene el nombre del método del controlador correspondiente a la solicitud
```php
$request->action;
```
Devuelve algo similar a `index`

> Debido a que las funciones de cierre no pertenecen a ningún controlador, el nombre de la solicitud de la ruta de cierre `$request->action` siempre devuelve una cadena vacía `''`.
> Consultar ruta de cierre en [ruta](route.md)
