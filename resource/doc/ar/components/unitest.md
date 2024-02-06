# اختبار الوحدة

## التثبيت

```php
composer require --dev phpunit/phpunit
```

## الاستخدام
قم بإنشاء ملف `tests/TestConfig.php` لاختبار تكوين قاعدة البيانات
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

## التشغيل

قم بتشغيل الأمر التالي في الدليل الرئيسي للمشروع `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

النتيجة تكون مماثلة للتالي:
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
