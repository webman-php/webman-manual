# İstisna Yönetimi

## Yapılandırma
`config/exception.php`
```php
return [
    // Burada istisna işleme sınıfını yapılandırın
    '' => support\exception\Handler::class,
];
```
Çoklu uygulama modunda, her uygulama için ayrı istisna işleme sınıfı yapılandırabilirsiniz, bkz. [Çoklu Uygulama](multiapp.md)


## Varsayılan İstisna İşleme Sınıfı
webman'de istisnalar varsayılan olarak `support\exception\Handler` sınıfı tarafından işlenir. Varsayılan istisna işleme sınıfını değiştirmek için `config/exception.php` yapılandırma dosyasını değiştirebilirsiniz. İstisna işleme sınıfı, `Webman\Exception\ExceptionHandlerInterface` arabirimini uygulamak zorundadır.
```php
interface ExceptionHandlerInterface
{
    /**
     * Günlüğe kayıt
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



## Yanıtı Oluşturma
İstisna işleme sınıfındaki `render` yöntemi yanıtı oluşturmak için kullanılır.

Eğer `config/app.php` yapılandırma dosyasında `debug` değeri `true` ise (kısaca `app.debug=true`), ayrıntılı istisna bilgileri döndürülür, aksi takdirde özet istisna bilgileri döndürülür.

Eğer istek json yanıt bekliyorsa, istisna bilgileri json formatında döndürülür, örneğin
```json
{
    "code": "500",
    "msg": "istisna bilgisi"
}
```
Eğer `app.debug=true` ise, json verilerine ayrıntılı bir `trace` alanı eklenir.

Varsayılan istisna işleme mantığını değiştirmek için kendi istisna işleme sınıfınızı yazabilirsiniz.

# İş İstisnası BusinessException
Bazen iç içe geçmiş bir fonksiyonda isteği sonlandırmak ve istemciye bir hata iletisi döndürmek isteyebiliriz, bu durumda bunu yapmak için `BusinessException` fırlatılabilir.
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
            throw new BusinessException('Parametre hatası', 3000);
        }
    }
}
```

Yukarıdaki örnek aşağıdaki gibi bir yanıt döndürecektir
```json
{"code": 3000, "msg": "Parametre hatası"}
```

> **Not**
> İş istisnası BusinessException işlemesine gerek yoktur, çünkü framework otomatik olarak yakalar ve istek tipine uygun çıktı döndürür.

## Özel İş İstisnası Oluşturma

Yukarıdaki yanıt ihtiyaçlarınıza uygun değilse, örneğin `msg`'yi `message` olarak değiştirmek istiyorsanız, `MyBusinessException` adında özel bir istisna oluşturabilirsiniz.

Aşağıdaki gibi bir `app/exception/MyBusinessException.php` dosyası oluşturun
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
        // json isteği json veri döndürür
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // json olmayan istek bir sayfa döndürür
        return new Response(200, [], $this->getMessage());
    }
}
```

Bu durumda iş istisnası oluşturulduğunda
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parametre hatası', 3000);
```
json isteği aşağıdaki gibi bir json döner
```json
{"code": 3000, "message": "Parametre hatası"}
```

> **İpucu**
> BusinessException istisnası, iş istisnası (örneğin kullanıcı giriş parametre hatası) olduğu için beklenen bir durumdur, bu nedenle framework onun ölümcül bir hata olduğunu düşünmez ve günlüğe kaydetmez.

## Özet
Mevcut isteği kesmeyi ve istemciye bir bilgi döndürmeyi düşündüğünüz herhangi bir durumda `BusinessException` istisnasının kullanımını düşünebilirsiniz.
