# Ciclo de vida

## Ciclo de vida del proceso
- Cada proceso tiene un ciclo de vida largo.
- Cada proceso se ejecuta de manera independiente y sin interferencias.
- Cada proceso puede manejar múltiples solicitudes durante su ciclo de vida.
- Cuando un proceso recibe comandos de "stop", "reload" o "restart", ejecutará una salida, terminando el ciclo de vida actual.

> **Nota**
> Cada proceso es independiente y sin interferencias, lo que significa que cada proceso mantiene sus propios recursos, variables y instancias de clases, lo que se manifiesta en cada proceso teniendo su propia conexión a base de datos. Algunos singletons se inicializan en cada proceso, lo que resulta en múltiples inicializaciones en varios procesos.

## Ciclo de vida de la solicitud
- Cada solicitud generará un objeto `$request`.
- El objeto `$request` será recolectado después de que se complete el manejo de la solicitud.

## Ciclo de vida del controlador
- Cada controlador solo se instanciará una vez por cada proceso, pero se instanciará varias veces en múltiples procesos (excepto cuando se desactiva la reutilización del controlador, consulte [Ciclo de vida del controlador](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- La instancia del controlador será compartida entre múltiples solicitudes dentro del mismo proceso (excepto cuando se desactiva la reutilización del controlador).
- El ciclo de vida del controlador terminará cuando el proceso se cierre (excepto cuando se desactiva la reutilización del controlador).

## Sobre el ciclo de vida de las variables
Webman está desarrollado en PHP, por lo que sigue completamente el mecanismo de recolección de variables de PHP. Las variables temporales generadas en la lógica empresarial, incluidas las instancias de clases creadas con la palabra clave "new", se reciclan automáticamente al finalizar una función o método, sin necesidad de usar `unset` manualmente. Esto significa que el desarrollo en Webman es casi idéntico a desarrollar en un marco tradicional. Por ejemplo, la instancia `$foo` en el siguiente ejemplo se liberará automáticamente una vez que se complete el método index:

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Suponiendo que aquí existe una clase Foo
        return response($foo->sayHello());
    }
}
```
Si deseas reutilizar una instancia de una clase, puedes guardar la clase en una propiedad estática de la clase o en una propiedad de un objeto de larga duración, como un controlador. También puedes utilizar el método `get` del contenedor para inicializar la instancia de la clase, por ejemplo:

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
El método `Container::get()` se utiliza para crear y guardar instancias de clases. La próxima vez que se llame con los mismos parámetros, devolverá la instancia de clase creada previamente.

> **Nota**
> `Container::get()` solo puede inicializar instancias sin parámetros de constructor. `Container::make()` puede crear instancias con parámetros de constructor, pero a diferencia de `Container::get()`, `Container::make()` no reutilizará la instancia, es decir, incluso si se llama con los mismos parámetros, `Container::make()` siempre devolverá una nueva instancia.

# Sobre las fugas de memoria
En la gran mayoría de los casos, nuestro código empresarial no sufrirá fugas de memoria (muy raramente los usuarios informan sobre fugas de memoria). Solo necesitamos prestar un poco de atención para evitar la expansión infinita de datos de largo ciclo de vida. Por ejemplo:

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
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
El controlador es, por defecto, de largo ciclo de vida (a menos que se desactive la reutilización del controlador); del mismo modo, la propiedad de array `$data` del controlador también tiene un largo ciclo de vida. Con el continuo crecimiento de elementos en la solicitud `foo/index`, el array `$data` consumirá cada vez más memoria, lo que llevará a una fuga de memoria.

Para obtener más información relacionada, consulta [Fugas de memoria](./memory-leak.md).
