# Cache

O webman usa por padrão [symfony/cache](https://github.com/symfony/cache) como componente de cache.

> É necessário instalar a extensão redis para `php-cli` antes de usar o `symfony/cache`.

## Instalação
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Depois de instalar, é necessário reiniciar (reload não é válido)

## Configuração do Redis
O arquivo de configuração do Redis está em `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## Exemplo
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **Observação**
> É recomendável adicionar um prefixo à chave para evitar conflitos com outros usos do Redis.

## Usando Outro Componente de Cache

O uso do componente [ThinkCache](https://github.com/top-think/think-cache) será referenciado em [Outras bases de dados](others.md#ThinkCache).
