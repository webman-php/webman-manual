# Redis

O componente redis do webman usa por padrão o [illuminate/redis](https://github.com/illuminate/redis), que é a biblioteca redis do Laravel, e é usado da mesma maneira que no Laravel.

Antes de usar o `illuminate/redis`, é necessário instalar a extensão redis para o `php-cli`.

> **Atenção**
> Verifique se a extensão redis está instalada para o `php-cli` usando o comando `php -m | grep redis`. Note que mesmo que você tenha instalado a extensão redis para o `php-fpm`, isso não significa que você poderá usá-la no `php-cli`, já que o `php-cli` e o `php-fpm` são aplicativos diferentes e podem usar diferentes arquivos de configuração `php.ini`. Use o comando `php --ini` para verificar qual arquivo de configuração `php.ini` está sendo usado pelo seu `php-cli`.

## Instalação

```php
composer require -W illuminate/redis illuminate/events
```

Após a instalação, é necessário reiniciar (reload não funciona).

## Configuração
O arquivo de configuração do redis está em `config/redis.php`.

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
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Interface Redis
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
Equivalente a:
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **Atenção**
> Tenha cuidado com a interface `Redis::select($db)`, pois o webman é um framework residente em memória e alterar o banco de dados em uma solicitação afetará solicitações futuras. Para múltiplos bancos de dados, é recomendável configurar diferentes conexões Redis para diferentes configurações de `$db`.

## Usando várias conexões Redis
Por exemplo, no arquivo de configuração `config/redis.php`.

```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],
]
```
Por padrão, é usada a conexão configurada em `default`, mas você pode usar o método `Redis::connection()` para selecionar a conexão redis a ser usada.

```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configuração de Cluster
Se sua aplicação usar um cluster de servidores Redis, você deve definir esses clusters no arquivo de configuração do Redis:

```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

Por padrão, o cluster pode fazer shard no nó do cliente, permitindo pools de nós e criando uma grande quantidade de memória disponível. No entanto, observe que compartilhamentos de clientes não tratam falhas, portanto, essa funcionalidade é principalmente indicada para dados em cache obtidos de outro banco de dados principal. Se deseja usar o cluster nativo do Redis, é necessário fazer a seguinte especificação no arquivo de configuração em `options`:

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Comandos Pipeline
Quando precisa enviar muitos comandos para o servidor em uma única operação, é recomendável usar comandos pipeline. O método pipeline aceita um fechamento de instância Redis. Todos os comandos enviados para a instância Redis serão executados em uma única operação:

```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
