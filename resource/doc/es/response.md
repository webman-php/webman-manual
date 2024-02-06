# Respuesta
La respuesta es realmente un objeto `support\Response`. Para facilitar la creación de este objeto, webman proporciona algunas funciones auxiliares.

## Devolver una respuesta arbitraria

**Ejemplo**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

La implementación de la función `response` es la siguiente:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

También puedes crear un objeto `response` vacío y luego usar `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` para configurar el contenido a devolver en el lugar adecuado.
```php
public function hello(Request $request)
{
    // Crear un objeto
    $response = response();
    
    // .... Lógica de negocio omitida
    
    // Configurar cookie
    $response->cookie('foo', 'value');
    
    // .... Lógica de negocio omitida
    
    // Configurar encabezados HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor del Encabezado 1',
                'X-Header-Dos' => 'Valor del Encabezado 2',
            ]);

    // .... Lógica de negocio omitida

    // Configurar los datos a devolver
    $response->withBody('Datos a devolver');
    return $response;
}
```

## Devolver json
**Ejemplo**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

La implementación de la función `json` es la siguiente:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## Devolver xml
**Ejemplo**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```

La implementación de la función `xml` es la siguiente:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Devolver vista
Crea el archivo `app/controller/FooController.php` de la siguiente manera:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Crea el archivo `app/view/foo/hello.html` de la siguiente manera:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Redireccionamiento
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```

La implementación de la función `redirect` es la siguiente:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Configuración de encabezados
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Valor del Encabezado' 
        ]);
    }
}
```
También puedes utilizar los métodos `header` y `withHeaders` para establecer un encabezado individual o en lotes.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Valor del Encabezado 1',
            'X-Header-Dos' => 'Valor del Encabezado 2',
        ]);
    }
}
```
También puedes configurar los encabezados de antemano y finalmente configurar los datos a devolver.

## Configuración de cookies
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```
También puedes configurar las cookies de antemano y finalmente configurar los datos a devolver.

```php
public function hello(Request $request)
{
    // Crear un objeto
    $response = response();
    
    // .... Lógica de negocio omitida
    
    // Configurar cookie
    $response->cookie('foo', 'value');
    
    // .... Lógica de negocio omitida

    // Configurar los datos a devolver
    $response->withBody('Datos a devolver');
    return $response;
}
```

El método `cookie` tiene los siguientes parámetros:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Devolver flujo de archivos
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```
- webman soporta el envío de archivos extremadamente grandes
- Para archivos grandes (más de 2 MB), webman no cargará todo el archivo en la memoria de una sola vez, en su lugar, leerá y enviará el archivo en segmentos en momentos apropiados
- webman optimizará la velocidad de lectura y envío del archivo en función de la velocidad de recepción del cliente, garantizando el envío más rápido del archivo minimizando el uso de memoria
- La transmisión de datos es no bloqueante y no afectará el manejo de otras solicitudes
- El método `file` agregará automáticamente el encabezado `if-modified-since` y comprobará el encabezado `if-modified-since` en la próxima solicitud. Si el archivo no ha sido modificado, devolverá directamente un código de estado 304 para ahorrar ancho de banda
- El archivo enviado utilizará automáticamente el encabezado `Content-Type` adecuado para enviar al navegador
- Si el archivo no existe, se convertirá automáticamente en una respuesta 404

## Descargar archivo
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'nombre_de_archivo.ico');
    }
}
```
El método `download` es básicamente igual al método `file`, pero con las diferencias de que:
1. Después de configurar el nombre del archivo para descargar, el archivo se descargará en lugar de mostrarse en el navegador
2. El método `download` no comprobará el encabezado `if-modified-since`

## Obtener salida
Algunas bibliotecas escriben directamente el contenido del archivo en la salida estándar, es decir, los datos se imprimen en la terminal en lugar de enviarse al navegador. En este caso, necesitamos capturar los datos en una variable usando `ob_start();` `ob_get_clean();` y luego enviar los datos al navegador, por ejemplo:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Crear imagen
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'Un Texto Simple', $text_color);

        // Comenzar a capturar la salida
        ob_start();
        // Imprimir imagen
        imagejpeg($im);
        // Obtener el contenido de la imagen
        $image = ob_get_clean();
        
        // Enviar la imagen
        return response($image)->header('Content-Type', 'imagen/jpeg');
    }
}
```
