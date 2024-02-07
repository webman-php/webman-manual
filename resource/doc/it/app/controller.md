# Controller

Conformemente alle specifiche PSR4, lo spazio dei nomi della classe del controller inizia con `plugin\{identificativo_plugin}`, ad esempio

Creare un nuovo file controller `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('ciao index');
    }

    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```

Quando si visita `http://127.0.0.1:8787/app/foo/foo`, la pagina restituirà `ciao index`

Quando si visita `http://127.0.0.1:8787/app/foo/foo/hello`, la pagina restituirà `ciao webman`

## Accesso tramite URL
Il percorso dell'URL dell'applicazione del plugin inizia sempre con `/app`, seguito dall'identificativo del plugin e poi il controller e il metodo specifico.
Ad esempio, l'indirizzo URL di `plugin\foo\app\controller\UserController` è `http://127.0.0.1:8787/app/foo/user`
