# ஒற்றை சோதனை

## நிறுவவும்

```php
composer require --dev phpunit/phpunit
```

## பயன்பாடு

சேவையகத்தின் கட்டமைக்கான `tests/TestConfig.php` எனும் புதிய கோப்பை உருவாக்குக
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

## இயக்குகின்றேன்

கணினியில் ரூட் இடம் பயன்பாடு `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`
முடிவு பெற்றுகொண்டபோது ஒரு போராட்டம் போன்றது:
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
