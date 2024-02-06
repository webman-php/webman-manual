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
        return response('¡Hola ' . $session->get('name'));
    }
}
```

Obtén una instancia de `Workerman\Protocols\Http\Session` mediante `$request->session();`, y utiliza los métodos de la instancia para agregar, modificar o eliminar datos de la sesión.

> Nota: La instancia de sesión destruirá automáticamente los datos de la sesión cuando se destruya, por lo tanto, no guardes el objeto devuelto por `$request->session()` en un arreglo global o en una propiedad de clase, ya que esto podría evitar que la sesión se guarde.

## Obtener todos los datos de sesión
```php
$session = $request->session();
$all = $session->all();
```
Devuelve un array. Si no hay datos de sesión, devuelve un array vacío.

## Obtener un valor específico de la sesión
```php
$session = $request->session();
$name = $session->get('name');
```
Devuelve null si los datos no existen.

También puedes pasar un valor predeterminado como segundo parámetro a la función "get". Si el valor correspondiente no se encuentra en el array de sesión, se devuelve el valor predeterminado. Por ejemplo:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## Almacenar sesión
Utiliza el método "set" para almacenar un dato.
```php
$session = $request->session();
$session->set('name', 'tom');
```
El método "set" no devuelve ningún valor; los datos de la sesión se guardarán automáticamente cuando se destruya el objeto de la sesión.

Cuando almacenes varios valores, usa el método "put".
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Al igual que "set", "put" no devuelve ningún valor.

## Eliminar datos de sesión
Utiliza el método `forget` para eliminar uno o varios datos de la sesión.
```php
$session = $request->session();
// Eliminar un dato
$session->forget('name');
// Eliminar varios datos
$session->forget(['name', 'age']);
```

Además, el sistema proporciona el método "delete", que difiere de "forget" en que solo puede eliminar un dato.
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
Este efecto es equivalente al siguiente código
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
No devuelve ningún valor; los datos de la sesión se eliminarán automáticamente del almacenamiento cuando se destruya el objeto de la sesión.

## Verificar si un dato de sesión específico existe
```php
$session = $request->session();
$has = $session->has('name');
```
Si la sesión correspondiente no existe o el valor de la sesión es null, devuelve false; de lo contrario, devuelve true.

```php
$session = $request->session();
$has = $session->exists('name');
```
Este código también se utiliza para verificar si los datos de sesión existen. La diferencia es que, si el valor del elemento de sesión correspondiente es null, también devuelve true.

## Función de ayuda session()
> Añadido el 09-12-2020

webman proporciona la función de ayuda `session()` para realizar la misma funcionalidad.
```php
// Obtener una instancia de sesión
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
// Similar a
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Similar a
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## Archivo de configuración
El archivo de configuración de sesión se encuentra en `config/session.php`, con un contenido similar al siguiente:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class o RedisSessionHandler::class o RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Si el manejador es FileSessionHandler::class, el valor es 'file',
    // Si el manejador es RedisSessionHandler::class, el valor es 'redis'
    // Si el manejador es RedisClusterSessionHandler::class, el valor es 'redis_cluster' (para clústeres de Redis)
    'type'    => 'file',

    // Cada manejador utiliza una configuración distinta
    'config' => [
        // Configuración para type 'file'
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuración para type 'redis'
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

    'session_name' => 'PHPSID', // Nombre de la cookie para almacenar el ID de la sesión
    
    // === Las siguientes configuraciones requieren webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Actualización automática de la sesión, por defecto desactivada
    'lifetime' => 7*24*60*60,          // Tiempo de expiración de la sesión
    'cookie_lifetime' => 365*24*60*60, // Tiempo de expiración de la cookie para el ID de sesión
    'cookie_path' => '/',              // Ruta de la cookie para el ID de sesión
    'domain' => '',                    // Dominio de la cookie para el ID de sesión
    'http_only' => true,               // Habilitar httpOnly, por defecto habilitado
    'secure' => false,                 // Solo habilitar la sesión en HTTPS, por defecto deshabilitado
    'same_site' => '',                 // Para prevenir ataques CSRF y seguimiento de usuarios, valores opcionales: strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilidad de recolección de la sesión
];
```

> **Nota**
> A partir de webman 1.4.0, el espacio de nombres SessionHandler se ha cambiado de
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> a
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  


## Configuración de período de validez
Cuando webman-framework < 1.3.14, el tiempo de expiración de la sesión en webman debe ser configurado en `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Por ejemplo, si se define un tiempo de expiración de 1440 segundos, la configuración sería la siguiente:
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Nota**
> Puedes usar el comando `php --ini` para encontrar la ubicación del archivo `php.ini`.
