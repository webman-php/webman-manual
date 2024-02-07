# AOP

> Grazie per il contributo dell'autore di Hyperf

### Installazione

- Installa l'integrazione aop

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Aggiungere la configurazione relativa a AOP

Dobbiamo aggiungere la configurazione `config.php` nella directory `config`

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
        // Qui inserire l'Aspect corrispondente
        app\aspect\DebugAspect::class,
    ]
];

```

### Configura il file di avvio start.php

> Posizioniamo il metodo di inizializzazione sotto timezone, di seguito omettiamo gli altri codici

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Inizializzazione
ClassLoader::init();
```

### Test

Innanzitutto creiamo la classe da intercettare

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

Successivamente aggiungiamo l'`DebugAspect` corrispondente

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

Poi modifichiamo il controller `app/controller/IndexController.php`

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

Successivamente configuriamo le route

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Infine avviamo il servizio e testiamolo.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
