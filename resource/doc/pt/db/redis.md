# Redis

O componente Redis do webman usa por padrão [illuminate/redis](https://github.com/illuminate/redis), que é a biblioteca Redis do Laravel, e é usado da mesma forma que no Laravel.

Antes de usar o `illuminate/redis`, é necessário instalar a extensão do Redis para o `php-cli`.

> **Observação**
> Use o comando `php -m | grep redis` para verificar se a extensão do Redis está instalada para o `php-cli`. Observe que mesmo que a extensão do Redis esteja instalada para o `php-fpm`, isso não significa que ela está disponível para o `php-cli`, pois são aplicativos diferentes e podem usar diferentes arquivos de configuração `php.ini`. Use o comando `php --ini` para verificar qual arquivo de configuração `php.ini` está sendo usado pelo seu `php-cli`.

## Instalação

```php
composer require -W illuminate/redis illuminate/events
```

Após a instalação, é necessário reiniciar (não será eficaz apenas recarregar).

## Configuração
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

## Interface do Redis
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
Equivalente a
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

> **Observação**
> Use com cuidado a interface `Redis::select($db)`, pois o webman é um framework de memória residencial e, se uma solicitação usar `Redis::select($db)` para mudar de banco de dados, isso afetará outras solicitações subsequentes. Para bancos de dados múltiplos, é recomendável configurar diferentes conexões Redis para diferentes `$db`.

## Usando Múltiplas Conexões Redis
Por exemplo, no arquivo de configuração `config/redis.php`
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
Por padrão, é usada a conexão configurada em `default`, mas é possível selecionar qual conexão Redis usar usando o método `Redis::connection()`.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configuração de Clusters
Se sua aplicação usa um cluster de servidores Redis, você deve definir esses clusters no arquivo de configuração do Redis usando a chave clusters:
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

Por padrão, o cluster pode fazer a fragmentação do cliente em nós, permitindo a criação de um pool de nós e alocar grandes quantidades de memória. No entanto, observe que o cliente compartilhado não lida com falhas, sendo útil principalmente para armazenamento em cache que é recuperado de forma redundante de outra base de dados principal. Se deseja usar o cluster nativo do Redis, é necessário especificar o seguinte no arquivo de configuração sob a chave options:

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

## Comandos em Pipeline
Quando precisar enviar muitos comandos para o servidor em uma única operação, é recomendável usar comandos de pipeline. O método pipeline aceita um closure de uma instância do Redis. Todos os comandos enviados para a instância do Redis serão executados em uma única operação:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
