# Migration Veritabanı Taşıma Aracı Phinx

## Açıklama

Phinx geliştiricilerin veritabanlarını kolayca değiştirmesine ve bakımını yapmasına olanak tanır. Elle SQL sorguları yazma ihtiyacını ortadan kaldırır ve güçlü bir PHP API kullanarak veritabanı taşımayı yönetir. Geliştiriciler, veritabanlarını sürüm kontrolü ile yönetebilirler. Phinx, farklı veritabanları arasında kolayca veri taşınmasını sağlar. Ayrıca hangi taşıma betiklerinin çalıştırıldığını izleyebilir, bu sayede geliştiriciler veritabanının durumundan endişelenmeyerek daha iyi bir sistem oluşturmayı düşünebilirler.

## Proje Adresi

https://github.com/cakephp/phinx

## Kurulum

```php
composer require robmorgan/phinx
```

## Resmi Türkçe Belgelerin Adresi

Detaylı kullanım için resmi Türkçe belgelere bakabilirsiniz, burada sadece webman'de nasıl yapılandırılacağını anlatacağız.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Taşıma Dosyası Dizin Yapısı

```
.
├── app                           Uygulama Dizini
│   ├── controller                Denetleyici Dizini
│   │   └── Index.php             Denetleyici
│   ├── model                     Model Dizini
......
├── database                      Veritabanı Dosyaları
│   ├── migrations                Taşıma Dosyaları
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Test Verileri
│   │   └── UserSeeder.php
......
```

## phinx.php Yapılandırması

Proje kök dizininde phinx.php adında bir dosya oluşturun

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

## Kullanım Tavsiyeleri

Taşıma dosyaları bir kez kod birleştikten sonra tekrar değiştirilmesine izin verilmez, bir sorun oluşursa değişiklik veya silme işlemi için yeni bir dosya oluşturulmalıdır.

#### Veri tabanı oluşturma işlemi dosya adlandırma kuralı

`{zaman (otomatik oluştur)}_create_{tablo_adı_küçük_harf}`

#### Veri tabanı değiştirme işlemi dosya adlandırma kuralı

`{zaman (otomatik oluştur)}_modify_{tablo_adı_küçük_harf+özel_değişiklik_öğesi_küçük_harf}`

### Veri tabanı silme işlemi dosya adlandırma kuralı

`{zaman(otomatik oluştur)}_delete_{tablo_adı_küçük_harf+özel_değişiklik_öğesi_küçük_harf}`

### Veri doldurma dosya adlandırma kuralı

`{zaman(otomatik oluştur)}_fill_{tablo_adı_küçük_harf+özel_değişiklik_öğesi_küçük_harf}`
