# Denetleyici

PSR4 standartlarına göre, denetleyici sınıfı isim alanı `plugin\{eklenti tanımlayıcısı}` ile başlar, örneğin

`plugin/foo/app/controller/FooController.php` adında yeni bir denetleyici dosyası oluşturun.

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

`http://127.0.0.1:8787/app/foo/foo` adresine erişildiğinde, sayfa `hello index` döndürür.

`http://127.0.0.1:8787/app/foo/foo/hello` adresine erişildiğinde, sayfa `hello webman` döndürür.


## URL Erişimi
Uygulama eklentisi URL adresleri `/app` ile başlar, ardından eklenti tanımlayıcısı gelir, ardından da belirli denetleyici ve metot gelir.
Örneğin `plugin\foo\app\controller\UserController` URL adresi `http://127.0.0.1:8787/app/foo/user` şeklindedir.
