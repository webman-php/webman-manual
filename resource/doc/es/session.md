# Gestión de sesiones

## Ejemplo
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

Obtenga una instancia de `Workerman\Protocols\Http\Session` a través de `$request->session();` y use los métodos de la instancia para agregar, modificar y eliminar datos de la sesión.

> Nota: Cuando se destruye el objeto de la sesión, los datos de la sesión se guardan automáticamente, por lo que no guarde el objeto devuelto por `$request->session()` en un array global o en un miembro de clase, ya que esto podría impedir la realización del guardado de la sesión.

## Obtener todos los datos de la sesión
```php
$session = $request->session();
$all = $session->all();
```
Devuelve un array. Si no hay datos en la sesión, se devuelve un array vacío.

## Obtener un valor de la sesión
```php
$session = $request->session();
$name = $session->get('name');
```
Si los datos no existen, se devuelve null.

También puede pasar un valor predeterminado como segundo argumento al método `get`. Si el valor correspondiente no se encuentra en la matriz de la sesión, se devuelve el valor predeterminado. Por ejemplo:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## Almacenar en la sesión
Para almacenar un dato, utilice el método `set`.
```php
$session = $request->session();
$session->set('name', 'tom');
```
El método `set` no devuelve ningún valor, ya que los datos de la sesión se guardan automáticamente cuando se destruye el objeto de la sesión.

Cuando se almacenan múltiples valores, se utiliza el método `put`.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
De manera similar, el método `put` no devuelve ningún valor.

## Eliminar datos de la sesión
Para eliminar uno o varios datos de la sesión, utilice el método `forget`.
```php
$session = $request->session();
// Eliminar uno
$session->forget('name');
// Eliminar varios
$session->forget(['name', 'age']);
```

Además, el sistema proporciona el método `delete`, que solo puede eliminar un valor a la vez.
```php
$session = $request->session();
// Equivalente a $session->forget('name');
$session->delete('name');
```

## Obtener y eliminar un valor de la sesión
```php
$session = $request->session();
$name = $session->pull('name');
```
El efecto es el mismo que el siguiente código
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Si la sesión correspondiente no existe, se devuelve null.

## Eliminar todos los datos de la sesión
```php
$request->session()->flush();
```
No devuelve ningún valor, ya que los datos de la sesión se eliminan automáticamente del almacenamiento cuando se destruye el objeto de la sesión.

## Verificar si un dato de la sesión existe
```php
$session = $request->session();
$has = $session->has('name');
```
Si la sesión correspondiente no existe o su valor es null, se devuelve false, de lo contrario, se devuelve true.

```php
$session = $request->session();
$has = $session->exists('name');
```
El código anterior también se utiliza para verificar si los datos de la sesión existen. La diferencia es que si el valor del ítem de la sesión está configurado como null, también devuelve true.

## Función auxiliar session()
> Agregado el 09-12-2020

webman proporciona la función auxiliar `session()` para realizar la misma funcionalidad.
```php
// Obtener instancia de la sesión
$session = session();
// Equivalente a
$session = $request->session();

// Obtener un valor específico
$value = session('key', 'default');
// Equivalente a
$value = session()->get('key', 'default');
// Equivalente a
$value = $request->session()->get('key', 'default');

// Asignar valores a la sesión
session(['key1'=>'value1', 'key2' => 'value2']);
// Equivalente a
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Equivalente a
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Archivo de configuración
El archivo de configuración de la sesión se encuentra en `config/session.php` y su contenido es similar al siguiente:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class o RedisSessionHandler::class o RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Si el controlador es FileSessionHandler::class, el valor será file, 
    // Si el controlador es RedisSessionHandler::class, el valor será redis
    // Si el controlador es RedisClusterSessionHandler::class, el valor será redis_cluster, es decir, un clúster Redis
    'type'    => 'file',

    // Cada controlador utiliza una configuración distinta
    'config' => [
        // Configuración para type = file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuración para type = redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Nombre de la cookie que almacena el id de sesión
    
    // === Las siguientes configuraciones requieren webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // ¿Actualizar automáticamente la sesión? Por defecto está desactivado
    'lifetime' => 7*24*60*60,          // Tiempo de expiración de la sesión
    'cookie_lifetime' => 365*24*60*60, // Tiempo de expiración de la cookie que almacena el id de sesión
    'cookie_path' => '/',              // Ruta de la cookie que almacena el id de sesión
    'domain' => '',                    // Dominio de la cookie que almacena el id de sesión
    'http_only' => true,               // ¿Habilitar httponly? Por defecto está habilitado
    'secure' => false,                 // ¿Habilitar la sesión solo en https? Por defecto está desactivado
    'same_site' => '',                 // Para prevenir ataques CSRF y el seguimiento de usuarios, los valores opcionales son: strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilidad de recolección de la sesión
];
```

> **Nota**
> A partir de webman 1.4.0, se cambió el espacio de nombres de SessionHandler de Webman, desde
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> a 
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## Configuración de caducidad
Cuando webman-framework < 1.3.14, el tiempo de expiración de la sesión en webman se debe configurar en `php.ini`.
```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Supongamos que el tiempo de expiración es de 1440 segundos, la configuración sería la siguiente:
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Consejo**
> Puede usar el comando `php --ini` para buscar la ubicación de `php.ini`.
