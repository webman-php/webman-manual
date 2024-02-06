# Aplicaciones Múltiples
A veces, un proyecto puede dividirse en varios subproyectos, por ejemplo, una tienda en línea puede dividirse en el proyecto principal de la tienda, la interfaz de programación de aplicaciones (API) de la tienda y el panel de administración de la tienda, los cuales comparten la misma configuración de base de datos.

webman te permite organizar el directorio de la aplicación de la siguiente manera:
```plaintext
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
Cuando se accede a la dirección `http://127.0.0.1:8787/shop/{controlador}/{método}`, se accede al controlador y al método en `app/shop/controller`.

Cuando se accede a la dirección `http://127.0.0.1:8787/api/{controlador}/{método}`, se accede al controlador y al método en `app/api/controller`.

Cuando se accede a la dirección `http://127.0.0.1:8787/admin/{controlador}/{método}`, se accede al controlador y al método en `app/admin/controller`.

En webman, incluso puedes organizar el directorio de la aplicación de la siguiente manera:
```plaintext
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
De esta manera, cuando se accede a la dirección `http://127.0.0.1:8787/{controlador}/{método}`, se accede al controlador y al método en `app/controller`. Cuando la ruta comienza con api o admin, se accede al controlador y al método en el directorio correspondiente.

Para las aplicaciones múltiples, el espacio de nombres de las clases debe seguir el estándar `psr4`, por ejemplo, el archivo `app/api/controller/FooController.php` se vería similar a esto:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}
```

## Configuración de middleware para aplicaciones múltiples
A veces, es posible que desees configurar middleware diferente para diferentes aplicaciones, por ejemplo, la aplicación `api` puede necesitar un middleware de control de acceso, mientras que `admin` necesita un middleware para verificar el inicio de sesión del administrador. La configuración para `config/midlleware.php` podría ser algo como esto:
```php
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
> Es posible que los middleware anteriores no existan, esto es solo un ejemplo de cómo configurar middleware según la aplicación.

El orden de ejecución del middleware es `middleware global` -> `middleware de la aplicación`.

Para obtener más información sobre el desarrollo de middleware, consulta el capítulo [Middleware](middleware.md).

## Configuración de manejo de excepciones para aplicaciones múltiples
Del mismo modo, es posible que desees configurar diferentes clases de manejo de excepciones para diferentes aplicaciones, por ejemplo, cuando surge una excepción en la aplicación `shop`, es posible que desees mostrar una página de error amigable, mientras que en la aplicación `api`, es posible que desees devolver una cadena JSON en lugar de una página. La configuración de clases de manejo de excepciones diferentes para diferentes aplicaciones en el archivo de configuración `config/exception.php` podría ser así:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> A diferencia de los middleware, cada aplicación puede configurar una sola clase de manejo de excepciones.

> Es posible que las clases de manejo de excepciones anteriores no existan, esto es solo un ejemplo de cómo configurar el manejo de excepciones según la aplicación.

Para obtener más información sobre el desarrollo de manejo de excepciones, consulta el capítulo [Manejo de excepciones](exception.md).
