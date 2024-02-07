# Birim Test

## Kurulum

```php
composer require --dev phpunit/phpunit
```

## Kullanım

`tests/TestConfig.php` adında yeni bir dosya oluşturun ve veritabanı yapılandırmasını test etmek için kullanın.

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

Proje kök dizininde aşağıdaki komutu çalıştırın: `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

Sonuç aşağıdakine benzer olmalıdır:

```plaintext
PHPUnit 9.5.10 tarafından Sebastian Bergmann ve katkıda bulunanlar.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
