# Yanıt

Yanıt aslında bir `support\Response` nesnesidir ve bu nesneyi oluşturmayı kolaylaştırmak için webman bazı yardımcı fonksiyonlar sağlar.

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
        return response('merhaba webman');
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

Ayrıca, boş bir `response` nesnesi oluşturabilir ve ardından uygun konumda `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` kullanarak dönüş içeriğini ayarlayabilirsiniz.
```php
public function hello(Request $request)
{
    // Bir nesne oluştur
    $response = response();
    
    // .... İş mantığı eksik
    
    // Çerez ayarla
    $response->cookie('foo', 'değer');
    
    // .... İş mantığı eksik
    
    // http başlığı ayarla
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Başlık Değeri 1',
                'X-Header-Tow' => 'Başlık Değeri 2',
            ]);

    // .... İş mantığı eksik
    
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
        return json(['code' => 0, 'msg' => 'tamam']);
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
Aşağıdaki gibi yeni bir dosya oluşturun `app/controller/FooController.php`

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
Aşağıdaki gibi yeni bir dosya oluşturun `app/view/foo/hello.html`

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
        return redirect('/kullanıcı');
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

## header ayarı
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('merhaba webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Başlık Değeri'
        ]);
    }
}
```
Ayrıca `header` ve `withHeaders` yöntemlerini kullanarak tek tek veya topluca başlık ayarlayabilirsiniz.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('merhaba webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Başlık Değeri 1',
            'X-Header-Tow' => 'Başlık Değeri 2',
        ]);
    }
}
```
Başlıkları önceden ayarlayabilir ve sonda döndürülecek veriyi ayarlayabilirsiniz.
```php
public function hello(Request $request)
{
    // Bir nesne oluştur
    $response = response();
    
    // .... İş mantığı eksik
  
    // http başlığı ayarla
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Başlık Değeri 1',
                'X-Header-Tow' => 'Başlık Değeri 2',
            ]);

    // .... İş mantığı eksik
  
    // Döndürülecek veriyi ayarla
    $response->withBody('Döndürülen veri');
    return $response;
}
```

## Çerez ayarı

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('merhaba webman')
        ->cookie('foo', 'değer');
    }
}
```
Çerezi önceden ayarlayabilir ve sonda döndürülecek veriyi ayarlayabilirsiniz.
```php
public function hello(Request $request)
{
    // Bir nesne oluştur
    $response = response();
    
    // .... İş mantığı eksik
    
    // Çerez ayarla
    $response->cookie('foo', 'değer');
    
    // .... İş mantığı eksik

    // Döndürülecek veriyi ayarla
    $response->withBody('Döndürülen veri');
    return $response;
}
```
Cookie yöntemi tam parametreleri aşağıdaki gibidir:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`
## Dosya akışı dönüşü
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

- Webman, çok büyük dosyaların gönderilmesini destekler.
- Büyük dosyalar için (2M'den büyük), webman tüm dosyayı bir seferde hafızaya almaz, uygun zamanlarda dosyayı parçalara bölerek gönderir.
- Webman, istemci tarafından alınan hızı göz önünde bulundurarak dosya okuma ve gönderme hızını optimize eder, dosyayı en hızlı şekilde gönderirken hafıza kullanımını en aza indirir.
- Veri gönderimi engellenmediğinden, diğer istek işlemlerini etkilemez.
- file yöntemi, otomatik olarak `if-modified-since` başlığını ekler ve bir sonraki istekte `if-modified-since` başlığını kontrol eder, dosya değiştirilmediyse bant genişliği tasarrufu sağlamak için doğrudan 304'ü döndürür.
- Gönderilen dosya, tarayıcıya otomatik olarak uygun `Content-Type` başlığı kullanılarak gönderilir.
- Dosya bulunamazsa, otomatik olarak 404 yanıtına dönüşecektir.


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
download yöntemi, file yöntemiyle temelde aynıdır, farklılıklar şunlardır:
1. İndirilecek dosya adı ayarlandıktan sonra dosya indirme olarak gerçekleştirilir, tarayıcıda gösterilmez.
2. download yöntemi `if-modified-since` başlığını kontrol etmez.


## Çıktı Alımı
Bazı kütüphaneler dosya içeriğini doğrudan standart çıktıya yazdırır, yani veriler tarayıcıya gönderilmez, bu durumda verileri bir değişkene yakalamak için `ob_start();` `ob_get_clean();` kullanarak verileri tarayıcıya gönderilmeden önce yakalamamız gerekir, örneğin:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Resim oluşturma
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'Basit bir Metin Dizesi', $text_color);

        // Çıktı alımını başlat
        ob_start();
        // Resmi çıktıla
        imagejpeg($im);
        // Resim içeriğini elde et
        $image = ob_get_clean();
        
        // Resmi gönder
        return response($image)->header('Content-Type', 'resim/jpeg');
    }
}
```
