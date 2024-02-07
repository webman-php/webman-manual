## स्थिर फ़ाइल को प्रसंस्करण करना
webman स्थिर फ़ाइल पहुंच का समर्थन करता है, स्थिर फ़ाइल सभी `public` निर्देशिका में रखी जाती हैं, उदाहरण के लिए,`http://127.0.0.8787/upload/avatar.png` की पहुंच के बारे में वास्तव में`{मुख्य परियोजना निर्देशिका}/public/upload/avatar.png` की पहुंच है।

> **ध्यान दें**
> webman 1.4 से ऐप प्लगइन का समर्थन करता है,`/app/xx/फ़ाइलनाम` पर आधारित स्थिर फ़ाइल पहुंच वास्तव में ऐप प्लगइन की `public` निर्देशिका का अभिगमन होता है, इसका मतलब है कि webman >=1.4.0 के समय में`{मुख्य परियोजना निर्देशिका}/public/app/` निर्देशिका का अभिगमन समर्थन नहीं करता है।
> अधिक जानकारी के लिए [ऐप प्लगइन](./plugin/app.md) का संदर्भ लें।

### स्थिर फ़ाइल समर्थन बंद करें
यदि स्थिर फ़ाइल समर्थन की आवश्यकता नहीं है, तो `config/static.php` खोलें और `enable` विकल्प को बंद करें। बंद करने के बाद सभी स्थिर फ़ाइल का अभिगमन 404 वापस करेगा।

### स्थिर फ़ाइल निर्देशिका बदलना
webman डिफ़ॉल्ट रूप से स्थिर फ़ाइल निर्देशिका के रूप में पब्लिक निर्देशिका का उपयोग करता है। इसे बदलने की आवश्यकता पड़े तो`support/helpers.php` में `public_path()` सहायक फ़ंक्शन को बदलें।

### स्थिर फ़ाइल मध्यवर्ती
webman एक स्थिर फ़ाइल मध्यवर्ती के साथ आता है, स्थान`app/middleware/StaticFile.php`।
कभी-कभी हमें स्थिर फ़ाइलों पर कुछ प्रक्रिया करने की आवश्यकता होती है, जैसे स्थिर फ़ाइल में क्रॉस-ऑरिज़न हैडर जोड़ना, डॉट (`.`) से शुरू होने वाली फ़ाइलों का उपयोग न करने के लिए इस मध्यवर्ती का उपयोग किया जा सकता है।

`app/middleware/StaticFile.php` की सामग्री निम्नलिखित होती है:
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
        // . से शुरू होने वाली छिपी फ़ाइल का उपयोग न करें
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // क्रॉस-ऑरिज़न हेडर जोड़ें
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
``` 
इस मध्यवर्ती की आवश्यकता होने पर, `config/static.php` में `middleware` विकल्प को सक्षम करें।
