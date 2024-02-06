# Controlador

Conforme a especificação PSR4, o namespace da classe do controlador começa com `plugin\{identificação-do-plugin}`, por exemplo

Crie o arquivo do controlador `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('olá índice');
    }
    
    public function hello(Request $request)
    {
        return response('olá webman');
    }
}
```

Quando acessar `http://127.0.0.1:8787/app/foo/foo`, a página retornará `olá índice`

Quando acessar `http://127.0.0.1:8787/app/foo/foo/hello`, a página retornará `olá webman`

## Acesso via URL
O caminho do endereço URL do plugin começa com `/app`, seguido pela identificação do plugin e, em seguida, o controlador e o método específicos.
Por exemplo, o endereço URL do `UserController` do plugin `plugin\foo\app\controller` é `http://127.0.0.1:8787/app/foo/user`
