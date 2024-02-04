# Controller

Gemäß dem PSR4-Standard beginnt der Namespace einer Controller-Klasse mit `plugin\{Plugin-Kennung}`, zum Beispiel

Erstellen Sie die Controller-Datei `plugin/foo/app/controller/FooController.php`.

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

Wenn Sie `http://127.0.0.1:8787/app/foo/foo` aufrufen, wird die Seite `hello index` zurückgeben.

Wenn Sie `http://127.0.0.1:8787/app/foo/foo/hello` aufrufen, wird die Seite `hello webman` zurückgeben.


## URL-Zugriff
Die URL-Pfade für Plugin-Anwendungen beginnen alle mit `/app`, gefolgt von der Plugin-Kennung und dann dem spezifischen Controller und der Methode.
Zum Beispiel ist die URL-Adresse für `plugin\foo\app\controller\UserController` `http://127.0.0.1:8787/app/foo/user`.
