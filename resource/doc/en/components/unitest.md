# Unit Testing

## Installation

```php
composer require --dev phpunit/phpunit
```

## Usage
Create a file `tests/TestConfig.php` for testing the database configuration.
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

## Execution

Run the following command in the root directory of the project: `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

The result will be similar to the following:
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```