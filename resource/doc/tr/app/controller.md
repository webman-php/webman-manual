# Kontrolcü

PSR4 standartlarına göre, kontrolcü sınıfının ad alanı `{plugin-identifier}` ile başlar, örneğin

`plugin/foo/app/controller/FooController.php` adında yeni bir kontrolcü dosyası oluşturun.

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

`http://127.0.0.1:8787/app/foo/foo` adresine erişildiğinde, sayfa `hello index` döner

`http://127.0.0.1:8787/app/foo/foo/hello` adresine erişildiğinde, sayfa `hello webman` döner


## URL Erişimi
Uygulama eklentilerinin URL adres yolları her zaman `/app` ile başlar, ardından eklenti tanımlayıcısı gelir, sonra da belirli kontrolcü ve yöntem gelir.
Örneğin `plugin\foo\app\controller\UserController` url adresi `http://127.0.0.1:8787/app/foo/user` şeklindedir.
