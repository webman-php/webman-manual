## Cola de Redis

Cola de mensajes basada en Redis que admite el procesamiento de mensajes con retraso.

## Instalación
`composer require webman/redis-queue`

## Archivo de configuración
El archivo de configuración de Redis se genera automáticamente en `config/plugin/webman/redis-queue/redis.php`, con un contenido similar a este:

```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // Contraseña, parámetro opcional
            'db' => 0,            // Base de datos
            'max_attempts'  => 5, // Número de intentos después de un fallo de consumo
            'retry_seconds' => 5, // Intervalo de reintentos, en segundos
        ]
    ],
];
```

### Reintento en caso de fallo de consumo
Si hay un fallo de consumo (ocurre una excepción), el mensaje se coloca en la cola de retraso y espera para ser reintentado. El número de reintentos está controlado por el parámetro `max_attempts`, y el intervalo de reintentos es controlado por los parámetros `retry_seconds` y `max_attempts`. Por ejemplo, si `max_attempts` es 5 y `retry_seconds` es 10, el intervalo de reintentos para el primer intento es `1*10` segundos, el intervalo para el segundo intento es `2*10` segundos, el tercero es `3*10` segundos, y así sucesivamente hasta el quinto reintento. Si se supera el número de reintentos establecido en `max_attempts`, el mensaje se coloca en la cola de fallos con la clave `"{redis-queue}-failed"`.

## Envío de mensajes (sincrónico)
> **注意**
> Se requiere webman/redis >= 1.2.0, depende de la extensión redis

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Nombre de la cola
        $queue = 'send-mail';
        // Data, que puede ser un array directo, no es necesario serializar
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Envío del mensaje
        Redis::send($queue, $data);
        // Envío de un mensaje con retraso, que se procesará 60 segundos después
        Redis::send($queue, $data, 60);

        return response('Prueba de cola de Redis');
    }

}
```
Si el envío es exitoso, `Redis::send()` devuelve true; de lo contrario, devuelve false o arroja una excepción.

> **Nota**
> Puede haber una discrepancia en el tiempo de consumo de la cola de retraso. Por ejemplo, si la velocidad de consumo es menor que la velocidad de producción y la cola se acumula, puede haber un retraso en el consumo. Una forma de mitigar esto es ejecutar más procesos de consumo.

## Envío de mensajes (asincrónico)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Nombre de la cola
        $queue = 'send-mail';
        // Data, que puede ser un array directo, no es necesario serializar
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Envío del mensaje
        Client::send($queue, $data);
        // Envío de un mensaje con retraso, que se procesará 60 segundos después
        Client::send($queue, $data, 60);

        return response('Prueba de cola de Redis');
    }

}
```
`Client::send()` no tiene un valor de retorno; es un envío asincrónico y no garantiza que el mensaje se entregue al 100% a Redis.

> **Nota**
> El principio de `Client::send()` es que se crea una cola de memoria local para enviar el mensaje de forma asincrónica a Redis (la velocidad de envío es rápida, aproximadamente 10,000 mensajes por segundo). Si el proceso se reinicia y la cola de memoria local no ha completado la sincronización, puede haber pérdida de mensajes. `Client::send()` es adecuado para enviar mensajes no críticos.

> **Nota**
> `Client::send()` es asincrónico y solo puede usarse en el entorno de ejecución de workerman. Para scripts de línea de comandos, utilice la interfaz sincrónica `Redis::send()`.

## Envío de mensajes desde otros proyectos
A veces es necesario enviar mensajes desde otros proyectos y no se puede usar `webman\redis-queue`. En esos casos, puede usar la siguiente función para enviar mensajes a la cola:

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

Donde el parámetro `$redis` es una instancia de redis. Por ejemplo, el uso de la extensión redis es similar a esto:

```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
````

## Consumo
El archivo de configuración del proceso de consumo se encuentra en `config/plugin/webman/redis-queue/process.php`.
El directorio de consumidores está en `app/queue/redis/`.

Al ejecutar el comando `php webman redis-queue:consumer my-send-mail`, se generará el archivo `app/queue/redis/MyMailSend.php`.

> **Nota**
> Si el comando no existe, puede generarse manualmente.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Nombre de la cola a consumir
    public $queue = 'send-mail';

    // Nombre de conexión, compatible con la conexión en `plugin/webman/redis-queue/redis.php`
    public $connection = 'default';

    // Consumo
    public function consume($data)
    {
        // No es necesario deserializar
        var_export($data); // Muestra ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **Atención**
> Si durante el proceso de consumo no se produce una excepción o un Error, se considera que el consumo fue exitoso; de lo contrario, se considera que el consumo falló y el mensaje se coloca en la cola de reintentos.
> redis-queue no tiene un mecanismo ack. Puede considerarse como un ack automático (si no se produce una excepción o un Error). Si durante el proceso de consumo desea marcar que el mensaje actual no se consumió correctamente, puede lanzar manualmente una excepción para que el mensaje entre en la cola de reintentos. Esto es prácticamente lo mismo que un mecanismo ack.

> **Nota**
> Los consumidores admiten múltiples servidores y procesos, y el mismo mensaje **no** será consumido repetidamente. Los mensajes consumidos se eliminan automáticamente de la cola, no es necesario eliminarlos manualmente.

> **Nota**
> Los procesos de consumo pueden consumir múltiples colas diferentes, y no es necesario modificar la configuración en `process.php` al agregar nuevas colas. Solo es necesario agregar la clase `Consumer` correspondiente a la cola de consumo en `app/queue/redis`, y utilizar el atributo de clase `$queue` para especificar la cola que se va a consumir.

> **Nota**
> Los usuarios de Windows deben ejecutar `php windows.php` para iniciar webman; de lo contrario, no se iniciarán los procesos de consumo.

## Configurar diferentes procesos de consumo para diferentes colas
De forma predeterminada, todos los consumidores comparten el mismo proceso de consumo. Sin embargo, a veces necesitamos separar algunos consumidores, por ejemplo, colocar los consumidores lentos en un grupo de procesos de consumo y los consumidores rápidos en otro grupo. Para hacer esto, podemos dividir los consumidores en dos directorios, por ejemplo `app_path() . '/queue/redis/fast'` y `app_path() . '/queue/redis/slow'` (tenga en cuenta que se debe cambiar el espacio de nombres de las clases de consumidores correspondientes), luego configurar lo siguiente:

```php
return [
    ...Otras configuraciones aquí...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Directorio de clases de consumidores
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Directorio de clases de consumidores
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

Con esta clasificación por directorios y su respectiva configuración, es sencillo establecer diferentes procesos de consumo para distintos consumidores.
## Configuración múltiple de Redis
#### Configuración
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // Contraseña, tipo de cadena, parámetro opcional
            'db' => 0,            // Base de datos
            'max_attempts'  => 5, // Intentos de reintentos después de fallo de consumo
            'retry_seconds' => 5, // Intervalo de reintentos, en segundos
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // Contraseña, tipo de cadena, parámetro opcional
            'db' => 0,             // Base de datos
            'max_attempts'  => 5, // Intentos de reintentos después de fallo de consumo
            'retry_seconds' => 5, // Intervalo de reintentos, en segundos
        ]
    ],
];
```

Tenga en cuenta que se ha agregado una configuración de Redis con la clave `other`.

#### Envío de mensajes a múltiples Redis

```php
// Enviar mensajes a la cola con la clave `default`
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// Equivalente a
Client::send($queue, $data);
Redis::send($queue, $data);

// Enviar mensajes a la cola con la clave `other`
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### Consumo de múltiples Redis
Consumir mensajes de la cola con la clave `other` en la configuración
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Nombre de la cola a consumir
    public $queue = 'send-mail';

    // === Establecerlo en 'other' para consumir de la cola con la clave 'other' en la configuración ===
    public $connection = 'other';

    // Consumir
    public function consume($data)
    {
        // No es necesario deserializar
        var_export($data);
    }
}
```

## Preguntas frecuentes

**¿Por qué aparece el error `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`?**

Este error solo ocurrirá en la interfaz de envío asincrónico `Client::send()`. Primero, el envío asincrónico guarda el mensaje en la memoria local y, cuando el proceso está inactivo, envía el mensaje a Redis. Si la velocidad a la que Redis recibe los mensajes es más lenta que la velocidad de producción de mensajes, o si el proceso está ocupado con otras tareas y no tiene suficiente tiempo para sincronizar los mensajes de la memoria a Redis, se producirá un atasco de mensajes. Si un mensaje se atasca durante más de 600 segundos, se activará este error.

Solución: Utilice la interfaz de envío sincrónico `Redis::send()` para enviar mensajes.
