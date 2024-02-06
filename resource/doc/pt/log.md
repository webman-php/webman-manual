# Registos
O webman usa o [monolog/monolog](https://github.com/Seldaek/monolog) para processar registos.

## Utilização
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('teste de registo');
        return response('olá índice');
    }
}
```

## Métodos Fornecidos
```php
Log::log($nível, $mensagem, array $context = [])
Log::debug($mensagem, array $context = [])
Log::info($mensagem, array $context = [])
Log::notice($mensagem, array $context = [])
Log::warning($mensagem, array $context = [])
Log::error($mensagem, array $context = [])
Log::critical($mensagem, array $context = [])
Log::alert($mensagem, array $context = [])
Log::emergency($mensagem, array $context = [])
```
equivalente a
```php
$log = Log::channel('default');
$log->log($nível, $mensagem, array $context = [])
$log->debug($mensagem, array $context = [])
$log->info($mensagem, array $context = [])
$log->notice($mensagem, array $context = [])
$log->warning($mensagem, array $context = [])
$log->error($mensagem, array $context = [])
$log->critical($mensagem, array $context = [])
$log->alert($mensagem, array $context = [])
$log->emergency($mensagem, array $context = [])
```

## Configuração
```php
return [
    // Canal de registo padrão
    'default' => [
        // Manipuladores do canal padrão, pode configurar vários
        'handlers' => [
            [   
                // Nome da classe do manipulador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parâmetros de construtor da classe do manipulador
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatação relacionada
                'formatter' => [
                    // Nome da classe de formatação
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parâmetros de construtor da classe de formatação
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Múltiplos Canais
O monolog suporta múltiplos canais, sendo `default` o canal padrão. Se desejar adicionar um canal `log2`, a configuração será semelhante à seguinte:
```php
return [
    // Canal de registo padrão
    'default' => [
        // Manipuladores do canal padrão, pode configurar vários
        'handlers' => [
            [   
                // Nome da classe do manipulador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parâmetros de construtor da classe do manipulador
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatação relacionada
                'formatter' => [
                    // Nome da classe de formatação
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parâmetros de construtor da classe de formatação
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Canal log2
    'log2' => [
        // Manipuladores do canal padrão, pode configurar vários
        'handlers' => [
            [   
                // Nome da classe do manipulador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parâmetros de construtor da classe do manipulador
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatação relacionada
                'formatter' => [
                    // Nome da classe de formatação
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parâmetros de construtor da classe de formatação
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Para utilizar o canal `log2`, o uso seria como segue:
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        $log = Log::channel('log2');
        $log->info('teste log2');
        return response('olá índice');
    }
}
```
