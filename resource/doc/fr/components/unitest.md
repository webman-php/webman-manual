# Tests unitaires

## Installation

```php
composer require --dev phpunit/phpunit
```

## Utilisation
Créez un nouveau fichier `tests/TestConfig.php` pour tester la configuration de la base de données
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

## Exécution
Exécutez dans le répertoire racine du projet `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

Le résultat ressemblera à ceci :
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
