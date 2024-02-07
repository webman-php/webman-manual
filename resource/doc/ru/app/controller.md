# Контроллер

Согласно спецификации PSR4, пространство имен класса контроллера начинается с `plugin\{идентификатор_плагина}`, например

Создайте файл контроллера `plugin/foo/app/controller/FooController.php`.

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

При обращении по адресу `http://127.0.0.1:8787/app/foo/foo` страница вернет `hello index`

При обращении по адресу `http://127.0.0.1:8787/app/foo/foo/hello` страница вернет `hello webman`


## Доступ по URL
Пути URL для приложения плагина начинаются с `/app`, за которым следует идентификатор плагина, а затем конкретный контроллер и метод.
Например, URL-адрес для `plugin\foo\app\controller\UserController` будет `http://127.0.0.1:8787/app/foo/user`
