# Testes Unitários

## Instalação

```php
composer require --dev phpunit/phpunit
```

## Utilização
Crie um arquivo `tests/TestConfig.php` para testar a configuração do banco de dados.

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

## Execução

Execute no diretório raiz do projeto `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

O resultado será semelhante ao seguinte:

```php
PHPUnit 9.5.10 por Sebastian Bergmann e colaboradores.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 teste, 5 asserções)
```
