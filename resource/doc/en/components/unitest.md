# Unit Testing

## Installation
 
```php
composer require --dev phpunit/phpunit
```

## Usage
Create a file `tests/TestConfig.php` to test the database configuration.
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

## Running

Run the command `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php` in the root directory of your project.

The result will be similar to the following:
```text
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
