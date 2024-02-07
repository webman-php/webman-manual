# Respuesta
La respuesta es en realidad un objeto `support\Response`. Para facilitar la creación de este objeto, webman proporciona algunas funciones de ayuda.

## Devolver cualquier respuesta

**Ejemplo**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hola webman');
    }
}
```

La función `response` se implementa de la siguiente manera:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

También puedes crear primero un objeto de respuesta vacío y luego, en el lugar apropiado, usar `$response->cookie()`, `$response->header()`, `$response->withHeaders()` o `$response->withBody()` para configurar el contenido a devolver.
```php
public function hello(Request $request)
{
    // Crear un objeto
    $response = response();
    
    // .... Lógica de negocio omitida
    
    // Configurar cookie
    $response->cookie('foo', 'valor');
    
    // .... Lógica de negocio omitida
    
    // Configurar encabezados HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor del encabezado 1',
                'X-Header-Tow' => 'Valor del encabezado 2',
            ]);

    // .... Lógica de negocio omitida

    // Configurar los datos a devolver
    $response->withBody('datos a devolver');
    return $response;
}
```

## Devolver JSON
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
La función `json` se implementa de la siguiente manera
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## Devolver XML
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
La función `xml` se implementa de la siguiente manera:
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
Crear archivo `app/controller/FooController.php` como se muestra a continuación

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

Crear archivo `app/view/foo/hello.html` como se muestra a continuación

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hola <?=htmlspecialchars($name)?>
</body>
</html>
```

## Redireccionar
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
La función de redirección se implementa de la siguiente manera:
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
        return response('hola webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Valor encabezado' 
        ]);
    }
}
```
También se pueden utilizar los métodos `header` y `withHeaders` para configurar un encabezado único o varios encabezados a la vez.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hola webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Valor del encabezado 1',
            'X-Header-Tow' => 'Valor del encabezado 2',
        ]);
    }
}
```
También puedes configurar los encabezados previamente y luego configurar los datos a devolver.

```php
public function hello(Request $request)
{
    // Crear un objeto
    $response = response();
    
    // .... Lógica de negocio omitida
  
    // Configurar encabezados HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valor del encabezado 1',
                'X-Header-Tow' => 'Valor del encabezado 2',
            ]);

    // .... Lógica de negocio omitida

    // Configurar los datos a devolver
    $response->withBody('datos a devolver');
    return $response;
}
```

## Establecer cookie
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hola webman')
        ->cookie('foo', 'valor');
    }
}
```
También puedes configurar la cookie previamente y luego configurar los datos a devolver.

```php
public function hello(Request $request)
{
    // Crear un objeto
    $response = response();
    
    // .... Lógica de negocio omitida
    
    // Configurar cookie
    $response->cookie('foo', 'valor');
    
    // .... Lógica de negocio omitida

    // Configurar los datos a devolver
    $response->withBody('datos a devolver');
    return $response;
}
```
El método de cookie tiene los siguientes parámetros completos:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Devolver flujo de archivo
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

- webman admite el envío de archivos muy grandes
- Para archivos grandes (más de 2M), webman no leerá el archivo completo en memoria de una vez, sino que lo leerá en segmentos en el momento adecuado y lo enviará
- webman optimizará la velocidad de lectura y envío del archivo según la velocidad a la que el cliente lo recibe, asegurando el envío más rápido posible del archivo al tiempo que minimiza el uso de memoria
- El envío de datos es no bloqueante y no afectará el manejo de otras peticiones
- El método `file` agregará automáticamente el encabezado de `if-modified-since` y verificará el encabezado `if-modified-since` en la próxima solicitud; si el archivo no ha sido modificado, devolverá directamente 304 para ahorrar ancho de banda
- El archivo enviado usará automáticamente el encabezado de `Content-Type` adecuado para enviar al navegador
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
        return response()->download(public_path() . '/favicon.ico', 'nombre_archivo.ico');
    }
}
```
El método de descarga `download` es básicamente igual que el método `file`, la diferencia es que:
1. Después de establecer el nombre del archivo para descargar, se descargará el archivo en lugar de mostrarlo en el navegador
2. El método `download` no verificará el encabezado `if-modified-since`
## Obtener la salida
Algunas bibliotecas imprimen directamente el contenido del archivo en la salida estándar, es decir, los datos se imprimen en la terminal de la línea de comandos y no se envían al navegador. En este caso, necesitamos capturar los datos en una variable a través de `ob_start();` y `ob_get_clean();`, y luego enviar los datos al navegador. Por ejemplo:

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
        imagestring($im, 1, 5, 5,  'Simple Text String', $text_color);

        // Comenzar a capturar la salida
        ob_start();
        // Imprimir imagen
        imagejpeg($im);
        // Obtener el contenido de la imagen
        $image = ob_get_clean();
        
        // Enviar la imagen
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
