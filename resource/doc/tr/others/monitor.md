# Process Monitoring
webman'in kendinde bir monitor işlemi bulunmaktadır ve iki özelliği destekler:
1. Dosya güncellemelerini izleme ve otomatik olarak yeni iş kodlarını yükleme (genellikle geliştirme aşamasında kullanılır).
2. Tüm işlemlerin bellek kullanımını izleme; eğer bir işlem, `php.ini` içinde belirtilen `memory_limit` sınırını aşmak üzereyse, o işlemi otomatik olarak güvenli bir şekilde yeniden başlatma (iş sürekliliğini etkilemez).

## İzleme Yapılandırması
Yapılandırma dosyası `config/process.php` içinde `monitor` yapılandırmasını içerir.
```php
global $argv;

return [
    // Dosya güncelleme izleme ve otomatik yeniden yükleme
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Bu dizinleri izle
            'monitorDir' => array_merge([    
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Bu uzantılara sahip dosyaları izle
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', 
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                     
            ]
        ]
    ]
];
```
`monitorDir`, hangi dizinlerin güncellemelerinin izleneceğini yapılandırmak için kullanılır (dizinlerin içindeki dosya sayısı fazla olmamalıdır).
`monitorExtensions`, `monitorDir` dizini içinde hangi dosya uzantılarının izlenmesi gerektiğini belirtmek için kullanılır.
`options.enable_file_monitor` değeri `true` ise, dosya güncelleme izleme işlevi etkinleştirilir (Linux sistemlerde debug modunda çalıştırıldığında varsayılan olarak dosya izleme etkinleştirilir).
`options.enable_memory_monitor` değeri `true` ise, bellek kullanım izleme işlevi etkinleştirilir (bellek kullanım izleme, Windows sistemlerinde desteklenmez).

> **Not**
> Windows işletim sistemi altında, dosya güncelleme izleme işlevini etkinleştirmek için `windows.bat` veya `php windows.php` dosyasını çalıştırmak gereklidir.
