# Unit Testing

## Installation

```php
composer require --dev phpunit/phpunit
```

## Verwendung
Erstellen Sie eine neue Datei `tests/TestConfig.php`, um die Datenbankkonfiguration zu testen.

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

## Ausführen

Führen Sie im Stammverzeichnis des Projekts `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php` aus.

Das Ergebnis sieht ähnlich aus wie folgt:

```
PHPUnit 9.5.10 von Sebastian Bergmann und Mitwirkenden.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
