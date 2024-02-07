# Corutinas

>**Requisitos de las corutinas**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Comando de actualización de webman `composer require workerman/webman-framework ^1.5.0`
> Comando de actualización de workerman `composer require workerman/workerman ^5.0.0`
> Se requiere la instalación de Fiber corutina `composer require revolt/event-loop ^1.0.0`

# Ejemplo
### Respuesta con retraso

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
`Timer::sleep()` es similar a la función `sleep()` nativa de PHP, la diferencia es que `Timer::sleep()` no bloqueará el proceso.


### Hacer una petición HTTP

> **Nota**
> Se requiere la instalación de composer require workerman/http-client ^2.0.0

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
        $response = $client->get('http://example.com'); // Hacer una solicitud asíncrona de forma síncrona
        return $response->getBody()->getContents();
    }
}
```
De la misma manera, la solicitud `$client->get('http://example.com')` es no bloqueante, lo que se puede utilizar para hacer solicitudes HTTP no bloqueantes en webman y mejorar el rendimiento de la aplicación.

Para obtener más información, consulta [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html).

### Agregar la clase support\Context

La clase `support\Context` se utiliza para almacenar datos del contexto de la solicitud y, una vez que la solicitud se completa, los datos del contexto respectivos se eliminan automáticamente. Esto significa que el ciclo de vida de los datos del contexto coincide con el ciclo de vida de la solicitud. `support\Context` es compatible con el entorno de las corutinas Fiber, Swoole y Swow.

### Corutinas de Swoole
Después de instalar la extensión de Swoole (se requiere Swoole>=5.0), se puede habilitar las corutinas de Swoole mediante la configuración en config/server.php:
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
Para obtener más información, consulta [Event Loop de workerman](https://www.workerman.net/doc/workerman/appendices/event.html).

### Contaminación de variables globales
En el entorno de las corutinas, está prohibido almacenar información de estado **relacionada con la solicitud** en variables globales o estáticas, ya que esto podría provocar contaminación de variables globales, por ejemplo:

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
Si se configura el número de procesos como 1, al realizar dos solicitudes consecutivas:
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
Se espera que los resultados de las dos solicitudes sean respectivamente `lilei` y `hanmeimei`, pero en realidad ambos resultados son `hanmeimei`.
Esto se debe a que la segunda solicitud sobrescribe la variable estática `$name` y, cuando la primera solicitud finaliza el periodo de espera, la variable estática `$name` ya se ha convertido en `hanmeimei`.

**La forma correcta de hacerlo es utilizar el almacenamiento de datos de estado de la solicitud en el contexto**
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
Debido a que `$name` es una variable local, las corutinas no pueden acceder a las variables locales entre sí, por lo que el uso de variables locales es seguro en el entorno de las corutinas.

# Acerca de las corutinas
Las corutinas no son una solución mágica, su introducción significa que hay que prestar atención a la contaminación de variables globales o estáticas, y es necesario establecer el contexto.

Además, la depuración de errores en el entorno de las corutinas es más compleja que la programación bloqueante.

La programación bloqueante en webman es lo suficientemente rápida, según [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) los datos de las últimas tres rondas de evaluación durante los últimos tres años muestran que la programación bloqueante de webman con negocios de bases de datos tiene un rendimiento casi el doble de alto que los marcos web de Go, como gin y echo, y casi 40 veces más alto que el marco tradicional de Laravel.
![](../../assets/img/benchemarks-go-sw.png?)


Cuando la base de datos y Redis se encuentran en una red interna, el rendimiento de la programación bloqueante con múltiples procesos puede ser superior al de las corutinas. Esto se debe a que cuando la base de datos y Redis son lo suficientemente rápidos, el costo de crear, programar y destruir corutinas puede ser mayor que el de cambiar entre procesos, por lo que la introducción de corutinas no necesariamente mejora significativamente el rendimiento.

# Cuándo usar corutinas
Cuando hay accesos lentos en los negocios, como la necesidad de acceder a una interfaz de terceros, se pueden utilizar las llamadas HTTP asíncronas no bloqueantes con [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) para mejorar la capacidad de concurrencia de la aplicación.
