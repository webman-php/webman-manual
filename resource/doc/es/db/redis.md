# Redis

El componente Redis de webman utiliza por defecto [illuminate/redis](https://github.com/illuminate/redis), que es la biblioteca de Redis de Laravel, y se utiliza de la misma manera que en Laravel.

Antes de utilizar `illuminate/redis`, es necesario instalar la extensión de Redis para `php-cli`.

> **Nota**
> Utiliza el comando `php -m | grep redis` para verificar si la extensión de Redis está instalada para `php-cli`. Ten en cuenta que aunque hayas instalado la extensión de Redis para `php-fpm`, no significa que la puedas utilizar en `php-cli`, ya que `php-cli` y `php-fpm` son aplicaciones diferentes que pueden usar diferentes configuraciones de `php.ini`. Utiliza el comando `php --ini` para ver qué archivo de configuración `php.ini` utiliza tu `php-cli`.

## Instalación

```php
composer require -W illuminate/redis illuminate/events
```

Después de la instalación, es necesario reiniciar (reload no funcionará)

## Configuración

El archivo de configuración de Redis se encuentra en `config/redis.php`.
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

## Ejemplo

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

## Interfaz de Redis

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

> **Nota**
> Uso cuidadoso de la interfaz `Redis::select($db)`, ya que webman es un marco de aplicación en memoria constante, cambiar la base de datos con `Redis::select($db)` en una solicitud afectará a las solicitudes siguientes. Se recomienda configurar diferentes conexiones de Redis para bases de datos múltiples con diferentes configuraciones de `$db`.

## Uso de múltiples conexiones Redis

Por ejemplo, en el archivo de configuración `config/redis.php`
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
Por defecto, se utiliza la conexión configurada en `default`, pero puedes elegir qué conexión de Redis utilizar con el método `Redis::connection()`.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configuración de clúster

Si tu aplicación utiliza un clúster de servidores Redis, deberías definir estos clústeres en el archivo de configuración de Redis utilizando la clave `clusters`:
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

Por defecto, el clúster permite la fragmentación del cliente en nodos, lo que te permite crear grandes pools de memoria disponibles. Es importante tener en cuenta que el cliente compartido no manejará las fallas, por lo que esta función se usa principalmente para cachear datos obtenidos de otra base de datos principal. Si deseas usar el clúster nativo de Redis, debes hacer la siguiente especificación en la clave `options` del archivo de configuración:

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

## Comandos de canalización

Si necesitas enviar muchos comandos al servidor en una sola operación, se recomienda utilizar los comandos de canalización. El método `pipeline` acepta un cierre de instancia de Redis. Puedes enviar todos los comandos a la instancia de Redis, y se ejecutarán todos en una sola operación:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
