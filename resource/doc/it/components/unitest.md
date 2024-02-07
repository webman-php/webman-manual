# Test delle unità

## Installazione

```php
composer require --dev phpunit/phpunit
```

## Utilizzo

Creare un nuovo file `tests/TestConfig.php` per testare la configurazione del database:

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

## Esecuzione

Eseguire nel directory radice del progetto: `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

Il risultato sarà simile al seguente:

```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
