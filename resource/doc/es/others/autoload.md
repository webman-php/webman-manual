# Carga automática

## Cargar archivos que siguen la especificación PSR-0 utilizando Composer
webman sigue la especificación de carga automática `PSR-4`. Si su aplicación necesita cargar bibliotecas de código que siguen la especificación `PSR-0`, siga los pasos a continuación.

- Cree un directorio `extend` para almacenar las bibliotecas de código que siguen la especificación `PSR-0`.
- Edite `composer.json` y agregue el siguiente contenido debajo de `autoload`
```js
"psr-0" : {
    "": "extend/"
}
```
El resultado final será similar a
![](../../assets/img/psr0.png)

- Ejecute `composer dumpautoload`
- Ejecute `php start.php restart` para reiniciar webman (nota: debe reiniciarse para que surta efecto)

## Cargar archivos utilizando Composer

- Edite `composer.json` y agregue los archivos que desee cargar debajo de `autoload.files`
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Ejecute `composer dumpautoload`
- Ejecute `php start.php restart` para reiniciar webman (nota: debe reiniciarse para que surta efecto)

> **Nota**
> Los archivos configurados en `autoload.files` en composer.json se cargarán antes de que se inicie webman. Los archivos cargados utilizando `config/autoload.php` del marco se cargarán después de que webman se haya iniciado.
> Los cambios realizados en los archivos cargados con `autoload.files` en composer.json solo surtirán efecto después de reiniciar, no con una recarga. Por otro lado, los archivos cargados utilizando `config/autoload.php` del marco admiten recarga en caliente, por lo que los cambios surtirán efecto con una recarga.

## Cargar archivos utilizando el marco

Algunos archivos pueden no cumplir con la especificación de SPR y no se pueden cargar automáticamente. En este caso, podemos cargar estos archivos a través de la configuración `config/autoload.php`, por ejemplo:
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
> Observamos que `autoload.php` está configurado para cargar los archivos `support/Request.php` y `support/Response.php`. Esto se debe a que también hay dos archivos idénticos bajo `vendor/workerman/webman-framework/src/support/`. Con `autoload.php`, cargamos primero `support/Request.php` y `support/Response.php` en el directorio raíz del proyecto, lo que nos permite personalizar el contenido de estos dos archivos sin necesidad de modificar los archivos en `vendor`. Si no necesita personalizarlos, puede ignorar esta configuración.
