## Özel 404 Sayfası
webman, 404 hatası durumunda otomatik olarak `public/404.html` içeriğini döndürür, bu nedenle geliştiriciler `public/404.html` dosyasını doğrudan değiştirebilirler.

Eğer 404 içeriğini dinamik olarak kontrol etmek istiyorsanız, örneğin ajax isteği yapıldığında JSON verisi `{"code:"404", "msg":"404 bulunamadı"}` döndürmek veya sayfa isteği yapıldığında `app/view/404.html` şablonunu döndürmek istiyorsanız, aşağıdaki örneğe bakabilirsiniz:

> Aşağıdaki örnek PHP templateleri için verilmiştir, diğer templateler `twig`, `blade`, `think-template` gibi prensip olarak benzerdir.

**`app/view/404.html` dosyasını oluşturun:**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 Bulunamadı</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php`'ye aşağıdaki kodları ekleyin:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax isteği yapıldığında JSON döndür
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 bulunamadı']);
    }
    // Sayfa isteği yapıldığında 404.html şablonunu döndür
    return view('404', ['error' => 'bir hata'])->withStatus(404);
});
```

## Özel 500 Sayfası
**`app/view/500.html` dosyasını oluşturun:**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Sunucu Hatası</title>
</head>
<body>
Özel hata şablonu:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**`app/exception/Handler.php` dosyasını oluşturun** (dizin yoksa kendiniz oluşturun)
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Getirme işlemi
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajax isteği yapıldığında JSON veri döndür
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Sayfa isteği yapıldığında 500.html şablonunu döndür
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**`config/exception.php`'yi yapılandırın**
```php
return [
    '' => \app\exception\Handler::class,
];
```
