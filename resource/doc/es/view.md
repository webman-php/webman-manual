## Vistas
Por defecto, webman utiliza la sintaxis nativa de PHP como plantilla, lo cual proporciona el mejor rendimiento cuando se activa `opcache`. Además de las plantillas nativas de PHP, webman también ofrece motores de plantillas como [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content).

## Activar opcache
Se recomienda encarecidamente activar las opciones `opcache.enable` y `opcache.enable_cli` en el archivo php.ini al usar vistas, para que el motor de plantillas alcance el mejor rendimiento.

## Instalar Twig
1. Instalación mediante composer

   `composer require twig/twig`

2. Modificar la configuración en `config/view.php` de la siguiente manera
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **Nota**
   > Otras opciones de configuración se pueden pasar mediante options, por ejemplo:

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
1. Instalación mediante composer

   `composer require psr/container ^1.1.1 webman/blade`

2. Modificar la configuración en `config/view.php` de la siguiente manera
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## Instalar think-template
1. Instalación mediante composer

   `composer require topthink/think-template`

2. Modificar la configuración en `config/view.php` de la siguiente manera
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **Nota**
   > Otras opciones de configuración se pueden pasar mediante options, por ejemplo

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

## Ejemplo de motor de plantillas de PHP nativo
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
Modificar la configuración en `config/view.php` de la siguiente manera
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
Para más información, consulta [Twig](https://twig.symfony.com/doc/3.x/).

## Ejemplo de motor de plantillas Blade
Modificar la configuración en `config/view.php` de la siguiente manera
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
> Nota: el nombre de la plantilla blade tiene la extensión `.blade.php`

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
Para más información, consulta [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Ejemplo de motor de plantillas think-template
Modificar la configuración en `config/view.php` de la siguiente manera
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
Para más información, consulta [think-template](https://www.kancloud.cn/manual/think-template/content).

## Asignación de plantillas
Además de utilizar `view(template, array)` para asignar valores a la plantilla, también podemos asignar valores a la plantilla en cualquier lugar llamando a `View::assign()`. Por ejemplo:
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
`View::assign()` es muy útil en ciertos escenarios, por ejemplo, si cada página de un sistema necesita mostrar la información del usuario que ha iniciado sesión, asignar esta información a la plantilla mediante `View::assign()` sería muy útil. La solución sería obtener la información del usuario en el middleware y luego asignar la información del usuario a la plantilla mediante `View::assign()`.

## Acerca de la ruta de archivos de vista
#### Controladores
Cuando un controlador llama a `view('nombre_plantilla', [])`, el archivo de vista se busca de acuerdo con las siguientes reglas:

1. Si no hay múltiples aplicaciones, se utilizará el archivo de vista correspondiente en `app/view/`.
2. En el caso de [múltiples aplicaciones](multiapp.md), se utilizará el archivo de vista correspondiente en `app/nombre_aplicacion/view/`.

En resumen, si `$request->app` está vacío, se utilizarán los archivos de vista en `app/view/`, de lo contrario, se utilizarán los archivos de vista en `app/{$request->app}/view/`.

#### Funciones de cierre
La variable `$request->app` estará vacía para las funciones de cierre, ya que no pertenecen a ninguna aplicación en particular. Por lo tanto, las funciones de cierre utilizarán los archivos de vista en `app/view/`, por ejemplo, al definir rutas en el archivo `config/route.php` de la siguiente manera
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
Se utilizará el archivo de vista `app/view/user.html` como plantilla (si se está utilizando blade, el archivo de plantilla sería `app/view/user.blade.php`).

#### Especificar aplicación
Para reutilizar plantillas en el modo de múltiples aplicaciones, el método `view($template, $data, $app = null)` proporciona un tercer parámetro `$app` que se puede utilizar para especificar qué directorio de aplicación se debe utilizar para las plantillas. Por ejemplo, `view('user', [], 'admin')` forzará el uso de archivos de vista en `app/admin/view/`.

## Ampliar Twig
> **Nota**
> Esta función requiere webman-framework>=1.4.8

Podemos extender la instancia de vista Twig mediante la devolución de la configuración `view.extension`, por ejemplo, en `config/view.php` como sigue
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

## Ampliar Blade
> **Nota**
> Esta función requiere webman-framework>=1.4.8

Del mismo modo, podemos extender la instancia de vista Blade mediante la devolución de la configuración `view.extension`, por ejemplo, en `config/view.php` como sigue
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Agregar directiva a blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```
## Uso del componente blade

> **Nota
> Se requiere webman/blade>=1.5.2**

Supongamos que necesitamos agregar un componente Alert.

**Cree `app/view/components/Alert.php`**
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

**Cree `app/view/components/alert.blade.php`**
```php
<div>
    <b style="color: red">hola componente de blade</b>
</div>
```

**`/config/view.php` debería ser similar al siguiente código**
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

Con esto, se ha configurado el componente Alert de Blade. Al utilizarlo en la plantilla, se vería algo como:
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

## Extensión para think-template
think-template utiliza `view.options.taglib_pre_load` para extender las librerías de etiquetas, por ejemplo:
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

Para más detalles, consulta [Extensión de etiquetas de think-template](https://www.kancloud.cn/manual/think-template/1286424)
