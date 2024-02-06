## Vistas
Por defecto, webman utiliza la sintaxis nativa de PHP como plantilla, que, con el uso de `opcache`, obtiene el mejor rendimiento. Además de la plantilla nativa de PHP, webman también proporciona motores de plantillas como [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) y [think-template](https://www.kancloud.cn/manual/think-template/content).

## Habilitar opcache
Al utilizar vistas, se recomienda encarecidamente habilitar las opciones `opcache.enable` y `opcache.enable_cli` en el archivo php.ini para que el motor de plantillas alcance el mejor rendimiento.

## Instalar Twig
1. Instalación con composer

```bash
composer require twig/twig
```

2. Modificar la configuración en `config/view.php` como sigue

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

> **Nota**
> Otras opciones de configuración se pasan a través de `options`, por ejemplo

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## Instalar Blade
1. Instalación con composer

```bash
composer require psr/container ^1.1.1 webman/blade
```

2. Modificar la configuración en `config/view.php` como sigue

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Instalar think-template
1. Instalación con composer

```bash
composer require topthink/think-template
```

2. Modificar la configuración en `config/view.php` como sigue

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```

> **Nota**
> Otras opciones de configuración se pasan a través de `options`, por ejemplo

```php
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'view_suffix' => 'html',
        'tpl_begin' => '{',
        'tpl_end' => '}'
    ]
];
```

## Ejemplo de motor de plantillas PHP nativo
Crear el archivo `app/controller/UserController.php` de la siguiente manera

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Crear el archivo `app/view/user/hello.html` de la siguiente manera

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

## Ejemplo de motor de plantillas Twig
Modificar la configuración en `config/view.php` como sigue

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` de la siguiente manera

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

El archivo `app/view/user/hello.html` de la siguiente manera

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

Más documentación en [Twig](https://twig.symfony.com/doc/3.x/).

## Ejemplo de motor de plantillas Blade
Modificar la configuración en `config/view.php` como sigue

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` de la siguiente manera

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

El archivo `app/view/user/hello.blade.php` de la siguiente manera

> Nota: el nombre del archivo de la plantilla blade tiene la extensión `.blade.php`

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

Más documentación en [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Ejemplo de motor de plantillas ThinkPHP
Modificar la configuración en `config/view.php` como sigue

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` de la siguiente manera

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

El archivo `app/view/user/hello.html` de la siguiente manera

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

Más documentación en [think-template](https://www.kancloud.cn/manual/think-template/content).

## Asignación de plantillas
Además de usar `view(template, data)` para asignar valores a las plantillas, también podemos usar `View::assign()` en cualquier lugar para asignar valores a la plantilla. Por ejemplo:

```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` es muy útil en algunos escenarios, como la visualización de la información del usuario en la parte superior de cada página en un sistema. Si se asignara esta información a cada página a través de `view('template', ['user_info' => 'user info'])`, sería muy problemático. La solución sería obtener la información del usuario a través de un middleware y luego asignarla a la plantilla mediante `View::assign()`.

## Acerca de la ruta de los archivos de vista

#### Controladores
Cuando un controlador llama a `view('nombre_de_la_plantilla',[])`, la búsqueda de la plantilla sigue estas reglas:

1. Si no se está utilizando la aplicación múltiple, se utiliza el archivo de vista correspondiente en `app/view/`.
2. En caso de [aplicaciones múltiples](multiapp.md), se utiliza el archivo de vista en `app/nombre_de_la_aplicacion/view/`.

En resumen, si `$request->app` está vacío, se utiliza el archivo de vista en `app/view/`; de lo contrario, se utiliza el archivo de vista en `app/{$request->app}/view/`.

#### Funciones anónimas
Dado que `$request->app` está vacío y no pertenece a ninguna aplicación, las funciones anónimas utilizan el archivo de vista en `app/view/`. Por ejemplo, al definir rutas en `config/route.php` de la siguiente manera:

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```

El archivo de vista `app/view/user.html` se utilizará como archivo de plantilla (cuando se utiliza la plantilla blade, el archivo de plantilla es `app/view/user.blade.php`).

#### Especificar la aplicación
Para que las plantillas se puedan reutilizar en el modo de aplicación múltiple, `view($template, $data, $app = null)` proporciona un tercer parámetro `$app`, que se puede utilizar para especificar qué aplicación utilizar para las plantillas. Por ejemplo, `view('user', [], 'admin')` obligaría a utilizar el archivo de vista en `app/admin/view/`.

## Ampliación de Twig
> **Nota**
> Esta característica requiere webman-framework>=1.4.8

Podemos ampliar la instancia de la vista Twig mediante el uso del callback `view.extension` en la configuración. Por ejemplo, en `config/view.php`:

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Agregar extensión
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Agregar filtro
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Agregar función
    }
];
```

## Ampliación de Blade
> **Nota**
> Esta característica requiere webman-framework>=1.4.8

De manera similar, podemos ampliar la instancia de la vista Blade utilizando el callback `view.extension` en la configuración. Por ejemplo, en `config/view.php`:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Agregar directiva a Blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Uso de componentes de Blade

> **Nota
> Requiere webman/blade>=1.5.2**

Supongamos que se necesita agregar un componente Alert.

**Crear `app/view/components/Alert.php`**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**Crear `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` similar al siguiente código**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

Con esto, el componente Alert de Blade está configurado y se puede utilizar en la plantilla de la siguiente manera:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```


## Ampliación de think-template
think-template utiliza `view.options.taglib_pre_load` para ampliar la biblioteca de etiquetas. Por ejemplo:

```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

Consulte [Extensión de tags de think-template](https://www.kancloud.cn/manual/think-template/1286424) para obtener más detalles.
