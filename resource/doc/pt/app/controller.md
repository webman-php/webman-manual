# Controlador

De acordo com a especificação PSR4, o namespace da classe do controlador começa com `plugin\{identificação do plugin}`, por exemplo

Crie um novo arquivo de controlador `plugin/foo/app/controller/FooController.php`.

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

Quando acessar `http://127.0.0.1:8787/app/foo/foo`, a página retornará `hello index`

Quando acessar `http://127.0.0.1:8787/app/foo/foo/hello`, a página retornará `hello webman`

## Acesso via URL
Os caminhos de endereço URL dos plugins de aplicativos começam com `/app`, seguidos pela identificação do plugin e, em seguida, pelo controlador e método específicos.
Por exemplo, o endereço URL do `plugin\foo\app\controller\UserController` é `http://127.0.0.1:8787/app/foo/user`
