# यूनिट टेस्टिंग

  
## स्थापना
 
```php
composer require --dev phpunit/phpunit
```
  
## उपयोग
`tests/TestConfig.php` नामक नया फ़ाइल बनाएं, जो डेटाबेस कॉन्फ़िगरेशन का परीक्षण करने के लिए है
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
  
## चालू
प्रोजेक्ट रूट फ़ोल्डर में `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php` को चलाएं

परिणाम निम्नलिखित प्रकार का होगा:
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
