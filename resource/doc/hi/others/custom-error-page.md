## अपनी 404 फ़ाइल कस्टमाइज़ करें
webman 404 के दौरान स्वचालित रूप से `public/404.html` में सामग्री लौटाएगा, इसलिए डेवलपर सीधे `public/404.html` फ़ाइल को बदल सकता है।

यदि आप चाहते हैं कि 404 सामग्री को डायनामिक रूप से नियंत्रित किया जाए, उदाहरण के लिए एज़ाक्स अनुरोध के दौरान json डेटा `{"code:"404", "msg":"404 not found"}` लौटाना, पृष्ठ अनुरोध के दौरान `app/view/404.html` टेम्पलेट लौटाना चाहते हैं, तो कृपया निम्नलिखित उदाहरण का पालन करें

> निम्नलिखित में php मूल टेम्पलेट का उदाहरण दिया गया है, अन्य टेम्पलेट `twig` `blade` `think-tmplate` भी इसी तरह काम करेंगे।

**`app/view/404.html` फ़ाइल बनाएं**
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

**`config/route.php` में निम्नलिखित कोड जोड़ें:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // एज़ाक्स अनुरोध के दौरान json लौटाएं
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // पेज अनुरोध के दौरान 404.html टेम्पलेट लौटाएं
    return view('404', ['error' => 'कोई त्रुटि'])->withStatus(404);
});
```

## अपनी 500 त्रुटि कस्टमाइज़ करें
**नया `app/view/500.html` बनाएं**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
कस्टम त्रुटि टेम्पलेट:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**नया** `app/exception/Handler.php`**(इस निर्देशिका में उपलब्ध नहीं है तो स्वयं बनाएं)****
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * लौटाना
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // एज़ाक्स अनुरोध json डेटा लौटाएं
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // पेज अनुरोध के दौरान 500.html टेम्पलेट लौटाएं
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**`config/exception.php` कॉन्फ़िगर करें**
```php
return [
    '' => \app\exception\Handler::class,
];
```
