# Belgeler

## İstek Nesnesini Almak

Webman, istek nesnesini önceden action yöntemine enjekte eder, örneğin

**Örnek**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get isteği içerisinde name parametresini al, eğer name parametresi belirtilmemişse $default_name döndür
        $name = $request->get('name', $default_name);
        // tarayıcıya dize gönder
        return response('hello ' . $name);
    }
}
```

`$request` nesnesi aracılığıyla herhangi bir istekle ilgili herhangi bir veri alabiliriz.

**Bazen diğer sınıflarda mevcut isteğin `$request` nesnesini almak istiyorsak, sadece `request()` yardımcı fonksiyonunu kullanmamız yeterli.**

## Get İsteği Parametrelerini Almak

**Tüm get dizisini almak**
```php
$request->get();
```
Get isteği parametreleri yoksa boş bir dizi döner.

**Get dizisinin bir değerini almak**
```php
$request->get('name');
```
Eğer get dizisinde bu değer yoksa null döner.

Ayrıca get yöntemine ikinci bir parametre olarak varsayılan bir değer iletebilirsiniz, eğer get dizisinde ilgili değeri bulamazsa varsayılan değeri döner. Örneğin:
```php
$request->get('name', 'tom');
```

## Post İsteği Parametrelerini Almak

**Tüm post dizisini almak**
```php
$request->post();
```
Post isteği parametreleri yoksa boş bir dizi döner.

**Post dizisinin bir değerini almak**
```php
$request->post('name');
```
Eğer post dizisinde bu değer yoksa null döner.

Get yöntemiyle aynı şekilde, post yöntemine ikinci bir parametre olarak varsayılan bir değer iletebilirsiniz, eğer post dizisinde ilgili değeri bulamazsa varsayılan değeri döner. Örneğin:
```php
$request->post('name', 'tom');
```

## Orijinal Post İsteği Gövdesini Almak
```php
$post = $request->rawBody();
```
Bu işlev, `php-fpm`deki `file_get_contents("php://input");` işlemine benzer. Http'nin orijinal istek gövdesini almak için çok kullanışlıdır, özellikle `application/x-www-form-urlencoded` biçiminde olmayan post isteği verileri alınırken. 

## Başlık (Header) Almak
**Tüm başlık dizisini almak**
```php
$request->header();
```
Eğer istekte başlık parametresi yoksa boş bir dizi döner. Tüm anahtarlar küçük harfle belirtilir.

**Belirli bir başlık dizisini almak**
```php
$request->header('host');
```
Eğer başlık dizisi bu anahtarı içermiyorsa null döner. Tüm anahtarlar küçük harfle belirtilir.

Get yöntemiyle aynı şekilde, başlık yöntemine ikinci bir parametre olarak varsayılan bir değer iletebilirsiniz, eğer başlık dizisinde ilgili anahtarı bulamazsa varsayılan değeri döner. Örneğin:
```php
$request->header('host', 'localhost');
```

## Çerez Almak
**Tüm çerez dizisini almak**
```php
$request->cookie();
```
Eğer istekte çerez parametresi yoksa boş bir dizi döner.

**Belirli bir çerez dizisini almak**
```php
$request->cookie('name');
```
Eğer çerez dizisi bu anahtarı içermiyorsa null döner.

Get yöntemiyle aynı şekilde, çerez yöntemine ikinci bir parametre olarak varsayılan bir değer iletebilirsiniz, eğer çerez dizisinde ilgili anahtarı bulamazsa varsayılan değeri döner. Örneğin:
```php
$request->cookie('name', 'tom');
```

## Tüm Girişi Almak
`post` ve `get`i kapsayan bir dizi.
```php
$request->all();
```

## Belirli Giriş Değerini Almak
`post` ve `get` verilerinden belirli bir değeri almak için.
```php
$request->input('name', $default_value);
```

## Kısmi Giriş verilerini Almak
`post` ve `get` verilerinden bazı verileri almak için.
```php
// username ve password'ü içeren bir dizi al, eğer karşılık gelen anahtar yoksa yoksay
$only = $request->only(['username', 'password']);
// avatar ve age hariç tüm girişi al
$except = $request->except(['avatar', 'age']);
```

## Yüklenen Dosyaları Almak
**Tüm yüklenen dosya dizisini almak**
```php
$request->file();
```

Form buna benzer:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`, aşağıdaki gibi bir format döndürür:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```

Bu, `webman\Http\UploadFile` örneği olan bir dizi olur. `webman\Http\UploadFile` sınıfı PHP'nin yerleşik olan [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) sınıfını genişletir ve bazı kullanışlı yöntemler sunar.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Dosya geçerli mi, true|false gibi
            var_export($spl_file->getUploadExtension()); // Yüklü dosya uzantısı, örneğin 'jpg'
            var_export($spl_file->getUploadMimeType()); // Yüklü dosya mime türü, örneğin 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Yükleme hatası kodunu al, örneğin UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Yüklü dosya adı, örneğin 'my-test.jpg'
            var_export($spl_file->getSize()); // Dosya boyutunu al, örneğin 13364, byte cinsinden
            var_export($spl_file->getPath()); // Yükleme dizinini al, örneğin '/tmp'
            var_export($spl_file->getRealPath()); // Geçici dosya yolunu al, örneğin `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Notlar:**

- Dosya yüklendikten sonra geçici bir dosya adı alınacaktır, `/tmp/workerman.upload.SRliMu` gibi
- Yüklü dosya boyutu [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) kısıtlamasına tabidir, varsayılan 10 MB'dir, bu varsayılan değeri değiştirmek için `config/server.php` dosyasında `max_package_size`yi değiştirebilirsiniz.
- İstek sona erdikten sonra geçici dosya otomatik olarak temizlenir
- Eğer istekte yüklü dosya yoksa, `$request->file()` boş bir dizi döndürür
- Yüklenen dosyalar, `move_uploaded_file()` yöntemini desteklemez, bunun yerine `$file->move()` yöntemini kullanın, aşağıdaki örneğe bakınız

### Belirli Yüklenen Dosyayı Almak
```php
$request->file('avatar');
```
Eğer dosya mevcutsa, ilgili dosyanın `webman\Http\UploadFile` örneğini döndürür, aksi takdirde null döner.

**Örnek**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## Host Bilgisini Almak
İsteğin host bilgisini almak.
```php
$request->host();
```
Eğer istek adresi standart olmayan 80 veya 443 portuna sahipse, host bilgisi port bilgisi içerebilir, örneğin`example.com:8080`. Eğer port bilgisi gerekmiyorsa, ilk parametre olarak `true` iletilebilir.

```php
$request->host(true);
```

## İstek Yöntemini Almak
```php
 $request->method();
```
Dönen değerlerden biri `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD` olabilir.

## İstek URI'sini Almak
```php
$request->uri();
```
Path ve queryString kısmını içeren isteğin uri'sini döndürür.

## İstek Yolunu Almak

```php
$request->path();
```
İsteğin path kısmını döndürür.

## İstek QueryString'ini Almak

```php
$request->queryString();
```
İsteğin queryString'ini döndürür.

## İstek URL'sini Almak
`url()` yöntemi, `Query` parametresi olmayan URL'yi döndürür.
```php
$request->url();
```
`//www.workerman.net/workerman-chat` gibi bir döner.

`fullUrl()` yöntemi, `Query` parametresi olan URL'yi döndürür.
```php
$request->fullUrl();
```
`//www.workerman.net/workerman-chat?type=download` gibi bir döner.

> **Dikkat**
> `url()` ve `fullUrl()` yöntemleri, protokol kısmını (http veya https) döndürmez.
> Çünkü tarayıcılarda `//example.com` gibi başında `//` olan URL'ler, otomatik olarak geçerli site protokolünü algılar ve http ya da https ile istekte bulunur.

Eğer nginx proxy kullanıyorsanız, `proxy_set_header X-Forwarded-Proto $scheme;`'i nginx yapılandırmasına ekleyin, [nginx proxy'ye bakın](others/nginx-proxy.md),
böylece `http` veya `https`'i tespit etmek için `$request->header('x-forwarded-proto');`'ü kullanabilirsiniz, örneğin:
```php
echo $request->header('x-forwarded-proto'); // http ya da https çıktı verir
```

## İstek HTTP Versiyonunu Almak

```php
$request->protocolVersion();
```
Dize olarak `1.1` veya `1.0` döndürür.

## İstek Oturum ID'sini Almak

```php
$request->sessionId();
```
Harfler ve rakamlardan oluşan bir dize döndürür.

## İstek İstemci IP'sini Almak
```php
$request->getRemoteIp();
```

## İstek İstemci Portunu Almak
```php
$request->getRemotePort();
```
## İstek Yapan Gerçek IP Adresini Almak
```php
$request->getRealIp($safe_mode=true);
```

Proje bir proxy (örneğin, nginx) kullanıyorsa, `$request->getRemoteIp()` kullanıldığında genellikle proxy sunucu IP'sini (`127.0.0.1` `192.168.x.x` gibi) ve istemci gerçek IP'sini alamazsınız. Bu durumda, istemci gerçek IP'sini almak için `$request->getRealIp()` kullanmayı deneyebilirsiniz.

`$request->getRealIp()`, gerçek IP'yi HTTP başlığının `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` alanlarından almaya çalışacaktır.

> HTTP başlıkları kolayca sahte olabileceğinden, bu yöntemle alınan istemci IP'si %100 güvenilir değildir, özellikle `$safe_mode` değeri false olduğunda. Bir proxy üzerinden istemci gerçek IP'sini elde etmenin daha güvenilir bir yolu, güvenli bir proxy sunucusunun IP adresinin bilinmesi ve gerçek IP'nin hangi HTTP başlığıyla taşındığının net olarak bilinmesidir. Eğer `$request->getRemoteIp()` ile dönen IP, bilinen güvenli bir proxy sunucusuysa ve gerçek IP'nin taşındığı HTTP başlığını `$request->header('gerçek IP taşıyan HTTP başlığı')` ile alabilirsiniz.


## Sunucu IP'sini Almak
```php
$request->getLocalIp();
```

## Sunucu Portunu Almak
```php
$request->getLocalPort();
```

## Ajax İsteği Olup Olmadığını Kontrol Etme
```php
$request->isAjax();
```

## Pjax İsteği Olup Olmadığını Kontrol Etme
```php
$request->isPjax();
```

## JSON Yanıtı Beklenip Beklenmediğini Kontrol Etme
```php
$request->expectsJson();
```

## İstemcinin JSON Yanıtını Kabul Edip Etmeyeceğini Kontrol Etme
```php
$request->acceptJson();
```

## İstekten Eklenti Adını Almak
Eklenti isteği olmadığında boş dize `''` döner.
```php
$request->plugin;
```
> Bu özellik webman>=1.4.0 gerektirir

## İstekten Uygulama Adını Almak
Tek uygulama durumunda her zaman boş dize `''` döner, [birden fazla uygulama](multiapp.md) durumunda uygulama adını döndürür.
```php
$request->app;
```

> Çünkü bir kapanış fonksiyonu herhangi bir uygulamaya ait değildir, bu nedenle kapanış yönlendirmesinden gelen istek için `$request->app` her zaman boş dize `''` döner
> Kapanış yönlendirmesi için [Yönlendirme](route.md) sayfasına bakın

## İstekten Denetleyici Sınıf Adını Almak
Denetleyiciye karşılık gelen sınıf adını almak
```php
$request->controller;
```
`app\controller\IndexController` gibi bir değer döner

> Bir kapanış fonksiyonu herhangi bir denetleyiciye ait olmadığından, kapanış yönlendirmesinden gelen istek için `$request->controller` her zaman boş dize `''` döner
> Kapanış yönlendirmesi için [Yönlendirme](route.md) sayfasına bakın

## İstekten Metot Adını Almak
İsteğe karşılık gelen denetleyici metot adını almak
```php
$request->action;
```
`index` gibi bir değer döner

> Bir kapanış fonksiyonu herhangi bir denetleyiciye ait olmadığından, kapanış yönlendirmesinden gelen istek için `$request->action` her zaman boş dize `''` döner
> Kapanış yönlendirmesi için [Yönlendirme](route.md) sayfasına bakın
