# Controlador

Cree un nuevo archivo de controlador `app/controller/FooController.php`.

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

Cuando se accede a `http://127.0.0.1:8787/foo`, la página devuelve `hello index`.

Cuando se accede a `http://127.0.0.1:8787/foo/hello`, la página devuelve `hello webman`.

Por supuesto, puedes cambiar las reglas de enrutamiento a través de la configuración de rutas, consulte [Rutas](route.md).

> **Consejo**
> Si aparece un error 404, abra `config/app.php`, establezca `controller_suffix` en `Controller` y reinicie.

## Sufijo del Controlador
A partir de la versión 1.3, webman admite configurar un sufijo para el controlador en `config/app.php`. Si `controller_suffix` en `config/app.php` se establece como una cadena vacía `''`, entonces el controlador se verá de la siguiente manera

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

Se recomienda encarecidamente configurar el sufijo del controlador como `Controller` para evitar conflictos de nombres entre controladores y modelos, aumentando así la seguridad.

## Notas
 - El framework automáticamente pasa un objeto `support\Request` al controlador, a través del cual se puede obtener los datos de entrada del usuario (get, post, header, cookie, etc.), consulte [Solicitud](request.md)
 - Dentro del controlador, se pueden devolver números, cadenas o un objeto `support\Response`, pero no se pueden devolver otros tipos de datos.
 - El objeto `support\Response` puede crearse mediante las funciones de ayuda `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, etc.
 

## Ciclo de Vida del Controlador

Cuando `controller_reuse` en `config/app.php` es `false`, se inicializará una instancia del controlador para cada solicitud, la instancia del controlador se destruirá al finalizar la solicitud, similar al funcionamiento de los frameworks tradicionales.

Cuando `controller_reuse` en `config/app.php` es `true`, todas las solicitudes reutilizarán la instancia del controlador, es decir, una vez creada la instancia del controlador residirá en memoria para todas las solicitudes.

> **Nota**
> Para desactivar la reutilización del controlador, webman>=1.4.0 se requiere, es decir, antes de la versión 1.4.0, el controlador se reutiliza para todas las solicitudes y no se puede cambiar.

> **Nota**
> Al habilitar la reutilización del controlador, las solicitudes no deben modificar ninguna propiedad del controlador, ya que estos cambios afectarán las solicitudes posteriores, por ejemplo:

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
        // Este método mantendrá el modelo después de la primera solicitud update?id=1
        // Si se realiza otra solicitud delete?id=2, se eliminará el modelo 1
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Consejo**
> Devolver datos en el constructor `__construct()` del controlador no tendrá ningún efecto, por ejemplo:

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
Cada solicitud creará una nueva instancia del controlador, la instancia del controlador se liberará al finalizar la solicitud y se liberará la memoria. No reutilizar el controlador es similar a la mayoría de las prácticas de desarrollo. Debido a la creación y destrucción repetida del controlador, el rendimiento será ligeramente inferior al de la reutilización del controlador (el rendimiento de la prueba de estrés helloworld es aproximadamente un 10% peor, con aplicación de negocio, esto puede ser ignorado en su mayoría).

#### Reutilización del Controlador
Si se reutiliza el controlador, un proceso solo creará una instancia del controlador, la instancia del controlador no se liberará al finalizar la solicitud, y las solicitudes posteriores en el mismo proceso reutilizarán esta instancia. La reutilización del controlador mejora la rendimiento, pero no es una práctica común entre la mayoría de los desarrolladores.

#### Casos en los que no se debe usar la Reutilización del Controlador
Cuando las solicitudes modifiquen las propiedades del controlador, no se debe habilitar la reutilización del controlador, ya que estos cambios en las propiedades afectarán a las solicitudes posteriores.

A algunos desarrolladores les gusta realizar algunas inicializaciones para cada solicitud en el constructor del controlador `__construct()`, en este caso, no se debe reutilizar el controlador, ya que el constructor del proceso solo se llamará una vez y no se llamará para cada solicitud.
