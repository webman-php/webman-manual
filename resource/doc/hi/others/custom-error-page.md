## अपने आप को निर्दिष्ट करें 404
webman 404 को स्वचालित रूप से `public/404.html` के भीतर की सामग्री वापस करेगा, इसलिए डेवलपर सीधे `public/404.html` फ़ाइल को बदल सकते हैं।

यदि आप डाइनामिक रूप से 404 की सामग्री कंट्रोल करना चाहते हैं, उदाहरण के तौर पर एजेक्स अनुरोध पर जेसन डेटा `{"code:"404", "msg":"404 not found"}` वापस देना, पृष्ठ अनुरोध पर `app/view/404.html` टेम्पलेट वापस देना, कृपया निम्न उदाहरण का पालन करें

> निम्नलिखित PHP मूल टेम्पलेट के उदाहरण के रूप में, इसके अन्य टेम्पलेट `twig` `blade` `think-tmplate` का सिद्धांत समान है

**फ़ाइल बनाएँ `app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php` में निम्न उपलब्धि जोड़ें:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // एजाक्स अनुरोध पर जेसन वापस दें
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // पृष्ठ अनुरोध 404.html टेम्पलेट वापस दे
    return view('404', ['error' => 'कोई त्रुटि'])->withStatus(404);
});
```

## अपने आप को निर्दिष्ट करें 500
**नया बनाएँ `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
स्वनिर्धारित त्रुटि टेम्पलेट:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**नया बनाएँ**app/exception/Handler.php**(यदि निर्देशिका मौजूद नहीं है, तो कृपया स्वयं बनाएं)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * रेंडर वापस
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // एजाक्स अनुरोध पर जेसन डेटा वापस दें
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // पृष्ठ अनुरोध 500.html टेम्पलेट वापस दे
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**विन्यास करें `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
