# Ciclo de vida

## Ciclo de vida del proceso
- Cada proceso tiene un ciclo de vida largo.
- Cada proceso se ejecuta de forma independiente y sin interferencias.
- Cada proceso puede manejar múltiples solicitudes durante su ciclo de vida.
- Cuando el proceso recibe comandos `stop`, `reload` o `restart`, terminará y finalizará su ciclo de vida actual.

> **Nota**
> Cada proceso es independiente y sin interferencias, lo que significa que cada proceso mantiene sus propios recursos, variables e instancias de clases. Esto se refleja en que cada proceso tiene su propia conexión a la base de datos y algunas instancias únicas se inicializan en cada proceso, lo que resulta en múltiples inicializaciones en varios procesos.

## Ciclo de vida de la solicitud
- Cada solicitud genera un objeto `$request`.
- El objeto `$request` se reciclará después de que se haya procesado la solicitud.

## Ciclo de vida del controlador
- Cada controlador se instancia solo una vez por proceso, pero se instancia múltiples veces en varios procesos (a excepción de la reutilización de controladores, consulte [Ciclo de vida del controlador](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- La instancia del controlador es compartida por múltiples solicitudes dentro del mismo proceso (excepto la reutilización del controlador).
- El ciclo de vida del controlador termina cuando el proceso sale (excepto la reutilización del controlador).

## Acerca del ciclo de vida de las variables
webman se basa en PHP, por lo que sigue por completo el mecanismo de recolección de basura de PHP. Las variables temporales generadas en la lógica empresarial, incluidas las instancias de clases creadas con la palabra clave `new`, se reciclan automáticamente al finalizar una función o método, sin necesidad de liberarlas manualmente con `unset`. Esto significa que el desarrollo con webman es prácticamente igual a trabajar con marcos tradicionales. Por ejemplo, la instancia `$foo` en el siguiente ejemplo se liberará automáticamente al finalizar el método `index`:

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Suponiendo que haya una clase Foo aquí
        return response($foo->sayHello());
    }
}
```
Si deseas reutilizar la instancia de una clase, puedes guardarla en una propiedad estática de la clase o en una propiedad de un objeto de ciclo de vida largo, como un controlador, o utilizar el método `get` del contenedor para inicializar la instancia de la clase. Por ejemplo:

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```

El método `Container::get()` se utiliza para crear y guardar la instancia de la clase. Cuando se llama nuevamente con los mismos parámetros, se devuelve la instancia creada previamente.

> **Nota**
> `Container::get()` solo puede inicializar instancias sin parámetros de constructor. `Container::make()` se puede usar para crear instancias con parámetros de constructor, pero a diferencia de `Container::get()`, `Container::make()` no reutilizará la instancia, es decir, incluso con los mismos parámetros, `Container::make()` siempre devolverá una nueva instancia.

# Acerca de las fugas de memoria
En la gran mayoría de los casos, nuestro código empresarial no sufrirá fugas de memoria (pocos usuarios han informado de este problema). Solo necesitamos prestar un poco de atención a que los datos en arrays de largo ciclo de vida no se expandan indefinidamente. Observa el siguiente código:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Propiedad de array
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('Hola index');
    }

    public function hello(Request $request)
    {
        return response('Hola webman');
    }
}
```
Por defecto, el controlador tiene un ciclo de vida largo (excepto la reutilización del controlador), por lo tanto, la propiedad de array `$data` del controlador también tiene un ciclo de vida largo. Con cada solicitud a `foo/index`, la cantidad de elementos en `$data` aumenta constantemente, lo que puede provocar fugas de memoria.

Para obtener más información, consulta [Fugas de memoria](./memory-leak.md)
