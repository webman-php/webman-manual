# Registo

O webman utiliza o [monolog/monolog](https://github.com/Seldaek/monolog) para gerir registos.

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

## Métodos disponibilizados
```php
Log::log($nível, $mensagem, array $contexto = [])
Log::debug($mensagem, array $contexto = [])
Log::info($mensagem, array $contexto = [])
Log::notice($mensagem, array $contexto = [])
Log::warning($mensagem, array $contexto = [])
Log::error($mensagem, array $contexto = [])
Log::critical($mensagem, array $contexto = [])
Log::alert($mensagem, array $contexto = [])
Log::emergency($mensagem, array $contexto = [])
```
Equivalente a
```php
$log = Log::channel('default');
$log->log($nível, $mensagem, array $contexto = [])
$log->debug($mensagem, array $contexto = [])
$log->info($mensagem, array $contexto = [])
$log->notice($mensagem, array $contexto = [])
$log->warning($mensagem, array $contexto = [])
$log->error($mensagem, array $contexto = [])
$log->critical($mensagem, array $contexto = [])
$log->alert($mensagem, array $contexto = [])
$log->emergency($mensagem, array $contexto = [])
```

## Configuração
```php
return [
    // Canal de registo predefinido
    'default' => [
        // Manipuladores do canal predefinido, pode configurar vários
        'handlers' => [
            [   
                // Nome da classe do manipulador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parâmetros do construtor da classe do manipulador
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatação relacionada
                'formatter' => [
                    // Nome da classe do processador de formatação
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parâmetros do construtor da classe do processador de formatação
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Múltiplos Canais
O monolog suporta múltiplos canais, sendo `default` o canal predefinido. Se desejar adicionar um canal `log2`, a configuração será semelhante à seguinte:
```php
return [
    // Canal de registo predefinido
    'default' => [
        // Manipuladores do canal predefinido, pode configurar vários
        'handlers' => [
            [   
                // Nome da classe do manipulador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parâmetros do construtor da classe do manipulador
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatação relacionada
                'formatter' => [
                    // Nome da classe do processador de formatação
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parâmetros do construtor da classe do processador de formatação
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Canal log2
    'log2' => [
        // Manipuladores do canal log2, pode configurar vários
        'handlers' => [
            [   
                // Nome da classe do manipulador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parâmetros do construtor da classe do manipulador
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatação relacionada
                'formatter' => [
                    // Nome da classe do processador de formatação
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parâmetros do construtor da classe do processador de formatação
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Para utilizar o canal `log2`, a utilização é a seguinte:
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
