# Pruebas unitarias

## Instalación

```php
composer require --dev phpunit/phpunit
```

## Uso

Cree un archivo `tests/TestConfig.php` para probar la configuración de la base de datos.

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

## Ejecución

Ejecute en el directorio raíz del proyecto:
```bash
./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php
```

El resultado será similar a:
```plaintext
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
