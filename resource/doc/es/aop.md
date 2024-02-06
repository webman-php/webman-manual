# AOP

> Gracias a los autores de Hyperf por su contribución

### Instalación

- Instalar aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Agregar configuraciones relacionadas con AOP

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
        // Escribir el Aspecto correspondiente aquí
        app\aspect\DebugAspect::class,
    ]
];

```

### Archivo de entrada de configuración start.php

> Colocaremos el método de inicialización debajo de la zona horaria, omitiendo el resto del código a continuación

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Inicialización
ClassLoader::init();
```

### Prueba

Primero, escribamos la clase de corte esperada

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

Luego agreguemos el `DebugAspect` correspondiente

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

A continuación, editemos el controlador `app/controller/IndexController.php`

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

Luego configuremos la ruta

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Finalmente, iniciemos el servicio y hagamos la prueba.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
