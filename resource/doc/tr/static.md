## Statik Dosyaların İşlenmesi
webman, statik dosya erişimini destekler. Statik dosyalar genellikle `public` dizini altına yerleştirilir, örneğin `http://127.0.0.8787/upload/avatar.png` adresine yapılan istek aslında `{ana_proje_dizini}/public/upload/avatar.png`'e erişim sağlar.

> **Not**
> webman 1.4 sürümünden itibaren uygulama eklentilerini desteklemektedir. `/app/xx/dosya_adı` ile başlayan statik dosya erişimi aslında uygulama eklentisinin `public` dizinine erişim sağlar. Başka bir deyişle, webman >=1.4.0 sürümü, `{ana_proje_dizini}/public/app/` altındaki dizinlere erişim sağlamamaktadır.
> Daha fazlası için [Uygulama Eklentileri](./plugin/app.md)'ne bakınız.

### Statik Dosya Desteğini Kapatma
Eğer statik dosya desteğine ihtiyaç duymuyorsanız, `config/static.php` dosyasını açarak `enable` seçeneğini `false` olarak değiştirin. Kapatıldığında tüm statik dosya erişimi 404 hatası döndürecektir.

### Statik Dosya Dizini Değiştirme
webman, varsayılan olarak statik dosyalar için public dizinini kullanır. Değiştirmek istiyorsanız, `support/helpers.php` dosyasındaki `public_path()` yardımcı işlevini değiştirmeniz gerekmektedir.

### Statik Dosya Ara Yazılımı
webman'in kendi içinde bir statik dosya ara yazılımı vardır, konumu `app/middleware/StaticFile.php`.
Bazen statik dosyalar üzerinde bazı işlemler yapmamız gerekebilir, örneğin statik dosyalara çapraz kaynak (CORS) başlığı eklemek, nokta (`.`) ile başlayan dosyalara erişimi engellemek için bu ara yazılımı kullanabiliriz.

`app/middleware/StaticFile.php` dosyası aşağıdaki gibi olabilir:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Nokta ile başlayan gizli dosyalara erişimi engelle
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Çapraz kaynak (CORS) başlığı ekle
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Bu ara yazılımı gerektiğinde `config/static.php` dosyasındaki `middleware` seçeneğini etkinleştirmeniz gerekmektedir.
