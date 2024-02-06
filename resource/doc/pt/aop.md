# AOP

> Thank the Hyperf author for the commit

### Instalação

- Instale a integração aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Adicione a configuração relacionada ao AOP

Precisamos adicionar a configuração do arquivo `config.php` no diretório `config`

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
        // Insira os Aspectos correspondentes aqui
        app\aspect\DebugAspect::class,
    ]
];

```

### Configurar o arquivo de entrada start.php

> Vamos colocar o método de inicialização abaixo do fuso horário, omitindo o restante do código a seguir

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Inicialização
ClassLoader::init();
```

### Teste

Primeiro, vamos escrever a classe a ser interceptada

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

Em seguida, adicione o correspondente `DebugAspect`

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

A seguir, edite o controlador `app/controller/IndexController.php`

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

Em seguida, configure a rota

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Por fim, inicie o serviço e teste.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
