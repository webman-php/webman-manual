# vlucas/phpdotenv

## Açıklama
`vlucas/phpdotenv`, farklı ortamların (örneğin geliştirme ortamı, test ortamı vb.) yapılandırmasını ayırt etmek için kullanılan bir ortam değişkeni yükleme bileşenidir.

## Proje Adresi

https://github.com/vlucas/phpdotenv
  
## Kurulum
 
```php
composer require vlucas/phpdotenv
 ```
  
## Kullanım

#### Proje kök dizinine `.env` dosyası oluşturun
**.env**
```plaintext
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
> `.env` dosyasını `.gitignore` listesine eklemenizi öneririz, böylece kod deposuna gönderilmez. Projeye bir `.env.example` yapılandırma örneği dosyası ekleyin. Projeyi dağıtırken `.env.example` dosyasını `.env` olarak kopyalayarak, mevcut ortama göre `.env` dosyasını değiştirerek farklı ortamlarda farklı yapılandırmalar yükleyebilirsiniz.

> **Dikkat**
> `vlucas/phpdotenv` PHP TS sürümünde (thread safe sürümünde) hatalara neden olabilir, lütfen NTS sürümünü (thread safe olmayan sürüm) kullanın.
> Mevcut PHP sürümünü `php -v` komutunu kullanarak kontrol edebilirsiniz.

## Daha Fazla Bilgi

https://github.com/vlucas/phpdotenv
