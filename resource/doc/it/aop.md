# AOP

> Ringraziamo l'autore di Hyperf per il suo contributo

### Installazione

- Installa aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Aggiungere configurazioni correlate all'AOP

È necessario aggiungere una configurazione `config.php` nella directory `config`

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
        // Scrivi qui l'Aspetto corrispondente
        app\aspect\DebugAspect::class,
    ]
];

```

### Configura il file di avvio start.php

> Posizioniamo il metodo di inizializzazione sotto timezone, tralasciando il resto del codice

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Inizializzazione
ClassLoader::init();
```

### Test

Per prima cosa, scriviamo la classe con il codice da intercettare

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

In seguito, aggiungiamo l'`DebugAspect` corrispondente

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

Successivamente, modifichiamo il controller `app/controller/IndexController.php`

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

Infine, configuriamo il percorso

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Infine, avvia il servizio e fai un test.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
