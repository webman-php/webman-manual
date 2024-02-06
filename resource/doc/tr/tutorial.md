# Basit Örnek

## Dize Döndürme
**Controller Oluşturma**

Aşağıdaki gibi `app/controller/UserController.php` dosyasını oluşturun

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get isteğinden name parametresini al, eğer name parametresi geçilmediyse $default_name döndür
        $name = $request->get('name', $default_name);
        // Tarayıcıya dize dönüşü
        return response('hello ' . $name);
    }
}
```

**Erişim**

Tarayıcıda `http://127.0.0.1:8787/user/hello?name=tom` adresine gidin

Tarayıcı `hello tom` döndürecektir

## Json Döndürme
`app/controller/UserController.php` dosyasını aşağıdaki gibi değiştirin

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Erişim**

Tarayıcıda `http://127.0.0.1:8787/user/hello?name=tom` adresine gidin

Tarayıcı `{"code":0,"msg":"ok","data":"tom""}` döndürecektir

Veri döndürmek için json yardımcı fonksiyonu otomatik olarak `Content-Type: application/json` başlığını ekleyecektir

## Xml Döndürme
Benzer şekilde, yardımcı fonksiyonu `xml($xml)` kullanarak `xml` yanıtı ile birlikte `Content-Type: text/xml` başlığını döndürecektir.

Burada `$xml` parametresi, `xml` dizesi veya `SimpleXMLElement` nesnesi olabilir

## Jsonp Döndürme
Benzer şekilde, `jsonp($data, $callback_name = 'callback')` yardımcı fonksiyonunu kullanarak `jsonp` yanıtı döndürebilirsiniz.

## Görünüm Döndürme
`app/controller/UserController.php` dosyasını aşağıdaki gibi değiştirin

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Aşağıdaki gibi `app/view/user/hello.html` dosyasını oluşturun

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

Tarayıcıda `http://127.0.0.1:8787/user/hello?name=tom` adresine giderek bir `hello tom` içerikli html sayfa alacaksınız.

Not: Webman varsayılan olarak şablon olarak php orijinal sözdizimini kullanır. Diğer görünümleri kullanmak istiyorsanız [görünüm](view.md) sayfasına bakınız.
