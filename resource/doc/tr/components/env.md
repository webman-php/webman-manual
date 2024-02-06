# vlucas/phpdotenv

## Açıklama
`vlucas/phpdotenv`, farklı ortamları (geliştirme ortamı, test ortamı vb.) ayırt etmek için kullanılan bir ortam değişkeni yükleme bileşenidir.

## Proje Adresi

https://github.com/vlucas/phpdotenv
  
## Kurulum
 
```php
composer require vlucas/phpdotenv
 ```
  
## Kullanım

#### Proje kök dizininde `.env` dosyası oluşturun
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Yapılandırma dosyasını değiştirin
**config/database.php**
```php
return [
    // Varsayılan veritabanı
    'default' => 'mysql',

    // Çeşitli veritabanı yapılandırmaları
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Not**
> `.env` dosyasını `.gitignore` listesine eklemeniz önerilir, böylece kod deposuna gönderilmez. Proje deposuna `.env.example` adında bir konfigürasyon örnek dosyası ekleyin, proje dağıtımı sırasında `.env.example` dosyasını `.env` olarak kopyalayarak mevcut ortama göre `.env` dosyasını değiştirin, bu sayede proje farklı ortamlarda farklı yapılandırmalarla yüklenebilir.

> **Not**
> `vlucas/phpdotenv`, PHP TS sürümünde (Thread Safe sürümünde) bazı hatalara neden olabilir, NTS sürümünü (Non-Thread Safe sürüm) kullanmanız önerilir.
> Mevcut PHP sürümünü `php -v` komutu ile kontrol edebilirsiniz.

## Daha Fazla Bilgi

https://github.com/vlucas/phpdotenv

