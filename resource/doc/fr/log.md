# Journal
Webman utilise [monolog/monolog](https://github.com/Seldaek/monolog) pour gérer les journaux.

## Utilisation
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('test de journal');
        return response('bonjour index');
    }
}
```

## Méthodes fournies
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
Équivalent à
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

## Configuration
```php
return [
    // Canal journal par défaut
    'default' => [
        // Gestionnaires du canal par défaut, vous pouvez en configurer plusieurs
        'handlers' => [
            [   
                // Nom de la classe du gestionnaire
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Paramètres du constructeur de la classe du gestionnaire
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatage
                'formatter' => [
                    // Nom de la classe du formateur
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Paramètres du constructeur de la classe du formateur
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Multi-canaux
Monolog prend en charge les multi-canaux, utilisant par défaut le canal `default`. Si vous souhaitez ajouter un canal `log2`, la configuration serait similaire à ceci :
```php
return [
    // Canal journal par défaut
    'default' => [
        // Gestionnaires du canal par défaut, vous pouvez en configurer plusieurs
        'handlers' => [
            [   
                // Nom de la classe du gestionnaire
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Paramètres du constructeur de la classe du gestionnaire
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatage
                'formatter' => [
                    // Nom de la classe du formateur
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Paramètres du constructeur de la classe du formateur
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Canal log2
    'log2' => [
        // Gestionnaires du canal log2, vous pouvez en configurer plusieurs
        'handlers' => [
            [   
                // Nom de la classe du gestionnaire
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Paramètres du constructeur de la classe du gestionnaire
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatage
                'formatter' => [
                    // Nom de la classe du formateur
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Paramètres du constructeur de la classe du formateur
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

L'utilisation du canal `log2` est illustrée comme suit :
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
        $log->info('test du log2');
        return response('bonjour index');
    }
}
```
