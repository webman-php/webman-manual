# Log

webman utilizza [monolog/monolog](https://github.com/Seldaek/monolog) per gestire i log.

## Utilizzo
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('test di log');
        return response('ciao index');
    }
}
```

## Metodi forniti
```php
Log::log($livello, $messaggio, array $contesto = [])
Log::debug($messaggio, array $contesto = [])
Log::info($messaggio, array $contesto = [])
Log::notice($messaggio, array $contesto = [])
Log::warning($messaggio, array $contesto = [])
Log::error($messaggio, array $contesto = [])
Log::critical($messaggio, array $contesto = [])
Log::alert($messaggio, array $contesto = [])
Log::emergency($messaggio, array $contesto = [])
```
Equivalente a
```php
$log = Log::channel('default');
$log->log($livello, $messaggio, array $contesto = [])
$log->debug($messaggio, array $contesto = [])
$log->info($messaggio, array $contesto = [])
$log->notice($messaggio, array $contesto = [])
$log->warning($messaggio, array $contesto = [])
$log->error($messaggio, array $contesto = [])
$log->critical($messaggio, array $contesto = [])
$log->alert($messaggio, array $contesto = [])
$log->emergency($messaggio, array $contesto = [])
```

## Configurazione
```php
return [
    // Canale di log predefinito
    'default' => [
        // Gestori del canale predefinito, è possibile impostarne più di uno
        'handlers' => [
            [   
                // Nome della classe del gestore
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parametri del costruttore della classe del gestore
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formattazione correlata
                'formatter' => [
                    // Nome della classe del formattatore
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parametri del costruttore della classe del formattatore
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Canali multipli
Monolog supporta canali multipli, utilizza il canale `default` per impostazione predefinita. Se si vuole aggiungere un canale `log2`, la configurazione è simile a quanto segue:
```php
return [
    // Canale di log predefinito
    'default' => [
        // Gestori del canale predefinito, è possibile impostarne più di uno
        'handlers' => [
            [   
                // Nome della classe del gestore
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parametri del costruttore della classe del gestore
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formattazione correlata
                'formatter' => [
                    // Nome della classe del formattatore
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parametri del costruttore della classe del formattatore
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Canale log2
    'log2' => [
        // Gestori del canale predefinito, è possibile impostarne più di uno
        'handlers' => [
            [   
                // Nome della classe del gestore
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parametri del costruttore della classe del gestore
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formattazione correlata
                'formatter' => [
                    // Nome della classe del formattatore
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parametri del costruttore della classe del formattatore
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Quando si utilizza il canale `log2`, l'uso è il seguente:
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
        $log->info('test log2');
        return response('ciao index');
    }
}
```
