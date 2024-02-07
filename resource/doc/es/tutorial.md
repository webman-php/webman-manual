# Ejemplo simple

## Devolución de una cadena
**Crear un controlador**

Crear un archivo `app/controller/UserController.php` con el siguiente contenido:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obtener el parámetro 'name' de la solicitud GET, si no se pasa el parámetro, devolver $default_name
        $name = $request->get('name', $default_name);
        // Devolver una cadena al navegador
        return response('hello ' . $name);
    }
}
```

**Acceder**

Acceder a `http://127.0.0.1:8787/user/hello?name=tom` en el navegador

El navegador devolverá `hello tom`

## Devolución de JSON
Modificar el archivo `app/controller/UserController.php` de la siguiente manera:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Acceder**

Acceder a `http://127.0.0.1:8787/user/hello?name=tom` en el navegador

El navegador devolverá `{"code":0,"msg":"ok","data":"tom""}`

El uso de la función de ayuda json para devolver datos automáticamente agrega la cabecera `Content-Type: application/json`

## Devolución de XML
Del mismo modo, el uso de la función de ayuda `xml($xml)` devolverá una respuesta `xml` con la cabecera `Content-Type: text/xml`.

El parámetro `$xml` puede ser una cadena `xml` o un objeto `SimpleXMLElement`.

## Devolución de JSONP
Del mismo modo, el uso de la función de ayuda `jsonp($data, $callback_name = 'callback')` devolverá una respuesta `jsonp`.

## Devolución de una vista
Modificar el archivo `app/controller/UserController.php` de la siguiente manera:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Crear el archivo `app/view/user/hello.html` con el siguiente contenido:

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

Acceder a `http://127.0.0.1:8787/user/hello?name=tom`

Se devolverá una página HTML con el contenido `hello tom`.

Nota: Por defecto, webman utiliza la sintaxis original de PHP como plantilla. Consultar [Vistas](view.md) para utilizar otras vistas.
