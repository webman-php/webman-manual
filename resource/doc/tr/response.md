# Yanıt

Yanıt aslında bir `support\Response` nesnesidir ve bu nesneyi kolayca oluşturabilmek için webman bazı yardımcı fonksiyonlar sağlar.

## Herhangi bir yanıt döndürme

**Örnek**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

response fonksiyonu aşağıdaki gibi uygulanmıştır:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Ayrıca, boş bir `response` nesnesi oluşturup, uygun bir konumda `$response->cookie()`, `$response->header()`, `$response->withHeaders()` ve `$response->withBody()` kullanarak içeriği ayarlayabilirsiniz.
```php
public function hello(Request $request)
{
    // Bir nesne oluştur
    $response = response();
    
    // .... İş mantığı atlanmış

    // Çerez ayarla
    $response->cookie('foo', 'değer');
    
    // .... İş mantığı atlanmış

    // HTTP başlığını ayarla
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Başlık Değeri 1',
                'X-Header-Two' => 'Başlık Değeri 2',
            ]);

    // .... İş mantığı atlanmış

    // Döndürülecek veriyi ayarla
    $response->withBody('Döndürülen veri');
    return $response;
}
```

## JSON döndürme
**Örnek**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
json fonksiyonu aşağıdaki gibi uygulanmıştır
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## XML döndürme
**Örnek**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
xml fonksiyonu aşağıdaki gibi uygulanmıştır:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Görünüm döndürme
Aşağıdaki gibi bir dosya oluşturun: `app/controller/FooController.php`
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Aşağıdaki gibi bir dosya oluşturun: `app/view/foo/hello.html`
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

## Yönlendirme
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
redirect fonksiyonu aşağıdaki gibi uygulanmıştır:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Başlık ayarlama
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Başlık Değeri' 
        ]);
    }
}
```
`header` ve `withHeaders` yöntemlerini kullanarak tek tek veya toplu başlık ayarlayabilirsiniz:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Başlık Değeri 1',
            'X-Header-Two' => 'Başlık Değeri 2',
        ]);
    }
}
```
Başlıkları önceden ayarlayabilir ve nihayetinde döndürülecek veriyi ayarlayabilirsiniz.
```php
public function hello(Request $request)
{
    // Bir nesne oluştur
    $response = response();
    
    // .... İş mantığı atlanmış
   
    // HTTP başlığını ayarla
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Başlık Değeri 1',
                'X-Header-Two' => 'Başlık Değeri 2',
            ]);

    // .... İş mantığı atlanmış

    // Döndürülecek veriyi ayarla
    $response->withBody('Döndürülen veri');
    return $response;
}
```

## Çerez ayarlama
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'değer');
    }
}
```
Çerezleri önceden ayarlayabilir ve nihayetinde döndürülecek veriyi ayarlayabilirsiniz.
```php
public function hello(Request $request)
{
    // Bir nesne oluştur
    $response = response();
    
    // .... İş mantığı atlanmış
    
    // Çerez ayarla
    $response->cookie('foo', 'değer');
    
    // .... İş mantığı atlanmış

    // Döndürülecek veriyi ayarla
    $response->withBody('Döndürülen veri');
    return $response;
}
```
`cookie` yönteminin tam parametreleri aşağıdaki gibidir:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Dosya akışı döndürme
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman, çok büyük dosyaları da gönderebilir
- Büyük dosyalar için (2 Mb üzeri), webman tüm dosyayı bir kerede belleğe yüklemez, uygun zamanda dosyayı segmente ayırarak okur ve gönderir
- webman, istemci tarafından alınan hızı dikkate alarak dosya okuma ve gönderme hızını optimize eder, böylece dosyanın en hızlı şekilde gönderilmesini ve bellek kullanımının en aza indirilmesini sağlar
- Veri gönderimi engellenmez, diğer istekleri etkilemez
- file yöntemi, otomatik olarak `if-modified-since` başlığını ekler ve bir sonraki isteğinde `if-modified-since` başlığını kontrol eder, dosya değiştirilmediyse bant genişliği tasarrufu sağlamak için doğrudan 304 yanıtı döner
- Gönderilen dosya, tarayıcıya otomatik olarak uygun `Content-Type` başlığı kullanılarak gönderilir
- Dosya mevcut değilse, otomatik olarak 404 yanıtı verilir

## Dosya İndirme
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'dosya_adi.ico');
    }
}
```
download yöntemi, file yöntemiyle temelde aynı işlevi görür. Farklılıklar şunlardır:
1) İndirilen dosya adı ayarlandıktan sonra dosya indirilir, tarayıcıda görüntülenmez
2) download yöntemi, `if-modified-since` başlığını kontrol etmez

## Çıktı Alma
Bazı kütüphaneler dosya içeriğini doğrudan standart çıktıya yazabilir, yani veri tarayıcıya gönderilmez, bu durumda veriyi bir değişkene yakalamak ve sonra tarayıcıya göndermek için `ob_start();` `ob_get_clean();` kullanmamız gerekebilir, örneğin:

```php
<?php
namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Resim oluştur
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'Basit Bir Metin Dizesi', $text_color);

        // Çıktıyı almayı başlat
        ob_start();
        // Resmi çıktıla
        imagejpeg($im);
        // Resim içeriğini al
        $image = ob_get_clean();
        
        // Resmi gönder
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
