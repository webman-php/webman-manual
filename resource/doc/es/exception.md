# Manejo de Excepciones

## Configuración
`config/exception.php`
```php
return [
    // Aquí se configura la clase de manejo de excepciones
    '' => support\exception\Handler::class,
];
```
En el modo de múltiples aplicaciones, se puede configurar una clase de manejo de excepciones para cada aplicación individualmente, consulte [Múltiples Aplicaciones](multiapp.md).

## Clase de Manejo de Excepciones Predeterminada
En webman, las excepciones se manejan de forma predeterminada por la clase `support\exception\Handler`. Se puede cambiar la clase de manejo de excepciones predeterminada modificando el archivo de configuración `config/exception.php`. La clase de manejo de excepciones debe implementar la interfaz `Webman\Exception\ExceptionHandlerInterface`.
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

## Renderizar Respuesta
El método `render` en la clase de manejo de excepciones se utiliza para renderizar respuestas.

Si el valor de `debug` en el archivo de configuración `config/app.php` es `true` (en adelante `app.debug=true`), se devolverá información detallada sobre la excepción, de lo contrario se devolverá información limitada sobre la excepción.

Si la solicitud espera una respuesta JSON, la información de la excepción se devolverá en formato JSON, por ejemplo:
```json
{
    "code": "500",
    "msg": "información de la excepción"
}
```
Si `app.debug=true`, los datos JSON incluirán un campo adicional `trace` que devuelve una pila de llamadas detallada.

Se puede escribir clases personalizadas de manejo de excepciones para cambiar la lógica de manejo de excepciones predeterminada.

# Excepción de Negocio BusinessException
A veces queremos detener una solicitud en una función anidada y devolver un mensaje de error al cliente. En este caso, se puede lanzar `BusinessException`.
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
En el ejemplo anterior se devolverá:
```json
{"code": 3000, "msg": "Parámetro incorrecto"}
```

> **Nota**
> La excepción de negocio `BusinessException` no necesita ser capturada por un try-catch, ya que el framework la captura automáticamente y devuelve una salida apropiada según el tipo de solicitud.

## Excepción de Negocio Personalizada
Si la respuesta anterior no cumple con sus requisitos, por ejemplo, si quiere cambiar `msg` a `message`, puede crear una excepción personalizada `MyBusinessException`.

Crear el archivo `app/exception/MyBusinessException.php` con el siguiente contenido:
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
        // Devolver datos en formato JSON si la solicitud es JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Devolver una página si la solicitud no es JSON
        return new Response(200, [], $this->getMessage());
    }
}
```
De esta manera, cuando se invoque la excepción de negocio:
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parámetro incorrecto', 3000);
```
Si es una solicitud JSON, se recibirá una respuesta JSON similar a la siguiente:
```json
{"code": 3000, "message": "Parámetro incorrecto"}
```

> **Consejo**
> Debido a que la excepción de negocio `BusinessException` es predecible (por ejemplo, error en los parámetros de entrada del usuario), el framework no la considera un error fatal y no la registra en los registros.

## Resumen
Se puede considerar el uso de `BusinessException` en cualquier momento en el que se desee interrumpir una solicitud actual y devolver información al cliente.
