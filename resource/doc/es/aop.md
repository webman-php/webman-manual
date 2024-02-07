# AOP

> Agradecimientos a los colaboradores de Hyperf

### Instalación

- Instalar la integración aop

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Agregar configuración relacionada con AOP

Necesitamos agregar la configuración `config.php` en el directorio `config`

```php
<?php

use Hyperf\Di\Annotation\AspectCollector;

return [
    'annotations' => [
        'scan' => [
            'paths' => [
                BASE_PATH . '/app',
            ],
            'ignore_annotations' => [
                'mixin',
            ],
            'class_map' => [
            ],
            'collectors' => [
                AspectCollector::class
            ],
        ],
    ],
    'aspects' => [
        // Escribir aquí el Aspect correspondiente
        app\aspect\DebugAspect::class,
    ]
];

```

### Configurar el archivo de inicio start.php

> Vamos a colocar el método de inicialización debajo de la zona de la zona horaria, a continuación omitiremos el resto del código

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Inicializar
ClassLoader::init();
```

### Prueba

Primero vamos a escribir la clase a la que se le aplicará el corte

```php
<?php
namespace app\service;

class UserService
{
    public function first(): array
    {
        return ['id' => 1];
    }
}
```

Luego añadimos el `DebugAspect` correspondiente

```php
<?php
namespace app\aspect;

use app\service\UserService;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

class DebugAspect extends AbstractAspect
{
    public $classes = [
        UserService::class . '::first',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        var_dump(11);
        return $proceedingJoinPoint->process();
    }
}
```

A continuación, editamos el controlador `app/controller/IndexController.php`

```php
<?php
namespace app\controller;

use app\service\UserService;
use support\Request;

class IndexController
{
    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => (new UserService())->first()]);
    }
}
```

Luego configuramos las rutas

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Finalmente, iniciamos el servicio y realizamos la prueba.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
