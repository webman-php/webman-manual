# Corrutinas

> **Requisitos de las corrotinas**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Comando de actualización de webman `composer require workerman/webman-framework ^1.5.0`
> Comando de actualización de workerman `composer require workerman/workerman ^5.0.0`
> Se requiere la instalación de Fiber coroutines `composer require revolt/event-loop ^1.0.0`

# Ejemplo
### Respuesta retardada

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Dormir durante 1.5 segundos
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` es similar a la función `sleep()` incorporada de PHP, la diferencia es que `Timer::sleep()` no bloqueará el proceso.


### Realizar solicitudes HTTP

> **Nota**
> Se necesita instalar composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Realizar solicitudes asincrónicas de forma síncrona
        return $response->getBody()->getContents();
    }
}
```
De manera similar, la solicitud `$client->get('http://example.com')` es no bloqueante, lo que puede utilizarse para realizar solicitudes HTTP no bloqueantes en webman y mejorar el rendimiento de la aplicación.

Para más información, consulta [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Agregando la clase support\Context

La clase `support\Context` se utiliza para almacenar datos de contexto de solicitud, que se eliminarán automáticamente cuando se complete la solicitud. Es decir, el ciclo de vida de los datos del contexto sigue el ciclo de vida de la solicitud. `support\Context` es compatible con el entorno de la corutina Fiber, Swoole y Swow.



### Corrutinas de Swoole
Después de instalar la extensión de Swoole (se requiere Swoole>=5.0), activa las corrutinas de Swoole a través de la configuración de config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Para más información, consulta [manejo de eventos de workerman](https://www.workerman.net/doc/workerman/appendices/event.html)

### Contaminación de variables globales

En el entorno de la corrutina, está prohibido almacenar información de estado **relacionada con la solicitud** en las variables globales o estáticas, ya que esto podría causar contaminación de variables globales, como en el ejemplo a continuación:

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

Al establecer el número de procesos en 1, cuando realizamos dos solicitudes consecutivas
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
Esperamos que los resultados de las dos solicitudes sean respectivamente `lilei` y `hanmeimei`, pero en realidad ambos resultados son `hanmeimei`.
Esto se debe a que la segunda solicitud sobrescribe la variable estática `$name`, y cuando la primera solicitud finalice el sueño, la variable estática `$name` ya habrá cambiado a `hanmeimei`.

**La forma correcta de hacerlo es utilizar context para almacenar datos de estado de la solicitud**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Las variables locales no causarán contaminación de datos**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Dado que `$name` es una variable local, las corrutinas no pueden acceder a las variables locales unas de otras, por lo que es seguro en términos de corrutinas.

# Sobre las corrutinas
Las corrutinas no son una bala de plata. La introducción de las corrutinas significa que es necesario prestar atención a la contaminación de las variables globales/estáticas y establecer el contexto. Además, depurar errores en el entorno de las corrutinas es un poco más complejo que la programación bloqueante.

En realidad, la programación bloqueante de webman es lo suficientemente rápida. Según los datos de los últimos tres años de la competencia en [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), el rendimiento de la programación bloqueante de webman en tareas de base de datos es casi el doble que el de los frameworks web de Go como gin y echo, y es casi 40 veces mayor que el del framework tradicional laravel.
![](../../assets/img/benchemarks-go-sw.png?)

Cuando la base de datos, Redis, etc. están en la red interna, el rendimiento de la programación no bloqueante de múltiples procesos a menudo es mayor que el de las corrutinas, ya que el consumo de crear, programar y eliminar corrutinas puede ser mayor que el de cambiar de un proceso a otro. Por lo tanto, la introducción de las corrutinas en este caso no mejorará significativamente el rendimiento.

# Cuándo usar corrutinas
Cuando hay accesos lentos en el negocio, como cuando el negocio necesita acceder a una interfaz de terceros, se pueden utilizar llamadas HTTP asincrónicas con [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) para mejorar la capacidad de concurrencia de la aplicación.
