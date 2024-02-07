# Registro
webman utiliza [monolog/monolog](https://github.com/Seldaek/monolog) para manejar los registros.

## Uso
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('prueba de registro');
        return response('hola index');
    }
}
```

## Métodos disponibles
```php
Log::log($level, $message, array $context = [])
Log::debug($message, array $context = [])
Log::info($message, array $context = [])
Log::notice($message, array $context = [])
Log::warning($message, array $context = [])
Log::error($message, array $context = [])
Log::critical($message, array $context = [])
Log::alert($message, array $context = [])
Log::emergency($message, array $context = [])
```
Equivalente a
```php
$log = Log::channel('default');
$log->log($level, $message, array $context = [])
$log->debug($message, array $context = [])
$log->info($message, array $context = [])
$log->notice($message, array $context = [])
$log->warning($message, array $context = [])
$log->error($message, array $context = [])
$log->critical($message, array $context = [])
$log->alert($message, array $context = [])
$log->emergency($message, array $context = [])
```

## Configuración
```php
return [
    // Canal de registro predeterminado
    'default' => [
        // Manejadores del canal predeterminado, se pueden configurar varios
        'handlers' => [
            [   
                // Nombre de la clase del manejador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parámetros del constructor de la clase del manejador
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formato relacionado
                'formatter' => [
                    // Nombre de la clase del formateador
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parámetros del constructor de la clase del formateador
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Múltiples canales
monolog soporta múltiples canales, y por defecto utiliza el canal `default`. Si deseas agregar un canal `log2`, la configuración sería similar a la siguiente:
```php
return [
    // Canal de registro predeterminado
    'default' => [
        // Manejadores del canal predeterminado, se pueden configurar varios
        'handlers' => [
            [   
                // Nombre de la clase del manejador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parámetros del constructor de la clase del manejador
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formato relacionado
                'formatter' => [
                    // Nombre de la clase del formateador
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parámetros del constructor de la clase del formateador
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Canal log2
    'log2' => [
        // Manejadores del canal log2, se pueden configurar varios
        'handlers' => [
            [   
                // Nombre de la clase del manejador
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parámetros del constructor de la clase del manejador
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formato relacionado
                'formatter' => [
                    // Nombre de la clase del formateador
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parámetros del constructor de la clase del formateador
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

El uso del canal `log2` sería el siguiente:
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
        $log->info('prueba de log2');
        return response('hola index');
    }
}
```
