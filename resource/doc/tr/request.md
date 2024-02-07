# Açıklama

## İstek Objesini Almak
webman, istek nesnesini action yönteminin ilk parametresine otomatik olarak enjekte eder, örneğin

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
        // get isteğinden ad parametresini al, eğer ad parametresi geçilmediyse $default_name'i döndür
        $name = $request->get('name', $default_name);
        // Tarayıcıya dize döndür
        return response('hello ' . $name);
    }
}
```

`$request` nesnesi aracılığıyla herhangi bir istekle ilgili herhangi bir veri alabiliriz.

**Bazen başka bir sınıfta mevcut isteğin `$request` nesnesini almak istiyoruz, bu durumda sadece yardımcı fonksiyonu `request()` kullanmamız yeterlidir.

## Get İstek Parametrelerini Almak

**Tüm get dizisini al**
```php
$request->get();
```
Eğer istekte get parametresi yoksa boş bir dizi döndürülür.

**Get dizisinden bir değer al**
```php
$request->get('name');
```
Eğer get dizisi bu değeri içermiyorsa null döndürülür.

Ayrıca get yöntemine ikinci bir parametre olarak varsayılan bir değer iletebilirsiniz, eğer get dizisi ilgili değeri bulamazsa varsayılan değer döndürülür. Örneğin:
```php
$request->get('name', 'tom');
```

## Post İstek Parametrelerini Almak
**Tüm post dizisini al**
```php
$request->post();
```
Eğer istekte post parametresi yoksa boş bir dizi döndürülür.

**Post dizisinden bir değer al**
```php
$request->post('name');
```
Eğer post dizisi bu değeri içermiyorsa null döndürülür.

Get yöntemi ile benzer şekilde, ikinci bir parametre olarak post yöntemine varsayılan bir değer iletebilirsiniz. Eğer post dizisi ilgili değeri bulamazsa varsayılan değer döndürülür. Örneğin:
```php
$request->post('name', 'tom');
```

## Raw Post İsteği Almak
```php
$post = $request->rawBody();
```
Bu işlev `php-fpm` içindeki `file_get_contents("php://input");` işlemine benzer. HTTP raw istek gövdesini almak için kullanılır. Bu, `application/x-www-form-urlencoded` biçiminde olmayan post istek verilerini almak için kullanışlıdır.

## Header Almak
**Tüm header dizisini al**
```php
$request->header();
```
Eğer istekte header parametresi yoksa boş bir dizi döndürülür. Tüm anahtarlar küçük harfle yazılmıştır.

**Header dizisinden bir değer al**
```php
$request->header('host');
```
Eğer header dizisi bu değeri içermiyorsa null döndürülür. Tüm anahtarlar küçük harfle yazılmıştır.

Get yöntemi ile benzer şekilde, ikinci bir parametre olarak header yöntemine varsayılan bir değer iletebilirsiniz. Eğer header dizisi ilgili değeri bulamazsa varsayılan değer döndürülür. Örneğin:
```php
$request->header('host', 'localhost');
```

## Çerez Almak
**Tüm çerez dizisini al**
```php
$request->cookie();
```
Eğer istekte çerez parametresi yoksa boş bir dizi döndürülür.

**Çerez dizisinden bir değer al**
```php
$request->cookie('name');
```
Eğer çerez dizisi bu değeri içermiyorsa null döndürülür.

Get yöntemi ile benzer şekilde, ikinci bir parametre olarak cookie yöntemine varsayılan bir değer iletebilirsiniz. Eğer cookie dizisi ilgili değeri bulamazsa varsayılan değer döndürülür. Örneğin:
```php
$request->cookie('name', 'tom');
```

## Tüm Girişleri Almak
`post` ve `get` içeren toplu verileri almak için.
```php
$request->all();
```

## Belirli Bir Giriş Değerini Almak
`post` ve `get` içinden belirli bir değeri almak için.
```php
$request->input('name', $default_value);
```

## Kısmi Giriş Verilerini Almak
`post` ve `get` içinden belirli verileri almak için.
```php
// Kullanıcı adı ve şifre'den oluşan bir dizi al, eğer ilgili anahtar bulunmazsa ihmal et
$only = $request->only(['username', 'password']);
// Avatar ve yaş dışındaki tüm girişleri al
$except = $request->except(['avatar', 'age']);
```
## Dosya Yükleme
**Tüm yükleme dosyası dizisini alın**
```php
$request->file();
```

Forma benzer:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`'ın döndürdüğü format şuna benzer:
```php
array (
    'file1' => nesne(webman\Http\UploadFile),
    'file2' => nesne(webman\Http\UploadFile)
)
```
Bu, `webman\Http\UploadFile` örneklerinin bir dizisidir. `webman\Http\UploadFile` sınıfı PHP'nin yerleşik olan [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) sınıfını genişleterek bazı pratik yöntemler sunar.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Dosya geçerli mi, örn. true|false
            var_export($spl_file->getUploadExtension()); // Yükleme dosyası uzantısı, örn. 'jpg'
            var_export($spl_file->getUploadMimeType()); // Yükleme dosyası mime türü, örn. 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Yükleme hatası kodunu al, örn. UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Yükleme dosya adı, örn. 'my-test.jpg'
            var_export($spl_file->getSize()); // Dosya boyutunu al, örn. 13364, byte cinsinden
            var_export($spl_file->getPath()); // Yüklü dizini al, örn. '/tmp'
            var_export($spl_file->getRealPath()); // Geçici dosya yolunu al, örn. `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Not:**
- Dosya yüklendikten sonra geçici bir dosya adı verilir, `/tmp/workerman.upload.SRliMu` gibi
- Yükleme dosya boyutu [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) kısıtlamasına tabidir, varsayılan olarak 10M'dir, `config/server.php` dosyasında `max_package_size`'ı değiştirerek varsayılan değeri değiştirebilirsiniz.
- İstek sona erdiğinde geçici dosya otomatik olarak temizlenir
- Eğer istekte yükleme dosyası yoksa, `$request->file()` boş bir dizi döndürür
- Yükleme dosyaları `move_uploaded_file()` yöntemini desteklemez, bunun yerine `$file->move()` yöntemini kullanmalısınız, aşağıdaki örneğe bakın

### Belirli yükleme dosyasını alın
```php
$request->file('avatar');
```
Eğer dosya mevcutsa, karşılık gelen `webman\Http\UploadFile` örneğini döndürür, aksi takdirde null döndürür.

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
            return json(['code' => 0, 'msg' => 'yükleme başarılı']);
        }
        return json(['code' => 1, 'msg' => 'dosya bulunamadı']);
    }
}
```

## Host Bilgisini Al
İsteğin host bilgisini alır.
```php
$request->host();
```
Eğer isteğin adresi standart olmayan 80 veya 443 portunu içeriyorsa, host bilgisi port ile birlikte olabilir, örneğin `example.com:8080`. Eğer port gerekmiyorsa, ilk parametre `true` olarak iletilir.

```php
$request->host(true);
```

## İstek Methodunu Al
```php
 $request->method();
```
Döndürülen değer `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD` değerlerinden biri olabilir.

## İstek URI'sini Al
```php
$request->uri();
```
Path ve queryString bölümlerini içeren isteğin URI'sini döndürür.

## İstek Path'ini Al
```php
$request->path();
```
İstek path'ini döndürür.

## İstek QueryString'ini Al
```php
$request->queryString();
```
İstek query string'ini döndürür.

## İstek URL'ini Al
`url()` yöntemi, `Query` parametre içermeyen URL döndürür.
```php
$request->url();
```
`//www.workerman.net/workerman-chat` gibi bir değer döndürür.

`fullUrl()` yöntemi, `Query` parametre içeren URL döndürür.
```php
$request->fullUrl();
```
`//www.workerman.net/workerman-chat?type=download` gibi bir değer döndürür.

> **Not**
> `url()` ve `fullUrl()` adresin protokol kısmını (http veya https) döndürmez.
> Çünkü tarayıcıda `//example.com` gibi `//` ile başlayan adresler otomatik olarak mevcut sitenin protokolünü algılar ve otomatik olarak http veya https ile istek yapar.

Eğer nginx proxy kullanıyorsanız, nginx konfigürasyonuna `proxy_set_header X-Forwarded-Proto $scheme;` ekleyin, [nginx proxy referansı](others/nginx-proxy.md),
böylece `http` veya `https` olup olmadığını belirlemek için `$request->header('x-forwarded-proto');`'yu kullanabilirsiniz, örneğin:
```php
echo $request->header('x-forwarded-proto'); // http veya https çıktısını verir
```

## İstek HTTP sürümünü Al
```php
$request->protocolVersion();
```
String olarak `1.1` veya `1.0` döndürür.

## İstek session ID'sini al
```php
$request->sessionId();
```
Harfler ve rakamlardan oluşan bir dize döndürür

## İstek istemci IP'sini al
```php
$request->getRemoteIp();
```

## İstek istemci portunu al
```php
$request->getRemotePort();
```
## İstemci Gerçek IP'sini Almak
```php
$request->getRealIp($safe_mode=true);
```

Projede bir işaretleme (örneğin nginx) kullanıldığında, `$request->getRemoteIp()` kullanılarak genellikle temsilci sunucusu IP'si (örneğin `127.0.0.1` `192.168.x.x`) elde edilir, gerçek istemci IP'si değil. Bu durumda, istemci gerçek IP'sini elde etmek için `$request->getRealIp()` kullanılabilir.

`$request->getRealIp()`, gerçek IP'yi almak için HTTP başlığının `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` alanlarından almaya çalışacaktır.

> HTTP başlıkları kolayca sahte olabileceğinden, bu yöntemle elde edilen istemci IP'si %100 güvenilir değildir, özellikle `$safe_mode` false ise. İstemci gerçek IP'sini bir temsilciden güvenilir bir şekilde almak için, bilinen güvenli bir temsilci sunucusu IP'sini bilmek ve gerçek IP'nin hangi HTTP başlığıyla taşındığını bilmek gereklidir. Eğer `$request->getRemoteIp()` dönen IP, bilinen güvenli bir temsilci sunucusunu doğrularsa, o zaman gerçek IP'yi almak için `$request->header('gerçek IP'yi içeren HTTP başlığı')` kullanılabilir.



## Sunucu IP'sini Alma
```php
$request->getLocalIp();
```


## Sunucu Portunu Alma
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


## JSON Cevabı Bekleniyor mu Kontrol Etme
```php
$request->expectsJson();
```


## İstemcinin JSON Cevabını Kabul Edip Etmeyeceğini Kontrol Etme
```php
$request->acceptJson();
```


## İstek Plugin Adını Alma
Plugin isteği boş dize `''` olarak döner.
```php
$request->plugin;
```
> Bu özellik için webman>=1.4.0 gereklidir



## İstek Uygulama Adını Alma
Tek uygulamada her zaman boş dize `''` döner, [çoklu uygulamada](multiapp.md) uygulama adını döner
```php
$request->app;
```


> Kapanış fonksiyonları herhangi bir uygulamaya ait olmadığı için, kapanış rotasından gelen isteklerde `$request->app` daima boş dize `''` döner
> Kapanış rotası için bakınız: [Rotalar](route.md)



## İstek Denetleyici Sınıf Adını Alma
Denetleyiciye karşılık gelen sınıf adını alır
```php
$request->controller;
```
`app\controller\IndexController` benzeri birşey döner

> Kapanış fonksiyonları herhangi bir denetleyiciye ait olmadığı için, kapanış rotasından gelen isteklerde `$request->controller` daima boş dize `''` döner
> Kapanış rotası için bakınız: [Rotalar](route.md)



## İstek Yöntem Adını Alma
İsteğe karşılık gelen denetleyici yöntem adını alır
```php
$request->action;
```
`index` gibi bir şey döner

> Kapanış fonksiyonları herhangi bir denetleyiciye ait olmadığı için, kapanış rotasından gelen isteklerde `$request->action` daima boş dize `''` döner
> Kapanış rotası için bakınız: [Rotalar](route.md)
