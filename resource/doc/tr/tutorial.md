# Basit Örnek

## Dize döndürme
**Controller Oluşturma**

Aşağıdaki gibi `app/controller/UserController.php` dosyasını oluşturun:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Get isteğinden ad parametresini al, eğer ad parametresi yoksa $default_name döndür
        $name = $request->get('name', $default_name);
        // Tarayıcıya dize döndür
        return response('hello ' . $name);
    }
}
```

**Erişim**

Tarayıcıda `http://127.0.0.1:8787/user/hello?name=tom` adresine gidin

Tarayıcı `hello tom` döndürecektir.

## JSON döndürme
`app/controller/UserController.php` dosyasını aşağıdaki gibi değiştirin:

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

Tarayıcı `{"code":0,"msg":"ok","data":"tom"}` döndürecektir.

Veri döndürmek için json yardımcı fonksiyonu otomatik olarak `Content-Type: application/json` başlığını ekler.

## XML döndürme
Benzer şekilde, `xml($xml)` yardımcı fonksiyonu, `text/xml` başlıklı bir `xml` yanıtı döndürecektir.

Burada `$xml` parametresi bir `xml` dizesi veya `SimpleXMLElement` nesnesi olabilir.

## JSONP döndürme
Benzer şekilde, `jsonp($data, $callback_name = 'callback')` yardımcı fonksiyonu bir `jsonp` yanıtı döndürecektir.

## Görünüm döndürme
`app/controller/UserController.php` dosyasını aşağıdaki gibi değiştirin:

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

Aşağıdaki gibi `app/view/user/hello.html` dosyasını oluşturun:

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

Tarayıcıda `http://127.0.0.1:8787/user/hello?name=tom` adresine gidin
`hello tom` içeriğini döndüren bir html sayfa alacaksınız.

Not: webman, şablon olarak varsayılan olarak PHP'nin orijinal sözdizimini kullanır. Başka görünümler kullanmak istiyorsanız [View](view.md) bölümüne bakınız.
