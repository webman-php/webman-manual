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
        // Obtén el parámetro name de la solicitud GET, si no se proporciona el parámetro name, devuelve $default_name
        $name = $request->get('name', $default_name);
        // Devolver una cadena al navegador
        return response('hello ' . $name);
    }
}
```

A través del objeto `$request`, podemos obtener cualquier dato relacionado con la solicitud.

**A veces queremos obtener el objeto `$request` de la solicitud actual en otras clases, en este caso, solo necesitamos usar la función de ayuda `request()`**;

## Obtener parámetros de solicitud GET

**Obtener todo el array GET**
```php
$request->get();
```
Si la solicitud no tiene parámetros GET, devuelve un array vacío.

**Obtener un valor del array GET**
```php
$request->get('name');
```
Si el array GET no contiene este valor, devolverá null.

También puedes pasar un valor predeterminado como segundo parámetro al método get. Si no se encuentra el valor correspondiente en el array GET, se devolverá el valor predeterminado. Por ejemplo:
```php
$request->get('name', 'tom');
```

## Obtener parámetros de solicitud POST
**Obtener todo el array POST**
```php
$request->post();
```
Si la solicitud no tiene parámetros POST, devuelve un array vacío.

**Obtener un valor del array POST**
```php
$request->post('name');
```
Si el array POST no contiene este valor, devolverá null.

Al igual que el método get, también puedes pasar un valor predeterminado como segundo parámetro al método post, si no se encuentra el valor correspondiente en el array POST, se devolverá el valor predeterminado. Por ejemplo:
```php
$request->post('name', 'tom');
```

## Obtener el cuerpo de la solicitud POST original
```php
$post = $request->rawBody();
```
Esta función es similar a la operación `file_get_contents("php://input");` en `php-fpm`. Se utiliza para obtener el cuerpo de la solicitud original http. Es muy útil para obtener datos de solicitudes POST en un formato no `application/x-www-form-urlencoded`.

## Obtener encabezados
**Obtener todo el array de encabezados**
```php
$request->header();
```
Si la solicitud no tiene encabezados, devuelve un array vacío. Nota: todas las claves son en minúsculas.

**Obtener un valor del array de encabezados**
```php
$request->header('host');
```
Si el array de encabezados no contiene este valor, devuelve null. Nota: todas las claves son en minúsculas.

Al igual que el método get, también puedes pasar un valor predeterminado como segundo parámetro al método header, si no se encuentra el valor correspondiente en el array de encabezados, se devolverá el valor predeterminado. Por ejemplo:
```php
$request->header('host', 'localhost');
```

## Obtener cookies
**Obtener todo el array de cookies**
```php
$request->cookie();
```
Si la solicitud no tiene cookies, devuelve un array vacío.

**Obtener un valor del array de cookies**
```php
$request->cookie('name');
```
Si el array de cookies no contiene este valor, devuelve null.

Al igual que el método get, también puedes pasar un valor predeterminado como segundo parámetro al método cookie, si no se encuentra el valor correspondiente en el array de cookies, se devolverá el valor predeterminado. Por ejemplo:
```php
$request->cookie('name', 'tom');
```

## Obtener todos los datos de entrada
Incluye la colección de `post` y `get`.
```php
$request->all();
```

## Obtener un valor de entrada específico
Obtiene un valor específico de la colección `post` o `get`.
```php
$request->input('name', $valor_predeterminado);
```

## Obtener datos de entrada parcial
Obtiene datos parciales de la colección `post` o `get`.
```php
// Obtener un array compuesto por username y password, si no existe la clave correspondiente, se ignora
$only = $request->only(['username', 'password']);
// Obtener todos los valores de entrada excepto avatar y age.
$except = $request->except(['avatar', 'age']);
```

## Obtener archivos cargados
**Obtener todos los archivos cargados**
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
Es un array de instancias `webman\Http\UploadFile`. La clase `webman\Http\UploadFile` hereda de la clase nativa de PHP [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) y proporciona algunos métodos prácticos.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Verifica si el archivo es válido, por ejemplo verdadero|falso
            var_export($spl_file->getUploadExtension()); // Obtiene la extensión del archivo cargado, por ejemplo 'jpg'
            var_export($spl_file->getUploadMimeType()); // Obtiene el tipo MIME del archivo cargado, por ejemplo 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Obtiene el código de error de carga, por ejemplo UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Obtiene el nombre del archivo cargado, por ejemplo 'mi-prueba.jpg'
            var_export($spl_file->getSize()); // Obtiene el tamaño del archivo, por ejemplo 13364, en bytes
            var_export($spl_file->getPath()); // Obtiene el directorio de carga, por ejemplo '/tmp'
            var_export($spl_file->getRealPath()); // Obtiene el camino real del archivo temporal, por ejemplo `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Nota:**

- El archivo se renombrará como un archivo temporal después de cargarse, por ejemplo, `/tmp/workerman.upload.SRliMu`
- El tamaño del archivo cargado está limitado por [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), 10 MB de forma predeterminada, se puede cambiar el valor predeterminado en el archivo `config/server.php` modificando `max_package_size`.
- El archivo temporal se eliminará automáticamente al finalizar la solicitud.
- Si la solicitud no contiene archivos cargados, `$request->file()` devolverá un array vacío.
- Los archivos cargados no admiten el método `move_uploaded_file()`, utiliza el método `$file->move()` como reemplazo, consulta el ejemplo a continuación.

### Obtener un archivo cargado específico
```php
$request->file('avatar');
```
Si el archivo existe, devuelve una instancia `webman\Http\UploadFile` correspondiente, de lo contrario, devuelve null.

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
            return json(['code' => 0, 'msg' => 'carga exitosa']);
        }
        return json(['code' => 1, 'msg' => 'archivo no encontrado']);
    }
}
```

## Obtener host
Obtiene la información del host de la solicitud.
```php
$request->host();
```
Si la dirección de la solicitud no es el puerto estándar 80 o 443, la información del host puede incluir el puerto, por ejemplo, `example.com:8080`. Si no se desea el puerto, se puede pasar `true` como primer parámetro.

```php
$request->host(true);
```

## Obtener el método de la solicitud
```php
$request->method();
```
El valor devuelto puede ser `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Obtener el URI de la solicitud
```php
$request->uri();
```
Devuelve el URI de la solicitud, incluyendo la parte del path y queryString.

## Obtener la ruta de la solicitud

```php
$request->path();
```
Devuelve la parte del path de la solicitud.

## Obtener la queryString de la solicitud

```php
$request->queryString();
```
Devuelve la parte de queryString de la solicitud.

## Obtener el URL de la solicitud
El método `url()` devuelve la URL sin el parámetro `query`.
```php
$request->url();
```
Devuelve algo como `//www.workerman.net/workerman-chat`

El método `fullUrl()` devuelve la URL con el parámetro `query`.
```php
$request->fullUrl();
```
Devuelve algo como `//www.workerman.net/workerman-chat?type=download`

> **Nota**
> `url()` y `fullUrl()` no incluyen la parte del protocolo (no devuelven http o https).
> Porque en el navegador, el uso de direcciones que comienzan con `//` automáticamente reconoce el protocolo del sitio actual y realiza la solicitud automáticamente con http o https.

Si estás utilizando un proxy nginx, asegúrate de agregar `proxy_set_header X-Forwarded-Proto $scheme;` a la configuración de nginx, [ver nginx proxy](others/nginx-proxy.md),
de esta manera, puedes usar `$request->header('x-forwarded-proto')` para determinar si es http o https, por ejemplo:
```php
echo $request->header('x-forwarded-proto'); // devuelve http o https
```

## Obtener la versión HTTP de la solicitud

```php
$request->protocolVersion();
```
Devuelve `1.1` o `1.0`.

## Obtener el sessionId de la solicitud

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
$request->getRemotePort();
```
## Obtener la IP real del cliente

```php
$request->getRealIp($safe_mode=true);
```

Cuando un proyecto utiliza un proxy (por ejemplo, nginx), usar `$request->getRemoteIp()` a menudo devuelve la IP del servidor proxy (como `127.0.0.1`, `192.168.x.x`) en lugar de la IP real del cliente. En estos casos, puede intentar usar `$request->getRealIp()` para obtener la IP real del cliente.

`$request->getRealIp()` intentará obtener la IP real del cliente de los campos de encabezado HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`.

> Debido a que los encabezados HTTP son fácilmente falsificables, la IP del cliente obtenida mediante este método no es completamente confiable, especialmente cuando `$safe_mode` es `false`. Un método más confiable para obtener la IP real del cliente a través de un proxy es si se conoce la IP segura del servidor proxy y se sabe exactamente qué encabezado HTTP lleva la IP real. Si la IP devuelta por `$request->getRemoteIp()` se confirma como la del servidor proxy seguro conocido, entonces se puede obtener la IP real a través de `$request->header('encabezado del HTTP que lleva la IP real')`.


## Obtener la IP del servidor

```php
$request->getLocalIp();
```

## Obtener el puerto del servidor

```php
$request->getLocalPort();
```

## Determinar si es una solicitud ajax

```php
$request->isAjax();
```

## Determinar si es una solicitud pjax

```php
$request->isPjax();
```

## Determinar si se espera una respuesta JSON

```php
$request->expectsJson();
```

## Determinar si el cliente acepta respuestas JSON

```php
$request->acceptJson();
```

## Obtener el nombre del plugin de la solicitud

Para solicitudes que no provienen de un plugin, devuelve una cadena vacía `''`.
```php
$request->plugin;
```
> Esta característica requiere webman>=1.4.0

## Obtener el nombre de la aplicación de la solicitud

Cuando solo hay una aplicación, siempre devuelve una cadena vacía `''`. En el caso de [aplicaciones múltiples](multiapp.md), devuelve el nombre de la aplicación.
```php
$request->app;
```

> Debido a que las funciones de cierre (closure) no pertenecen a ninguna aplicación, la solicitud de una ruta de cierre siempre devuelve una cadena vacía `''`.
> Consulte [Rutas de cierre](route.md) para obtener información sobre las rutas de cierre.

## Obtener el nombre de la clase del controlador de la solicitud

Obtener el nombre de la clase correspondiente al controlador
```php
$request->controller;
```
Devuelve algo similar a `app\controller\IndexController`

> Debido a que las funciones de cierre (closure) no pertenecen a ningún controlador, la solicitud de una ruta de cierre siempre devuelve una cadena vacía `''`.
> Consulte [Rutas de cierre](route.md) para obtener información sobre las rutas de cierre.

## Obtener el nombre del método de la solicitud

Obtener el nombre del método del controlador correspondiente a la solicitud
```php
$request->action;
```
Devuelve algo similar a `index`

> Debido a que las funciones de cierre (closure) no pertenecen a ningún controlador, la solicitud de una ruta de cierre siempre devuelve una cadena vacía `''`.
> Consulte [Rutas de cierre](route.md) para obtener información sobre las rutas de cierre.
