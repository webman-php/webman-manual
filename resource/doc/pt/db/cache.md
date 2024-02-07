# Cache

No webman, o [symfony/cache](https://github.com/symfony/cache) é usado como componente de cache por padrão.

> Antes de usar o `symfony/cache`, o módulo de extensão do Redis deve ser instalado para o `php-cli`.

## Instalação
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Após a instalação, é necessário reiniciar (reload não é eficaz).

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

> **Nota**
> É recomendável adicionar um prefixo à chave para evitar conflitos com outros usos do Redis.

## Uso de Outros Componentes de Cache

O componente [ThinkCache](https://github.com/top-think/think-cache) pode ser referenciado para uso em [outros bancos de dados](others.md#ThinkCache).
