# Unit test

  
## Install
 
```php
composer require --dev phpunit/phpunit
```
  
## Usage
Create a new file `tests/TestConfig.php` for testing the database configuration
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
  
## Run

Run in the project root `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

The result is similar to the following：
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
