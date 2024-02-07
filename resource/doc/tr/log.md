# Günlük
webman [monolog/monolog](https://github.com/Seldaek/monolog) kullanarak günlükleri işler.

## Kullanım
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('günlük test');
        return response('merhaba index');
    }
}
```

## Sağlanan Yöntemler
```php
Log::log($seviye, $mesaj, array $bağlam = [])
Log::debug($mesaj, array $bağlam = [])
Log::info($mesaj, array $bağlam = [])
Log::notice($mesaj, array $bağlam = [])
Log::warning($mesaj, array $bağlam = [])
Log::error($mesaj, array $bağlam = [])
Log::critical($mesaj, array $bağlam = [])
Log::alert($mesaj, array $bağlam = [])
Log::emergency($mesaj, array $bağlam = [])
```
Eşdeğerdir
```php
$log = Log::channel('default');
$log->log($seviye, $mesaj, array $bağlam = [])
$log->debug($mesaj, array $bağlam = [])
$log->info($mesaj, array $bağlam = [])
$log->notice($mesaj, array $bağlam = [])
$log->warning($mesaj, array $bağlam = [])
$log->error($mesaj, array $bağlam = [])
$log->critical($mesaj, array $bağlam = [])
$log->alert($mesaj, array $bağlam = [])
$log->emergency($mesaj, array $bağlam = [])
```

## Yapılandırma
```php
return [
    // Varsayılan günlük kanalı
    'default' => [
        // Varsayılan kanalın işleyicileri, birden fazla ayarlanabilir
        'handlers' => [
            [   
                // işleyici sınıfının adı
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // işleyici sınıfının yapıcı parametreleri
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Biçim ile ilgili
                'formatter' => [
                    // Biçimlendirme işleci sınıfının adı
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Biçimlendirme işleci sınıfının yapıcı parametreleri
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Çoklu Kanal
Monolog, varsayılan olarak `default` kanalını destekler. Bir `log2` kanalı eklemek isterseniz, aşağıdaki gibi yapılandırabilirsiniz:
```php
return [
    // Varsayılan günlük kanalı
    'default' => [
        // Varsayılan kanalın işleyicileri, birden fazla ayarlanabilir
        'handlers' => [
            [   
                // işleyici sınıfının adı
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // işleyici sınıfının yapıcı parametreleri
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Biçim ile ilgili
                'formatter' => [
                    // Biçimlendirme işleci sınıfının adı
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Biçimlendirme işleci sınıfının yapıcı parametreleri
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 kanalı
    'log2' => [
        // log2 kanalının işleyicileri, birden fazla ayarlanabilir
        'handlers' => [
            [   
                // işleyici sınıfının adı
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // işleyici sınıfının yapıcı parametreleri
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Biçim ile ilgili
                'formatter' => [
                    // Biçimlendirme işleci sınıfının adı
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Biçimlendirme işleci sınıfının yapıcı parametreleri
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2` kanalını kullanma yöntemi aşağıdaki gibi olacaktır:
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
        $log->info('log2 test');
        return response('merhaba index');
    }
}
```
