# एकक परीक्षण

## स्थापना

```php
composer require --dev phpunit/phpunit
```

## उपयोग
`tests/TestConfig.php` फ़ाइल नई बनाएं, जिसे डेटाबेस कॉन्फ़िगरेशन की जांच के लिए उपयोग किया जाएगा।

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

## चलाना

प्रोजेक्ट रूट फ़ोल्डर में `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php` को चलाएँ।

नतीजा निम्नलिखित तरह होगा:
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
