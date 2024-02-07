# Aplicaciones múltiples
A veces, un proyecto puede dividirse en varios subproyectos, por ejemplo, una tienda en línea puede dividirse en la tienda principal, la interfaz de programación de aplicaciones (API) de la tienda y el panel de administración de la tienda, y todos ellos utilizan la misma configuración de base de datos.

webman te permite planificar el directorio de la aplicación de la siguiente manera:
``` 
app 
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
``` 
Cuando se accede a la dirección `http://127.0.0.1:8787/shop/{controller}/{method}` se accede al controlador y al método en el directorio `app/shop/controller`.

Cuando se accede a la dirección `http://127.0.0.1:8787/api/{controller}/{method}` se accede al controlador y al método en el directorio `app/api/controller`.

Cuando se accede a la dirección `http://127.0.0.1:8787/admin/{controller}/{method}` se accede al controlador y al método en el directorio `app/admin/controller`.

En webman, incluso puedes planificar el directorio de la aplicación de la siguiente manera.
``` 
app 
├── controller
├── model
├── view
│ 
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
``` 
De esta manera, cuando se accede a la dirección `http://127.0.0.1:8787/{controller}/{method}`, se accede al controlador y al método en el directorio `app/controller`. Cuando la ruta comienza con api o admin, se accede al controlador y al método en el directorio correspondiente.

Cuando se utilizan múltiples aplicaciones, los espacios de nombres de las clases deben cumplir con `psr4`, por ejemplo, el archivo `app/api/controller/FooController.php` se vería similar al siguiente:
``` php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}
```

## Configuración de middleware para aplicaciones múltiples
A veces puede que desees configurar middleware diferentes para diferentes aplicaciones, por ejemplo, la aplicación `api` puede necesitar un middleware de control de acceso, mientras que `admin` puede necesitar un middleware para verificar el inicio de sesión del administrador. La configuración de `config/midlleware.php` puede ser similar a lo siguiente:
``` php
return [
    // Middleware global
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware de la aplicación api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware de la aplicación admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Es posible que los middleware mencionados anteriormente no existan, aquí se presentan únicamente como ejemplos de cómo configurar middleware según la aplicación.

El orden de ejecución del middleware es `middleware global` -> `middleware de la aplicación`.

Para desarrollar middleware, consulta el capítulo de [middleware](middleware.md).

## Configuración de manejo de excepciones para aplicaciones múltiples
Del mismo modo, es posible que desees configurar clases de manejo de excepciones diferentes para diferentes aplicaciones. Por ejemplo, cuando ocurre una excepción en la aplicación `shop`, es posible que desees mostrar una página amigable, mientras que en la aplicación `api`, es posible que desees devolver una cadena JSON en lugar de una página. La configuración del archivo `config/exception.php` para configurar clases de manejo de excepciones para diferentes aplicaciones puede ser similar a lo siguiente:
``` php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> A diferencia de los middleware, cada aplicación solo puede configurar una clase de manejo de excepciones.

> Es posible que las clases de manejo de excepciones mencionadas anteriormente no existan, aquí se presentan únicamente como ejemplos de cómo configurar el manejo de excepciones según la aplicación.

Para desarrollar el manejo de excepciones, consulta el capítulo de [manejo de excepciones](exception.md).
