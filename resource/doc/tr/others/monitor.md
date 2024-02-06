# İşlem İzleme
Webman, kendisine ait olan bir izleme işlemine sahiptir, bu işlem iki özelliği destekler:
1. Dosya güncellemelerini izler ve otomatik olarak yeni işletme kodunu yükler (genellikle geliştirme sırasında kullanılır)
2. Tüm işlemlerin bellek kullanımını izler, eğer bir işlemin bellek kullanımı `php.ini` dosyasındaki `memory_limit` limitini aşmak üzere ise o işlemi güvenli bir şekilde yeniden başlatır (işletmeyi etkilemez)

### İzleme Yapılandırması
Yapılandırma dosyası `config/process.php` içerisinde `monitor` yapılandırması bulunmaktadır.
```php
global $argv;

return [
    // Dosya güncelleme algılama ve otomatik yeniden yükleme
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
            // Bu uzantılara sahip dosyalar izlenecek
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Dosya izlemeyi etkinleştir
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Bellek izlemeyi etkinleştir
            ]
        ]
    ]
];
```
`monitorDir`, izlenmesi gereken hangi dizinlerin yapılandırılması için kullanılır (izleme dizinindeki dosya sayısı çok fazla olmamalıdır).
`monitorExtensions`, `monitorDir` içerisinde hangi dosya uzantılarının izlenmesi gerektiğini yapılandırmak için kullanılır.
`options.enable_file_monitor` değeri `true` ise, dosya güncelleme izleme özelliği etkin olur (Linux sistemlerde varsayılan olarak hata ayıklama modunda dosya izleme etkin olur).
`options.enable_memory_monitor` değeri `true` ise, bellek kullanımı izleme özelliği etkin olur (bellek kullanımı izleme özelliği Windows sistemlerde desteklenmez).

> **Not**
> Windows sistemlerde sadece "windows.bat" veya "php windows.php" komutunu çalıştırmak için dosya güncelleme izleme özelliği etkinleştirilebilir.
