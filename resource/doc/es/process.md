# Proceso personalizado

En webman, al igual que en workerman, puedes personalizar tus procesos de escucha o tus propios procesos.

> **Nota**
> Los usuarios de Windows deben usar `php windows.php` para iniciar webman y poder ejecutar un proceso personalizado.

## Servicio http personalizado
A veces, es posible que tengas requisitos especiales que requieran cambiar el núcleo del servicio http de webman. En este caso, puedes usar un proceso personalizado para lograrlo.

Por ejemplo, crea `app\Server.php`:

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Aquí puedes sobrescribir los métodos de Webman\App
}
```

Agrega la siguiente configuración en `config/process.php`:

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
            'request_class' => \support\Request::class, // Configuración de la clase request
            'logger' => \support\Log::channel('default'), // Instancia de registro
            'app_path' => app_path(), // Ubicación del directorio app
            'public_path' => public_path() // Ubicación del directorio public
        ]
    ]
];
```

> **Consejo**
> Si deseas cerrar el proceso http integrado de webman, simplemente establece `listen=>''` en `config/server.php`.

## Ejemplo de escucha personalizada de websocket
Crea `app/Pusher.php`:

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
> Nota: Todas las propiedades onXXX son públicas.

Agrega la siguiente configuración en `config/process.php`:

```php
return [
    // ... Otras configuraciones de procesos omitidas...
    
    // websocket_test es el nombre del proceso
    'websocket_test' => [
        // Aquí se especifica la clase del proceso, es decir, la clase Pusher definida anteriormente
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Ejemplo de proceso sin escucha personalizada
Crea `app/TaskTest.php`:

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Verificar si hay nuevos usuarios registrados en la base de datos cada 10 segundos
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Agrega la siguiente configuracion en `config/process.php`:

```php
return [
    // ... Otras configuraciones de procesos omitidas...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Nota: Si se omite la escucha, el proceso no escuchará ningún puerto; si se omite el número de procesos, el valor predeterminado es 1.

## Explicación de la configuración

Una configuración completa de un proceso se muestra a continuación:

```php
return [
    // ...
    
    // websocket_test es el nombre del proceso
    'websocket_test' => [
        // Aquí se especifica la clase del proceso
        'handler' => app\Pusher::class,
        // Protocolo, IP y puerto a escuchar (opcional)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Número de procesos (opcional, 1 por defecto)
        'count'   => 2,
        // Usuario que ejecutará el proceso (opcional, usuario actual por defecto)
        'user'    => '',
        // Grupo de usuario que ejecutará el proceso (opcional, grupo de usuario actual por defecto)
        'group'   => '',
        // Compatibilidad con recarga del proceso (opcional, verdadero por defecto)
        'reloadable' => true,
        // Habilitar reusePort (opcional, requerido que php>=7.0, verdadero por defecto)
        'reusePort'  => true,
        // Transporte (opcional, configurar como ssl si se necesita habilitar SSL, predeterminado es tcp)
        'transport'  => 'tcp',
        // Contexto (opcional, cuando se habilita SSL, se requiere la ruta al certificado)
        'context'    => [],
        // Parámetros del constructor de la clase del proceso, en este caso para process\Pusher::class (opcional)
        'constructor' => [],
    ],
];
```

## Conclusión
El proceso personalizado en webman es simplemente un envoltorio simple para workerman. Separación de la configuración y la lógica del negocio, así como la implementación de los callbacks `onXXX` de workerman a través de los métodos de las clases, son idénticos a los métodos normales de workerman.
