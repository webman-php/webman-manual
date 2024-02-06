# Contrôleur

Selon la spécification PSR4, l'espace de noms de la classe du contrôleur commence par `plugin\{identifiant_du_plugin}`, par exemple

Créez un nouveau fichier de contrôleur `plugin/foo/app/controller/FooController.php`.

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

Lorsque vous accédez à `http://127.0.0.1:8787/app/foo/foo`, la page renvoie `hello index`

Lorsque vous accédez à `http://127.0.0.1:8787/app/foo/foo/hello`, la page renvoie `hello webman`


## Accès à l'URL
Le chemin d'accès de l'URL des applications de plugin commence par `/app`, suivi de l'identifiant du plugin, puis du contrôleur et de la méthode spécifique.
Par exemple, l'adresse URL du contrôleur `plugin\foo\app\controller\UserController` est `http://127.0.0.1:8787/app/foo/user`
