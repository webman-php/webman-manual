# ஒற்றை சோதனை

## நிறுவல்

```php
composer require --dev phpunit/phpunit
```

## பயன்பாடு

புதிய கோப்பு `tests/TestConfig.php` உருவாக்கவும், தரவற்ற உள்ளது சோதிக்கவும்
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

## இயக்குவது

திட்டம் ரூட் உருப்பினால் `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php` இயக்கம்

முடிவு போக்குவது போன்ற குறிப்புகள் கொண்டுள்ளது:
``` 
PHPUnit 9.5.10 ஆனது சேபாஸ்டியன் பெர்க்மான் மற்றும் பங்கிகள் இயக்கம்.

.                                                                   1 / 1 (100%)

நேரம்: 00:00.010, நினைவகம்: 6.00 எம்.பி

சரி (1 சோதனை, 5 கருத்துகள்)
```
