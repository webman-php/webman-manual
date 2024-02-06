# Birim Testleri

## Kurulum

```php
composer require --dev phpunit/phpunit
```

## Kullanım
`tests/TestConfig.php` adında yeni bir dosya oluşturun, bu dosya veritabanı yapılandırmasını test etmek için kullanılacak.

```php
<?php
use PHPUnit\Framework\TestCase;

class TestConfig extends TestCase
{
    public function testAppConfig()
    {
        $config = config('app');
        self::assertIsArray($config);
        self::assertArrayHasKey('debug', $config);
        self::assertIsBool($config['debug']);
        self::assertArrayHasKey('default_timezone', $config);
        self::assertIsString($config['default_timezone']);
    }
}
```

## Çalıştırma
Proje kök dizininde `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php` komutunu çalıştırın.

Sonuç aşağıdakine benzer olacaktır:
```
PHPUnit 9.5.10 tarafından Sebastian Bergmann ve katkıda bulunanlar.

.                                                                   1 / 1 (100%)

Zaman: 00:00.010, Bellek: 6.00 MB

OK (1 test, 5 doğrulama)
```
