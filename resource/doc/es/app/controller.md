# Controlador

Según la especificación PSR4, el espacio de nombres de la clase del controlador debe comenzar con `plugin\{identificador_del_plugin}`, por ejemplo:

Crear el archivo del controlador `plugin/foo/app/controller/FooController.php`.

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
La ruta de la dirección URL de la aplicación del complemento comienza con `/app`, seguido del identificador del complemento, y luego el controlador y método específico.
Por ejemplo, la dirección URL del controlador `plugin\foo\app\controller\UserController` es `http://127.0.0.1:8787/app/foo/user`.
