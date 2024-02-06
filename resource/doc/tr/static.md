## Statik Dosyaların İşlenmesi
webman, statik dosya erişimini destekler. Statik dosyalar genellikle `public` klasörü altında bulunur, örneğin `http://127.0.0.8787/upload/avatar.png` adresine erişmek aslında `{ana proje dizini}/public/upload/avatar.png`'ye erişmek anlamına gelir.

> **Not**
> webman 1.4 sürümüyle birlikte uygulama eklentilerini desteklemeye başladı, `/app/xx/file_name` ile başlayan statik dosya erişimi aslında uygulama eklentisinin `public` klasörüne erişmek anlamına gelir, yani webman >=1.4.0 sürümünde `{ana proje dizini}/public/app/` altındaki klasörlere erişim desteklenmemektedir.
> Daha fazlası için [Uygulama Eklentileri](./plugin/app.md) bölümüne bakın.

### Statik dosya desteğini kapatma
Eğer statik dosya desteğine ihtiyaç duymuyorsanız, `config/static.php` dosyasını açıp `enable` seçeneğini false olarak değiştirin. Kapatıldıktan sonra tüm statik dosya erişimleri 404 hatası döndürecektir.

### Statik dosya dizinini değiştirme
webman, varsayılan olarak statik dosya dizini olarak public klasörünü kullanır. Değiştirmek istiyorsanız, lütfen `support/helpers.php` dosyasındaki `public_path()` yardımcı işlevini değiştirin.

### Statik dosya Middleware
webman'in kendi bir statik dosya aracı vardır, konumu `app/middleware/StaticFile.php`.
Bazı durumlarda, statik dosyalara bazı işlemler yapmak gerekebilir, örneğin statik dosyalara çapraz domain HTTP başlıkları eklemek veya nokta (`.`) ile başlayan dosyaların erişimini engellemek için bu middleware kullanılabilir.

`app/middleware/StaticFile.php` içeriği aşağıdaki gibi olabilir:
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
        // Görünmeyen dosyaların erişimini engelle
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 yasak</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Çapraz domain HTTP başlıkları ekle
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Bu middleware'i kullanmanız gerekiyorsa, `config/static.php` dosyasında `middleware` seçeneğini etkinleştirmeniz gerekmektedir.
