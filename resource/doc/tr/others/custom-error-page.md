## Özel 404 Sayfası
webman, 404 hatası durumunda otomatik olarak `public/404.html` içeriğini döndürecektir, bu nedenle geliştiriciler doğrudan `public/404.html` dosyasını değiştirebilir.

Eğer 404 içeriğini dinamik olarak kontrol etmek istiyorsanız, örneğin ajax isteği durumunda JSON veri döndürmek `{"code:"404", "msg":"404 sayfa bulunamadı"}`, sayfa isteği durumunda `app/view/404.html` şablonunu döndürmek istiyorsanız aşağıdaki örneğe bakınız.

> Aşağıda PHP templatelere örnek olarak kullanılan, diğer template'ler `twig` `blade` `think-template` gibi, benzer prensiplere sahiptir.

**`app/view/404.html` dosyasını oluşturun**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 Sayfa Bulunamadı</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php` dosyasına aşağıdaki kodu ekleyin:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax isteği durumunda JSON olarak dön
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 sayfa bulunamadı']);
    }
    // Sayfa isteği durumunda 404.html şablonunu döndür
    return view('404', ['error' => 'bir hata'])->withStatus(404);
});
```

## Özel 500 Sayfası
**Yeni `app/view/500.html` dosyasını oluşturun**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Dahili Sunucu Hatası</title>
</head>
<body>
Özel hata şablonu:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Yeni** `app/exception/Handler.php` **(klasör yoksa kendiniz oluşturun)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Renderlama işlemi
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajax isteği durumunda JSON veri döndür
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Sayfa isteği durumunda 500.html şablonunu döndür
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**`config/exception.php` dosyasını yapılandırın**
```php
return [
    '' => \app\exception\Handler::class,
];
```
