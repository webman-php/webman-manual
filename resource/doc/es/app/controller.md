# Controlador

Según la especificación PSR4, el espacio de nombres de la clase del controlador comienza con `plugin\{identificador del plugin}`, por ejemplo:

Crear un archivo de controlador `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

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

Cuando se accede a `http://127.0.0.1:8787/app/foo/foo`, la página devuelve `hello index`.

Cuando se accede a `http://127.0.0.1:8787/app/foo/foo/hello`, la página devuelve `hello webman`.


## Acceso a URL
La ruta de la dirección URL de la aplicación del complemento siempre comienza con `/app`, seguido del identificador del plugin y luego el controlador y el método específico.
Por ejemplo, la dirección URL para `plugin\foo\app\controller\UserController` es `http://127.0.0.1:8787/app/foo/user`.
