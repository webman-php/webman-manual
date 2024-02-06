# Multi-idioma

El multi-idioma utiliza el componente [symfony/translation](https://github.com/symfony/translation).

## Instalación
```
composer require symfony/translation
```

## Creación del paquete de idiomas
Por defecto, webman coloca el paquete de idiomas en el directorio `resource/translations` (si no existe, créelo), si necesita cambiar el directorio, configúrelo en `config/translation.php`.
Cada idioma corresponde a una subcarpeta, y la definición del idioma por defecto se coloca en `messages.php`. Por ejemplo:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Todos los archivos de idioma devuelven un array, por ejemplo:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hola webman',
];
```

## Configuración

`config/translation.php`

```php
return [
    // Idioma por defecto
    'locale' => 'zh_CN',
    // Idioma de respaldo, se intenta usar la traducción en el idioma de respaldo si no se encuentra en el idioma actual
    'fallback_locale' => ['zh_CN', 'en'],
    // Directorio donde se almacenan los archivos de idioma
    'path' => base_path() . '/resource/translations',
];
```

## Traducción

Utilice el método `trans()` para la traducción.

Cree un archivo de idioma `resource/translations/zh_CN/messages.php` como sigue:
```php
return [
    'hello' => '¡Hola mundo!',
];
```

Cree el archivo `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // ¡Hola mundo!
        return response($hello);
    }
}
```

Al acceder a `http://127.0.0.1:8787/user/get` se devolverá "¡Hola mundo!"

## Cambiar el idioma por defecto

Utilice el método `locale()` para cambiar el idioma.

Agregue un archivo de idioma `resource/translations/en/messages.php` como sigue:
```php
return [
    'hello' => '¡Hola mundo!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Cambiar el idioma
        locale('en');
        $hello = trans('hello'); // ¡Hola mundo!
        return response($hello);
    }
}
```
Al acceder a `http://127.0.0.1:8787/user/get` se devolverá "¡Hola mundo!"

También puede usar el cuarto parámetro de la función `trans()` para cambiar el idioma temporalmente, por ejemplo, el siguiente ejemplo es equivalente al anterior:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Cambiar el idioma mediante el cuarto parámetro
        $hello = trans('hello', [], null, 'en'); // ¡Hola mundo!
        return response($hello);
    }
}
```

## Establecer el idioma explícitamente para cada solicitud
La traducción es un singleton, lo que significa que todas las solicitudes comparten esta instancia, por lo que si una solicitud utiliza `locale()` para establecer el idioma por defecto, afectará a todas las solicitudes subsiguientes en ese proceso. Por lo tanto, deberíamos establecer explícitamente el idioma para cada solicitud. Por ejemplo, utilizando el siguiente middleware.

Cree el archivo `app/middleware/Lang.php` (si el directorio no existe, créelo) como sigue:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

Agregue el middleware global en `config/middleware.php` como sigue:
```php
return [
    // Middleware global
    '' => [
        // ... otros middleware aquí
        app\middleware\Lang::class,
    ]
];
```


## Uso de marcadores de posición
A veces, un mensaje contiene variables que necesitan ser traducidas, por ejemplo
```php
trans('hello ' . $name);
```
Cuando se encuentre con esta situación, utilizaremos marcadores de posición para manejarla.

Modifique `resource/translations/zh_CN/messages.php` como sigue:
```php
return [
    'hello' => '¡Hola %name%!',
```
Al traducir, pase los valores correspondientes a los marcadores de posición a través del segundo parámetro.
```php
trans('hello', ['%name%' => 'webman']); // ¡Hola webman!
```

## Manejo de plurales
Algunos idiomas tienen diferentes estructuras gramaticales según la cantidad de elementos, por ejemplo, `Hay %count% manzana`, cuando `%count%` es 1, la estructura es correcta, pero cuando es mayor que 1, es incorrecta.

Cuando se enfrenta a esta situación, utilizamos **tubos** (`|`) para enumerar las formas gramaticales plurales.

Agregue `apple_count` al archivo de idioma `resource/translations/en/messages.php` como sigue:
```php
return [
    // ...
    'apple_count' => 'Hay una manzana|Hay %count% manzanas',
];
```

```php
trans('apple_count', ['%count%' => 10]); // Hay 10 manzanas
```

Incluso se puede especificar un rango numérico para crear reglas plurales más complejas:
```php
return [
    // ...
    'apple_count' => '{0} No hay manzanas|{1} Hay una manzana|]1,19] Hay %count% manzanas|[20,Inf[ Hay muchas manzanas'
];
```

```php
trans('apple_count', ['%count%' => 20]); // Hay muchas manzanas
```

## Especificar archivo de idioma

Por defecto, el nombre del archivo de idioma es `messages.php`, pero en realidad puede crear archivos de idioma con otros nombres.

Cree el archivo de idioma `resource/translations/zh_CN/admin.php` como sigue:
```php
return [
    'hello_admin' => '¡Hola administrador!',
];
```

Especifique el archivo de idioma (sin la extensión `.php`) a través del tercer parámetro de `trans()`.
```php
trans('hello', [], 'admin', 'zh_CN'); // ¡Hola administrador!
```

## Más información
Consulte el [manual de symfony/translation](https://symfony.com/doc/current/translation.html)
