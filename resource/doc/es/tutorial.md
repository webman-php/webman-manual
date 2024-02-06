# Ejemplo simple

## Devolver una cadena
**Crear un controlador**

Crear el archivo `app/controller/UserController.php` de la siguiente manera:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obtener el parámetro $name de la solicitud GET, si no se proporciona, devolverá $default_name
        $name = $request->get('name', $default_name);
        // Devolver una cadena al navegador
        return response('hello ' . $name);
    }
}
```

**Acceder**

Acceder a `http://127.0.0.1:8787/user/hello?name=tom` en el navegador

El navegador devolverá `hello tom`

## Devolver JSON
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

Al devolver datos usando la función auxiliar json, se añadirá automáticamente el encabezado `Content-Type: application/json`

## Devolver XML
Del mismo modo, al utilizar la función auxiliar `xml($xml)` se devolverá una respuesta XML con el encabezado `Content-Type: text/xml`.

El parámetro `$xml` puede ser una cadena XML o un objeto `SimpleXMLElement`.

## Devolver JSONP
Del mismo modo, al utilizar la función auxiliar `jsonp($data, $callback_name = 'callback')` se devolverá una respuesta JSONP.

## Devolver una vista
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

Crear el archivo `app/view/user/hello.html` de la siguiente manera:

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
Devolverá una página HTML con el contenido `hello tom`.

Nota: webman utiliza por defecto la sintaxis nativa de PHP como plantilla. Para utilizar otras vistas, consulte [Vistas](view.md).
