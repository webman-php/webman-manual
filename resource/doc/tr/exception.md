# İstisna İşleme

## Yapılandırma
`config/exception.php`
```php
return [
    // İstisna işleme sınıfını burada yapılandırın
    '' => support\exception\Handler::class,
];
```
Çoklu uygulama modunda her uygulama için ayrı ayrı istisna işleme sınıfı yapılandırabilirsiniz. [Çoklu Uygulama](multiapp.md) bölümüne bakınız.


## Varsayılan İstisna İşleme Sınıfı
webman'de istisnalar varsayılan olarak `support\exception\Handler` sınıfı tarafından işlenir. Varsayılan istisna işleme sınıfını değiştirmek için `config/exception.php` yapılandırma dosyasını değiştirebilirsiniz. İstisna işleme sınıfı `Webman\Exception\ExceptionHandlerInterface` arayüzünü uygulamak zorundadır.
```php
interface ExceptionHandlerInterface
{
    /**
     * Günlüğe kaydet
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Yanıtı oluştur
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Yanıt Oluşturma
İstisna işleme sınıfı içindeki `render` yöntemi yanıtı oluşturmak için kullanılır.

Eğer `config/app.php` yapılandırma dosyasında `debug` değeri `true` ise (kısaca `app.debug=true`), ayrıntılı istisna bilgileri döndürülür, aksi halde kısa istisna bilgilerini döndürür.

Eğer istemci isteği JSON yanıt bekliyorsa, istisna bilgisi JSON formatında döndürülür, benzer şekilde:
```json
{
    "code": "500",
    "msg": "İstisna Bilgisi"
}
```
Eğer `app.debug=true` ise, JSON verilerine ayrıca detaylı çağrı yığını (`trace`) alanı eklenir.

Varsayılan istisna işleme mantığını değiştirmek için kendi istisna işleme sınıfınızı yazabilirsiniz.

# İş İstisnaları BusinessException
Bazen iç içe geçmiş bir fonksiyonda isteği sonlandırmak ve istemciye bir hata ileti döndürmek isteyebiliriz, bu durumda bunu yapmak için `BusinessException` fırlatabiliriz.
Örneğin:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Parametre Hatası', 3000);
        }
    }
}
```

Yukarıdaki örnek aşağıdakini döndürecektir:
```json
{"code": 3000, "msg": "Parametre Hatası"}
```

> **Not**
> İş istisnası BusinessException yakalanması gerekmeyen bir durumdur, çerçeve otomatik olarak yakalar ve isteğin türüne göre uygun çıktıyı döndürür.

## Özel İş İstisnaları

Eğer yukarıdaki yanıt sizin gereksinimlerinize uymuyorsa, örneğin `msg` yerine `message` yazmak istiyorsanız, `MyBusinessException` adında özel bir istisna sınıfı oluşturabilirsiniz.

`app/exception/MyBusinessException.php` dosyasını aşağıdaki içerikle oluşturun:
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // JSON isteği JSON veri döndürür
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // JSON olmayan istek bir sayfa döndürür
        return new Response(200, [], $this->getMessage());
    }
}
```

Böylece iş mantığı
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parametre Hatası', 3000);
```
çalıştığında, JSON isteği aşağıdaki gibi bir JSON yanıt alacaktır:
```json
{"code": 3000, "message": "Parametre Hatası"}
```

> **İpucu**
> BusinessException istisnası, öngörülebilir bir iş istisnası olduğu için çerçeve onu ölümcül bir hata olarak kabul etmez ve günlüğe kaydetmez.

## Sonuç
Mevcut isteği sonlandırıp istemciye bilgi döndürmek istediğiniz her durumda `BusinessException` istisnasını kullanmayı düşünebilirsiniz.
