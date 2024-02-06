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
        // Gestori per il canale predefinito, è possibile configurarne più di uno
        'handlers' => [
            [   
                // Nome della classe gestore
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parametri del costruttore della classe gestore
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formattazione relativa
                'formatter' => [
                    // Nome della classe di formattazione
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parametri del costruttore della classe di formattazione
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Canali multipli
Monolog supporta i canali multipli, di default utilizza il canale `default`. Se si desidera aggiungere un canale `log2`, la configurazione sarà simile a quanto segue:
```php
return [
    // Canale di log predefinito
    'default' => [
        // Gestori per il canale predefinito, è possibile configurarne più di uno
        'handlers' => [
            [   
                // Nome della classe gestore
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parametri del costruttore della classe gestore
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formattazione relativa
                'formatter' => [
                    // Nome della classe di formattazione
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parametri del costruttore della classe di formattazione
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Canale log2
    'log2' => [
        // Gestori per il canale log2, è possibile configurarne più di uno
        'handlers' => [
            [   
                // Nome della classe gestore
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Parametri del costruttore della classe gestore
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formattazione relativa
                'formatter' => [
                    // Nome della classe di formattazione
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Parametri del costruttore della classe di formattazione
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Nel caso si desideri utilizzare il canale `log2`, l'utilizzo sarebbe il seguente:
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
