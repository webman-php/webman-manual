# Günlük
webman, günlükleri işlemek için [monolog/monolog](https://github.com/Seldaek/monolog) kullanmaktadır.

## Kullanımı
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('günlük testi');
        return response('merhaba index');
    }
}
```

## Sağlanan Metodlar
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
Aynıdır
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
        // Varsayılan kanal için işleyiciler, birden fazla ayarlayabilirsiniz
        'handlers' => [
            [   
                // işleyici sınıfın adı
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // işleyici sınıfın inşa fonksiyonu parametreleri
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Biçimleme ile ilgili
                'formatter' => [
                    // Biçimlendirme işleyici sınıfın adı
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Biçimlendirme işleyici sınıfın inşa fonksiyonu parametreleri
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Çoklu Kanal
monolog, varsayılan olarak `default` kanalını destekler. `log2` adında bir kanal eklemek istiyorsanız, konfigürasyon aşağıdaki gibi olmalıdır:
```php
return [
    // Varsayılan günlük kanalı
    'default' => [
        // Varsayılan kanal için işleyiciler, birden fazla ayarlayabilirsiniz
        'handlers' => [
            [   
                // işleyici sınıfın adı
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // işleyici sınıfın inşa fonksiyonu parametreleri
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Biçimleme ile ilgili
                'formatter' => [
                    // Biçimlendirme işleyici sınıfın adı
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Biçimlendirme işleyici sınıfın inşa fonksiyonu parametreleri
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 kanalı
    'log2' => [
        // Varsayılan kanal için işleyiciler, birden fazla ayarlayabilirsiniz
        'handlers' => [
            [   
                // işleyici sınıfın adı
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // işleyici sınıfın inşa fonksiyonu parametreleri
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Biçimleme ile ilgili
                'formatter' => [
                    // Biçimlendirme işleyici sınıfın adı
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Biçimlendirme işleyici sınıfın inşa fonksiyonu parametreleri
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2` kanalını kullanırken, kullanımı şu şekildedir:
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
        $log->info('log2 testi');
        return response('merhaba index');
    }
}
```
