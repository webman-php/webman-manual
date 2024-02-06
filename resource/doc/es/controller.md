# Controlador

Crea un archivo de controlador nuevo `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Cuando accedes a `http://127.0.0.1:8787/foo`, la página devuelve `hello index`.

Cuando accedes a `http://127.0.0.1:8787/foo/hello`, la página devuelve `hello webman`.

Por supuesto, puedes cambiar las reglas de enrutamiento mediante la configuración de rutas, consulta [Rutas](route.md).

> **Consejo**
> Si experimentas un error 404 al intentar acceder, abre `config/app.php`, establece `controller_suffix` como `Controller` y reinicia.

## Sufijo del Controlador
A partir de la versión 1.3 de webman, puedes configurar el sufijo del controlador en `config/app.php`. Si el `controller_suffix` en `config/app.php` se establece como una cadena vacía `''`, los controladores se parecerán a lo siguiente:

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Se recomienda encarecidamente establecer el sufijo del controlador como `Controller`, de esta manera puedes evitar conflictos de nombres entre el controlador y la clase del modelo, a la vez que aumentas la seguridad.

## Nota
 - El framework automáticamente pasa un objeto `support\Request` al controlador, a través del cual puedes obtener datos de entrada del usuario (datos `get`, `post`, `header`, `cookie`, etc.). Consulta [Solicitud](request.md).
 - En el controlador puedes devolver números, cadenas o un objeto `support\Response`, pero no puedes devolver ningún otro tipo de datos.
 - El objeto `support\Response` se puede crear a través de funciones auxiliares como `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, entre otras.

## Ciclo de Vida del Controlador

Cuando `config/app.php` tiene `controller_reuse` establecido en `false`, se inicializa una instancia del controlador correspondiente en cada solicitud, y al finalizar la solicitud se destruye la instancia del controlador, siguiendo el mecanismo de ejecución de los frameworks tradicionales.

Cuando `config/app.php` tiene `controller_reuse` establecido en `true`, todas las solicitudes reutilizan la instancia del controlador, es decir, una vez que se crea la instancia del controlador, permanece en la memoria y se reutiliza para todas las solicitudes.

> **Nota**
> Para desactivar la reutilización del controlador se necesita webman>=1.4.0, es decir, antes de la versión 1.4.0, el controlador se reutilizaba para todas las solicitudes y no se podía cambiar.

> **Nota**
> Al habilitar la reutilización del controlador, las solicitudes no deben modificar las propiedades del controlador, ya que estos cambios afectarán las solicitudes posteriores, por ejemplo:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Este método conservará el modelo después de la primera solicitud de update?id=1
        // Si se solicita delete?id=2, se eliminará el dato 1
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Consejo**
> En el método constructor `__construct()` del controlador, devolver datos no tendrá ningún efecto, por ejemplo:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Devolver datos en el constructor no tendrá ningún efecto, el navegador no recibirá esta respuesta
        return response('hello'); 
    }
}
```

## Diferencias entre Reutilización y No Reutilización del Controlador
Las diferencias son las siguientes:

#### No Reutilización del Controlador
Se crea una nueva instancia del controlador en cada solicitud, al finalizar la solicitud se libera esa instancia y se recupera memoria. No reutilizar el controlador es similar a la ejecución de frameworks tradicionales y coincide con la mayoría de las prácticas de desarrollo. Debido a la creación y destrucción repetidas del controlador, su rendimiento será ligeramente inferior a la reutilización del controlador (la diferencia de rendimiento es de aproximadamente un 10% en pruebas de rendimiento de helloworld, aunque con aplicaciones reales puede ser prácticamente despreciable).

#### Reutilización del Controlador
Si se reutiliza el controlador, se crea una instancia del controlador una vez por proceso, al finalizar la solicitud no se libera esa instancia del controlador, y las solicitudes posteriores en el mismo proceso reutilizan esa instancia. La reutilización del controlador ofrece un mejor rendimiento, pero no coincide con la mayoría de las prácticas de desarrollo.

#### Casos en los que no es posible usar la reutilización del controlador
Cuando las solicitudes pueden cambiar las propiedades del controlador, no se puede habilitar la reutilización del controlador, ya que estos cambios en las propiedades afectarán a solicitudes posteriores.

A algunos desarrolladores les gusta realizar inicializaciones para cada solicitud en el método constructor `__construct()` del controlador, en estos casos no se puede reutilizar el controlador, ya que el método constructor solo se llama una vez por proceso y no para cada solicitud.
