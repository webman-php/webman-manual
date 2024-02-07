# Carga automática

## Cargar archivos bajo la especificación PSR-0 utilizando Composer
webman sigue la especificación de carga automática `PSR-4`. Si su proyecto necesita cargar bibliotecas de código que siguen la especificación `PSR-0`, siga los pasos a continuación.

- Cree el directorio `extend` para almacenar las bibliotecas de código que siguen la especificación `PSR-0`.
- Edite `composer.json` y agregue lo siguiente dentro de `autoload`:

```js
"psr-0" : {
    "": "extend/"
}
```
El resultado final se verá similar a
![](../../assets/img/psr0.png)

- Ejecute `composer dumpautoload`.
- Ejecute `php start.php restart` para reiniciar webman (nota: es necesario reiniciar para que surta efecto).

## Cargar ciertos archivos utilizando Composer
- Edite `composer.json` y agregue los archivos que desea cargar dentro de `autoload.files`:

```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```
- Ejecute `composer dumpautoload`.
- Ejecute `php start.php restart` para reiniciar webman (nota: es necesario reiniciar para que surta efecto).

> **Nota**
> Los archivos especificados en `autoload.files` en `composer.json` se cargarán antes de que webman se inicie. Por otro lado, los archivos cargados mediante la configuración en `config/autoload.php` del framework se cargarán después de que webman se inicie.
> Los cambios en los archivos cargados mediante `autoload.files` en `composer.json` solo surtirán efecto después de reiniciar; no funcionará con la recarga. Mientras que los archivos cargados mediante la configuración en `config/autoload.php` del framework admiten la recarga en caliente, por lo que los cambios surten efecto al recargar.

## Cargar ciertos archivos utilizando el framework
Algunos archivos pueden no seguir la especificación SPR y no se pueden cargar automáticamente. En estos casos, podemos cargar estos archivos mediante la configuración en `config/autoload.php`, por ejemplo:
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
> **Nota**
> Podemos ver en el archivo `autoload.php` que se establece la carga de dos archivos, `support/Request.php` y `support/Response.php`. Esto se debe a que en `vendor/workerman/webman-framework/src/support/` también hay dos archivos con el mismo nombre. Al utilizar `autoload.php`, priorizamos la carga de `support/Request.php` y `support/Response.php` en el directorio raíz del proyecto, lo que nos permite personalizar el contenido de estos dos archivos sin necesidad de modificar los archivos en `vendor`. Si no necesita personalizarlos, puede ignorar esta configuración.
