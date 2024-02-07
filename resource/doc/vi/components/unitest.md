# Kiểm thử đơn vị

## Cài đặt

```php
composer require --dev phpunit/phpunit
```

## Sử dụng

Tạo tệp `tests/TestConfig.php` để kiểm thử cấu hình cơ sở dữ liệu

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

## Chạy

Chạy trong thư mục gốc của dự án `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

Kết quả sẽ giống như sau:

``` 
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
