# Procesos personalizados

En webman, al igual que en workerman, puedes personalizar la escucha o los procesos.

> **Nota**
> Los usuarios de Windows necesitan usar `php windows.php` para iniciar webman y poder ejecutar procesos personalizados.

## Personalizar el servicio http
A veces puedes tener una necesidad especial que requiere modificar el código principal del servicio http de webman. En este caso, puedes usar procesos personalizados para lograrlo.

Por ejemplo, crea app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Aquí se sobrescriben los métodos de Webman\App
}
```

Agrega la siguiente configuración en `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Otras configuraciones omitidas...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Número de procesos
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Configuración de la clase de solicitud
            'logger' => \support\Log::channel('default'), // Instancia de registro
            'app_path' => app_path(), // Ubicación del directorio app
            'public_path' => public_path() // Ubicación del directorio public
        ]
    ]
];
```

> **Consejo**
> Si deseas desactivar el proceso http incluido en webman, solo necesitas configurar `listen=>''` en config/server.php

## Ejemplo de personalización de escucha de websocket

Crea `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> Nota: Todas las propiedades onXXX son públicas

Agrega la siguiente configuración en `config/process.php`
```php
return [
    // ... Otras configuraciones de proceso omitidas...
    
    // websocket_test es el nombre del proceso
    'websocket_test' => [
        // Aquí se especifica la clase del proceso, que es la clase Pusher definida anteriormente
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Ejemplo de proceso personalizado sin escucha
Crea `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Verifica la base de datos cada 10 segundos para ver si hay nuevos usuarios registrados
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Agrega la siguiente configuración en `config/process.php`
```php
return [
    // ... Otras configuraciones de proceso omitidas...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Nota: Si la escucha se omita, el proceso no escuchará ningún puerto. Si el número de procesos se omite, el valor predeterminado es 1.

## Descripción de los archivos de configuración

La configuración completa de un proceso se define de la siguiente manera:
```php
return [
    // ... 

    // websocket_test es el nombre del proceso
    'websocket_test' => [
        // Aquí se especifica la clase del proceso
        'handler' => app\Pusher::class,
        // Protocolo, IP y puerto escuchado (opcional)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Número de procesos (opcional, por defecto 1)
        'count'   => 2,
        // Usuario que ejecuta el proceso (opcional, usuario actual por defecto)
        'user'    => '',
        // Grupo de usuario que ejecuta el proceso (opcional, grupo de usuario actual por defecto)
        'group'   => '',
        // Si el proceso admite recarga (opcional, true por defecto)
        'reloadable' => true,
        // Si se habilita reusePort (opcional, esta opción requiere php>=7.0, por defecto true)
        'reusePort'  => true,
        // Transporte (opcional, establecer a ssl si es necesario activar ssl, por defecto tcp)
        'transport'  => 'tcp',
        // contexto (opcional, cuando es ssl, se necesitan pasar las rutas de certificados)
        'context'    => [], 
        // Parámetros del constructor de la clase del proceso; en este caso, para process\Pusher::class (opcional)
        'constructor' => [],
    ],
];
```

## Conclusión
Los procesos personalizados en webman son básicamente un simple envoltorio de workerman, separando la configuración del negocio y proporcionando formas de implementar los callbacks `onXXX` de workerman a través de métodos de clase. Todos los demás usos son completamente iguales a los de workerman.
