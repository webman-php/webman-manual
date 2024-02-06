# Manejo de excepciones

## Configuración
`config/exception.php`
```php
return [
    // Aquí se configura la clase de manejo de excepciones
    '' => support\exception\Handler::class,
];
```
En el modo de aplicaciones múltiples, puedes configurar una clase de manejo de excepciones independiente para cada aplicación, consulta [Aplicaciones Múltiples](multiapp.md)


## Clase de manejo de excepciones predeterminada
En webman, las excepciones por defecto son manejadas por la clase `support\exception\Handler`. Puedes modificar el archivo de configuración `config/exception.php` para cambiar la clase de manejo de excepciones predeterminada. La clase de manejo de excepciones debe implementar la interfaz `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Registrar el registro
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Renderizar respuesta
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Renderización de respuesta
El método `render` dentro de la clase de manejo de excepciones se utiliza para renderizar la respuesta.

Si el valor de `debug` en el archivo de configuración `config/app.php` es `true` (llamado `app.debug=true` en adelante), se devolverá información detallada de la excepción, de lo contrario se devolverá información concisa de la excepción.

Si la solicitud espera una respuesta en formato json, la información de la excepción se devolverá en formato json, similar a:
```json
{
    "code": "500",
    "msg": "mensaje de la excepción"
}
```
Si `app.debug=true`, los datos json mostrarán un campo adicional `trace` con detalles del stack de llamadas.

Puedes escribir tu propia clase de manejo de excepciones para cambiar la lógica de manejo de excepciones predeterminada.

# Excepción de negocio BusinessException
A veces queremos detener una solicitud dentro de una función anidada y devolver un mensaje de error al cliente, para esto podemos lanzar un `BusinessException`.
Por ejemplo:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Parámetro incorrecto', 3000);
        }
    }
}
```

El ejemplo anterior devolverá:
```json
{"code": 3000, "msg": "Parámetro incorrecto"}
```

> **Nota**
> La excepción de negocio BusinessException no necesita ser capturada por el bloque try-catch del negocio, el framework la capturará automáticamente y devolverá la salida adecuada según el tipo de solicitud.

## Excepción de negocio personalizada

Si la respuesta anterior no cumple con tus necesidades, por ejemplo, si quieres cambiar `msg` a `message`, puedes personalizar una `MyBusinessException`.

Crea el archivo `app/exception/MyBusinessException.php` con el siguiente contenido
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // Devolver datos json para solicitudes en formato json
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Devolver una página para solicitudes no json
        return new Response(200, [], $this->getMessage());
    }
}
```

De esta manera, cuando el negocio llame a
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parámetro incorrecto', 3000);
```
Una solicitud de formato json recibirá una respuesta similar a la siguiente:
```json
{"code": 3000, "message": "Parámetro incorrecto"}
```

> **Consejo**
> Debido a que la excepción BusinessException pertenece a las excepciones comerciales (por ejemplo, errores en los parámetros de entrada del usuario), es predecible, por lo que el framework no la considera como un error fatal y no registrará sucesos.

## Resumen
Cuando desees interrumpir una solicitud actual y devolver información al cliente, considera utilizar la excepción `BusinessException`.
