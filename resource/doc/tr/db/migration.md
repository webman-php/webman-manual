# Migration Veritabanı Taşıma Aracı Phinx

## Açıklama

Phinx, geliştiricilerin veritabanını basitçe değiştirmesine ve bakımını yapmasına imkan tanır. Elle SQL sorguları yazma gereksinimini ortadan kaldırır ve güçlü PHP API'sini kullanarak veritabanı taşımalarını yönetir. Geliştiriciler, veritabanı taşıma işlemlerini sürüm kontrolüyle yönetebilir. Phinx, farklı veritabanları arasında veri taşımasını kolaylaştırır. Ayrıca hangi taşıma betiklerinin yürütüldüğünü izleyebilir ve geliştiricilerin veritabanı durumundan endişelenmeden daha iyi sistemler nasıl yazacaklarına odaklanmalarını sağlar.

## Proje Adresi

https://github.com/cakephp/phinx

## Kurulum

```php
composer require robmorgan/phinx
```

## Resmi Çince Belgeler Adresi

Detaylı kullanım için resmi Çince belgelere göz atabilirsiniz, burada sadece webman içinde yapılandırma ve kullanımı anlatılacaktır.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Taşıma Dosyası Dizin Yapısı

```plaintext
.
├── app                           Uygulama dizini
│   ├── controller                Denetleyici dizini
│   │   └── Index.php             Denetleyici
│   ├── model                     Model dizini
......
├── database                      Veritabanı dosyaları
│   ├── migrations                Taşıma dosyaları
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Test verileri
│   │   └── UserSeeder.php
......
```

## phinx.php Yapılandırması

Proje ana dizininde phinx.php dosyası oluşturun.

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## Kullanım Önerileri

Taşıma dosyaları bir kez kod birleştirildikten sonra artık değiştirilemez, bir sorun ortaya çıkarsa işlem yapmak için yeni bir değiştirme veya silme işlem dosyası oluşturulmalıdır.

#### Veri tabanı oluşturma işlem dosyası adlandırma kuralı

`{zaman (otomatik oluşturulur)}_create_{tablo_adı_küçük_harflerle}`

#### Veri tabanı değiştirme işlem dosyası adlandırma kuralı

`{zaman (otomatik oluşturulur)}_modify_{tablo_adı_küçük_harflerle+belirli_değişiklikler_küçük_harflerle}`

### Veri tabanı silme işlem dosyası adlandırma kuralı

`{zaman (otomatik oluşturulur)}_delete_{tablo_adı_küçük_harflerle+belirli_değişiklikler_küçük_harflerle}`

### Veri doldurma dosyası adlandırma kuralı

`{zaman (otomatik oluşturulur)}_fill_{tablo_adı_küçük_harflerle+belirli_değişiklikler_küçük_harflerle}`
