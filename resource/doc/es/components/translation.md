# Multi-idioma

El multi-idioma utiliza el componente [symfony/translation](https://github.com/symfony/translation).

## Instalación
```composer require symfony/translation```

## Crear Paquete de Idioma
Por defecto, webman coloca los paquetes de idioma en el directorio `resource/translations` (si no existe, créalo tú mismo). Si necesitas cambiar el directorio, configúralo en `config/translation.php`. 
Cada idioma tiene su propia subcarpeta y las definiciones de idioma suelen ir por defecto en `messages.php`. Por ejemplo:

``` 
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```
Todos los archivos de idioma devuelven un array, por ejemplo:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Configuración
`config/translation.php`

```php
return [
    // Idioma por defecto
    'locale' => 'zh_CN',
    // Idioma de reemplazo. Si no se puede encontrar una traducción en el idioma actual, se intentará usar la traducción del idioma de reemplazo.
    'fallback_locale' => ['zh_CN', 'en'],
    // Directorio donde se almacenan los archivos de idioma
    'path' => base_path() . '/resource/translations',
];
```

## Traducción
La traducción se realiza mediante el método `trans()`.

Crea el archivo de idioma `resource/translations/zh_CN/messages.php` de la siguiente manera:

```php
return [
    'hello' => '¡Hola mundo!',
];
```

Crea el archivo `app/controller/UserController.php`:
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

Al visitar `http://127.0.0.1:8787/user/get` regresará "¡Hola mundo!"

## Cambiar Idioma Predeterminado
Para cambiar el idioma, usa el método `locale()`.

Agrega el archivo de idioma `resource/translations/en/messages.php` de la siguiente manera:
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
        // Cambiar idioma
        locale('en');
        $hello = trans('hello'); // ¡Hola mundo!
        return response($hello);
    }
}
```

Al visitar `http://127.0.0.1:8787/user/get` regresará "¡Hola mundo!"

También puedes usar el cuarto parámetro de la función `trans()` para cambiar temporalmente el idioma. Por ejemplo, los dos ejemplos a continuación son equivalentes:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Cuarto parámetro para cambiar el idioma
        $hello = trans('hello', [], null, 'en'); // ¡Hola mundo!
        return response($hello);
    }
}
```

## Establecer un Idioma Específico para Cada Solicitud
La traducción es un singleton, lo que significa que todas las solicitudes comparten la misma instancia. Si una solicitud usa `locale()` para establecer el idioma por defecto, afectará a todas las solicitudes posteriores en el mismo proceso. Por lo tanto, se debe establecer un idioma específico para cada solicitud. Por ejemplo, utilizando el middleware siguiente:

Crea el archivo `app/middleware/Lang.php` (si el directorio no existe, créalo tú mismo) de la siguiente manera:

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

Agrega el middleware global en `config/middleware.php` de la siguiente manera:
```php
return [
    // Middleware global
    '' => [
        // ... Se omiten otros middlewares
        app\middleware\Lang::class,
    ]
];
```

## Uso de Marcadores de Posición
A veces, un mensaje contiene variables que deben ser traducidas, por ejemplo:
```php
trans('hello ' . $name);
```
En estos casos, se utilizan marcadores de posición.

Modifica `resource/translations/zh_CN/messages.php` de la siguiente manera:
```php
return [
    'hello' => '¡Hola %name%!',
];
```
Al traducir, se pasa el valor correspondiente del marcador de posición como segundo parámetro:
```php
trans('hello', ['%name%' => 'webman']); // ¡Hola webman!
```

## Tratamiento de Pluralidad
Algunos idiomas requieren diferentes formas de frases dependiendo de la cantidad de elementos, por ejemplo `Hay %count% manzanas`, donde la frase es correcta cuando `%count%` es 1, pero incorrecta cuando es mayor. 
En estos casos, se utilizan un **pipe** (`|`) para listar las formas de pluralidad.

Agrega la clave `apple_count` al archivo de idioma `resource/translations/en/messages.php` de la siguiente manera:
```php
return [
    // ...
    'apple_count' => 'Hay una manzana|Hay %count% manzanas',
];
```
```php
trans('apple_count', ['%count%' => 10]); // Hay 10 manzanas
```

Incluso se pueden especificar rangos numéricos para crear reglas de pluralidad más complejas:
```php
return [
    // ...
    'apple_count' => '{0} No hay manzanas|{1} Hay una manzana|]1,19] Hay %count% manzanas|[20,Inf[ Hay muchas manzanas'
];
```

```php
trans('apple_count', ['%count%' => 20]); // Hay muchas manzanas
```

## Especificar Archivos de Idioma
El archivo de idioma se llama por defecto `messages.php`, pero en realidad puedes crear archivos de idioma con otros nombres.

Crea el archivo de idioma `resource/translations/zh_CN/admin.php` de la siguiente manera:
```php
return [
    'hello_admin' => '¡Hola administrador!',
];
```

Especifica el archivo de idioma a través del tercer parámetro de `trans()` (omitir la extensión `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // ¡Hola administrador!
```

## Más Información
Consulta el [manual de symfony/translation](https://symfony.com/doc/current/translation.html) para más detalles.
