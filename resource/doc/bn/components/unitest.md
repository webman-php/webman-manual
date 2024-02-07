# ইউনিট টেস্ট

## ইনস্টলেশন

```php
composer require --dev phpunit/phpunit
```

## ব্যবহার

ডেটাবেস কনফিগারেশন পরীক্ষা করার জন্য `tests/TestConfig.php` নামে নতুন ফাইল তৈরি করুন

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

## চালান

প্রজেক্ট রুট ফোল্ডারে নিচের কমান্ড চালান: `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

ফলাফল এরকম দেখা যাবে:
```bash
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
