# Redis

El componente de Redis de webman utiliza por defecto [illuminate/redis](https://github.com/illuminate/redis), que es la biblioteca de Redis de Laravel, y se utiliza de la misma manera que en Laravel.

Antes de usar `illuminate/redis`, debe instalar la extensión Redis para `php-cli`.

> **Nota**
> Use el comando `php -m | grep redis` para verificar si la extensión Redis está instalada para `php-cli`. Tenga en cuenta que incluso si tiene instalada la extensión Redis para `php-fpm`, no significa que pueda usarla para `php-cli`, ya que `php-cli` y `php-fpm` son aplicaciones diferentes y pueden usar configuraciones diferentes en `php.ini`. Use el comando `php --ini` para verificar qué archivo de configuración `php.ini` está utilizando su `php-cli`.

## Instalación

```php
composer require -W illuminate/redis illuminate/events
```

Después de la instalación, es necesario reiniciar (no recargar) el servidor.

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
> Tenga cuidado al usar el método `Redis::select($db)`, ya que webman es un marco de trabajo en memoria constante, si una solicitud utiliza `Redis::select($db)` para cambiar la base de datos, afectará a las solicitudes posteriores. Para múltiples bases de datos, se recomienda configurar diferentes conexiones Redis para diferentes `$db`.

## Uso de múltiples conexiones Redis
Por ejemplo, en el archivo de configuración `config/redis.php`:
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
Por defecto se utiliza la conexión configurada en `default`, puede utilizar el método `Redis::connection()` para elegir qué conexión de redis usar:
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Configuración de clúster
Si su aplicación utiliza un clúster de servidores Redis, debe definir estos clústeres en el archivo de configuración de Redis utilizando la clave clusters:
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

Por defecto, los clústeres pueden implementar el fragmento del cliente en los nodos, lo que le permite implementar piscinas de nodos y crear grandes cantidades de memoria disponibles. Tenga en cuenta que el fragmento compartido del cliente no manejará los fallos; por lo tanto, esta función es principalmente adecuada para el almacenamiento en caché de datos obtenidos de otra base de datos principal. Si desea utilizar el clúster nativo de Redis, debe hacer la siguiente especificación en la clave options del archivo de configuración:
```php
return [
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Comandos de pipeline
Cuando necesite enviar múltiples comandos al servidor en una sola operación, se recomienda utilizar comandos de pipeline. El método pipeline acepta un cierre de instancia de Redis. Puede enviar todos los comandos a la instancia de Redis, y todos se ejecutarán en una sola operación:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
