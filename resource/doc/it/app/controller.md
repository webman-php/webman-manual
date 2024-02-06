# Controller

Conformemente alla specifica PSR4, lo spazio dei nomi della classe del controller inizia con `plugin\{identificativo_plugin}`, per esempio

Crea un nuovo file controller `plugin/foo/app/controller/FooController.php`.

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

Quando si accede a `http://127.0.0.1:8787/app/foo/foo`, la pagina restituirà `ciao index`

Quando si accede a `http://127.0.0.1:8787/app/foo/foo/hello`, la pagina restituirà `ciao webman`


## Accesso URL
I percorsi dell'indirizzo URL dell'applicazione del plugin iniziano con `/app`, seguiti dall'identificativo del plugin e quindi dal controller e dal metodo specifici.
Per esempio, l'indirizzo URL del `plugin\foo\app\controller\UserController` è `http://127.0.0.1:8787/app/foo/user`
