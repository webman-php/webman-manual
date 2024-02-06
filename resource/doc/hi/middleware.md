# मिडलवेयर
मिडलवेयर सामान्यत: अनुरोध या प्रतिक्रिया को रोकने के लिए उपयोग किया जाता है। उदाहरण के लिए, एक नियंत्रक को निष्पादित करने से पहले उपयोक्ता की पहचान की पुष्टि करना, उदाहरण के रूप में उपयोगकर्ता लॉगइन नहीं किया हो तो लॉगिन पेज पर पुनःसंदेश भेजना, उदाहरण के रूप में किसी header header को जोड़ना, उदाहरण के रूप में किसी URI अनुरोध का हिस्सा का भराव पर सांख्यिकी बढ़ाना और बहुत कुछ।

## मिडलवेयर प्याज़ मॉडल

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     मिडलवेयर1                       │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               मिडलवेयर2                 │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         मिडलवेयर3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── अनुरोध ──────────────────────> नियंत्रक ─ प्रतिक्रिया ───────────────────────────> ग्राहक
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
मिडलवेयर और नियंत्रक एक शास्त्रीय प्याज़ मॉडल का अंग हैं, मिडलवेयर एक एक स्तर का प्याज़ होते हैं, नियंत्रक प्याज़ के केनर होते हैं। जैसा कि चित्र में दिखाया गया है, अनुरोध मिडलवेयर 1, 2, 3 से गुज़रता है नियंत्रक तक पहुंचता है, नियंत्रक ने एक प्रतिक्रिया लौटाई, फिर प्रतिक्रिया मिडलवेयर 3, 2, 1 के क्रम में गुज़रकर ग्राहक को वापस लौटाई। यानी कि प्रत्येक मिडलवेयर में हम अनुरोध प्राप्त कर सकते हैं, साथ ही ही हमे प्रतिक्रिया भी प्राप्त होती है।

## अनुरोध रोक
कभी-कभी हम चाहते हैं कि किसी निश्चित अनुरोध को नियंत्रक स्तर तक न जाने दिया जाए, उदाहरण के लिए, हम एक पहचान मिडलवेयर में जानते हैं कि वर्तमान उपयोगकर्ता लॉग इन नहीं हैं, तो हम सीधे अनुरोध को रोक सकते हैं और एक लॉगिन प्रतिक्रिया लौटा सकते हैं। तो यह प्रक्रिया नीचे वाले तरीके की तरह होगी

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     मिडलवेयर1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │           आभिवचन मिडलवेयर                │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         मिडलवेयर 3         │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── अनुरोध ──────────┐  │     │       नियंत्रक      │     │     │
            │     │ प्रतिक्रिया │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

चित्र में दिखाया गया है कि पहचान मिडलवेयर में अनुरोध पंहूंचा और एक लॉगिन प्रतिक्रिया उत्पन्न हुई, प्रतिक्रिया पहचान मिडलवेयर से गुजरकर मिडलवेयर1 तक लौटी और फिर ब्राउज़र को वापस लौटी।

## मिडलवेयर इंटरफेस
मिडलवेयर को `Webman\MiddlewareInterface` इंटरफेस को अमल करना आवश्यक है।
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
यानी `process` विधि की अनिवार्य रूप से अंगीकारी होनी चाहिए, `process` विधि को एक `support\Response` ऑब्जेक्ट वापस करना चाहिए, डिफ़ॉल्ट रूप से यह ऑब्जेक्ट `$handler($request)` द्वारा उत्पन्न होता है (अनुरोध को लवली सेंट करता है), इसके अलावा `response()` `json()` `xml()` `redirect()` और अन्य हेल्पर फ़ंक्शन द्वारा प्राप्त होता है (अनुरोध को लवली सेंट करता है)।

## मिडलवेयर में अनुरोध और प्रतिक्रिया प्राप्त करना
मिडलवेयर में हम अनुरोध प्राप्त कर सकते हैं, और नियंत्रक के बाद प्रतिक्रिया भी प्राप्त कर सकते हैं, इसलिए मिडलवेयर का भी तीन भाग होते हैं।
1. अनुरोध का पारण घटक, यानी अनुरोध प्रसंस्करण से पहले की अवस्था
2. नियंत्रक द्वारा अनुरोध प्रसंस्करण, यानी अनुरोध प्रसंस्करणी अवस्था
3. प्रतिक्रिया का पारण घटक, यानी प्रतिक्रिया प्रसंस्करण के बाद की अवस्था

मिडलवेयर में इन तीन अवस्थाएँ नीचे के रूप में दिखाई देती हैं
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'यहाँ अनुरोध का पारण घटक है, यानी अनुरोध प्रसंस्करण से पहले';
        
        $response = $handler($request); // परिणामी रूप से प्लगइन को लवली सेंट किया जाएगा, जब तक नियंत्रक से प्रतिक्रिया प्राप्त ना हो
        
        echo 'यहाँ प्रतिक्रिया का पारण घटक है, यानी प्रतिक्रिया प्रसंस्करण के बाद';
        
        return $response;
    }
}
```
# उदाहरण: प्रमाणीकरण मध्यवर्ती

'फ़ाइल बनाएं `app/middleware/AuthCheckTest.php` (यदि डेयरेक्टरी मौजूद नहीं हो तो कृपया खुद बनाएं) निम्नलिखित तरह:
```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // लॉग इन कर चुके हैं, अनुरोध आगे के लिए प्याज की कोर तक जाता है
            return $handler($request);
        }

        // रिफ्लेक्शन कक्ष के द्वारा नियंत्रक किन मेथड को लॉग इन करने की आवश्यकता नहीं है, 
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // आपत्ति नियंत्रित कृपया, पुनर्निर्देशित प्रतिक्रिया लौटाएँ, अनुरोध को प्याज की कोर के लिए रोकें
        return redirect('/user/login');
        }

        // लॉगिन की आवश्यकता नहीं है, अनुरोध आगे के लिए प्याज की कोर तक जाता है
        return $handler($request);
    }
}
```

नया नियंत्रक बनाएं `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * लॉग इन की आवश्यकता नहीं है
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **ध्यान दें**
>`$noNeedLogin` में मौजूद है कि वर्तमान नियंत्रक बिना लॉगिन किए भी पहुँच सकता है।

`config/middleware.php` में निम्नलिखित तरह से ग्लोबल मध्यवर्ती जोड़ें:
```php
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        // ... अन्य मध्यवर्ती को छोड़ दें
        app\middleware\AuthCheckTest::class,
    ]
];
```

इस प्रमाणीकरण मध्यवर्ती के साथ, हम नियंत्रक पर व्यापार कोड लिखने में परेशान नहीं होते हैं, क्योंकि वे सक्षम हैं कि उपयोगकर्ता क्या कर रहे हैं।

## उदाहरण: क्रॉस-ओरिज़न अनुरोध मध्यवर्ती

फ़ाइल बनाएं `app/middleware/AccessControlTest.php` (यदि डेयरेक्टरी मौजूद नहीं हो तो कृपया खुद बनाएं) निम्नलिखित तरह:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // यदि विकल्प अनुरोध है तो एक खाली प्रतिक्रिया लौटाएँ, अन्यथा प्याज की कोर तक जारी रहें और एक प्रतिक्रिया प्राप्त करें
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // प्रतिक्रिया में क्रॉस-ओरिज़न संबंधित http हेडर जोड़ें
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

**टिप्पणी**
क्रॉस-ओरिज़न के अनुरोध के लिए OPTIONS अनुरोध उत्पन्न हो सकते हैं, हम नहीं चाहते कि OPTIONS अनुरोध कंट्रोलर में जाए, इसलिए हमने OPTIONS अनुरोध के लिए सीधा खाली प्रतिक्रिया (`response('')`) लौटाने का अनुरोध किया है। यदि आपको अपने योग्यता को सेट करने की आवश्यकता है, कृपया `Route::any(..)` या `Route::add(['POST', 'OPTIONS'], ..)` का उपयोग करें.|

`config/middleware.php` में निम्नलिखित तरह से ग्लोबल मध्यवर्ती जोड़ें:
```php
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        // ... अन्य मध्यवर्ती को छोड़ दें
        app\middleware\AccessControlTest::class,
    ]
];
```

> **ध्यान दें**
> यदि एजएक्स अनुरोध ने हेडर हेडर को निर्धारित किया है, तो कृपया मध्यवर्ती में `Access-Control-Allow-Headers` फ़ील्ड में इस कस्टम हेडर हेडर को जोड़ें, अन्यथा `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` त्रुटि आ सकती है।

## विवरण

- मध्यवर्ती को ग्लोबल मध्यवर्ती, एप्लिकेशन मध्यवर्ती (एप्लिकेशन मोड में केवल लागू होता है, देखें [अनेक एप्लिकेशन](multiapp.md)) और रूट मध्यवर्ती में विभाजित किया जा सकता है
- वर्तमान में किसी एक नियंत्रक की मध्यवर्ती का समर्थन नहीं किया गया है (लेकिन मध्यवर्ती में `$request->controller` का निर्णय करके नियंत्रक मध्यवर्ती की तरह की कार्य की जा सकती है)
- मध्यवर्ती कॉन्फ़िगरेशन फ़ाइल का स्थान `config/middleware.php` है
- ग्लोबल मध्यवर्ती को शंखला `''` के तहत विन्यासित किया गया है
- एप्लिकेशन मध्यवर्ती को यदि अनेक एप्लिकेशन मोड में ही लागू होता है

```php
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // एपीआई एप्लिकेशन मध्यवर्ती (एप्लिकेशन मोड में केवल)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## रूट मध्यवर्ती

हम किसी एक या किसी गुच्छे के लिए कुछ मार्गों पर मध्यवर्ती सेट कर सकते हैं।
उदाहरण के लिए `config/route.php` में निम्नलिखित विन्यास जोड़ें:
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## मध्यवर्ती का निर्माण संबंधी वर्गों का पास

> **ध्यान दें**
> यह सुविधा webman-framework >= 1.4.8 का समर्थन करती है

1.4.8 संस्करण के बाद, कॉन्फ़िगरेशन फ़ाइल ने सीधे मध्यवर्ती या उमरियत से सीधे विषय वस्त्राण करने के साथ इस सुविधा का समर्थन किया हैं, इस तरह से हम मध्यवर्ती को वर्गों का पास करने के लिए इसका सिद्धांत कर सकते हैं।
उदाहरण के लिए`config/middleware.php` में ऐसे कॉन्फ़िगर करें।
```
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // एपीआई एप्लिकेशन मध्यवर्ती (एप्लिकेशन मोड में केवल)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

इसी तरह, रूट मध्यवर्ती भी पैरामीटर पार करने के लिए मध्यवर्ती कोड में उमरियत से सीधे कॉन्फ़िग किया जा सकता है, उदाहरण के लिए `config/route.php` में निम्नलिखित तरह से कॉन्फ़िग करें।
```
Route :: any ('/ admin', [app \ admin \ controller \ IndexController :: class, 'index'])-> middleware ([
     new app \ middleware \ MiddlewareA ($ param1, $ param2, ...),
     function(){
         return new app\middleware\MiddlewareB($param1, $param2, ...);
     },
]);
```

## मध्यवर्ती निष्पादन क्रम
- मध्यवर्ती निष्पादन क्रम है `ग्लोबल मध्यवर्ती`->`एप्लिकेशन मध्यवर्ती`->`रूट मध्यवर्ती`।
- एक से अधिक ग्लोबल मध्यवर्ती होने पर, वास्तविक मध्यवर्ती की क्रमवादी व्यवस्था के अनुसार कार्यान्वित होती है (एप्लिकेशन मध्यवर्ती, रूट मध्यवर्ती भी इसी
## मध्यवर्ती वर्तमान अनुरोध मार्ग सूचना प्राप्त करें
> **ध्यान दें**
> webman-framework >= 1.3.2 की आवश्यकता है

हम `$request->route` का उपयोग करके मार्ग ऑब्जेक्ट प्राप्त कर सकते हैं, जिसे उपयुक्त जानकारी प्राप्त करने के लिए संबोधित मेथड को कॉल करके प्राप्त कर सकते हैं।

**मार्ग कॉन्फ़िगरेशन**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**मध्यवर्ती**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // अगर अनुरोध में कोई मार्ग संबंधित नहीं है (डिफ़ॉल्ट मार्ग छोड़कर), तो $request->route को null माना जाएगा
        // मान लो कि ब्राउज़र पता /user/111 पर है, तो निम्नलिखित जानकारी प्रिंट होगी
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **ध्यान दें**
> `$route->param()` मेथड की आवश्यकता webman-framework >= 1.3.16 की है


## मध्यवर्ती में असामान्य को प्राप्त करना
> **ध्यान दें**
> webman-framework >= 1.3.15 की आवश्यकता है

व्यवसाय प्रक्रिया के दौरान असामान्यता हो सकती है, मध्यवर्ती में `$response->exception()` का उपयोग करके असामान्यता प्राप्त करें।

**मार्ग कॉन्फ़िगरेशन**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**मध्यवर्ती:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## सुपर ग्लोबल मध्यवर्ती

> **ध्यान दें**
> यह सुविधा webman-framework >= 1.5.16 की आवश्यकता है

मुख्य परियोजना के ग्लोबल मध्यवर्ती केवल मुख्य परियोजना को प्रभावित करती है, यह किसी भी [अनुप्रयोग प्लगइन](app/app.md) पर प्रभाव नहीं डालती। कभी-कभी हमें एक सुपर ग्लोबल मध्यवर्ती जो सभी प्लगइन को समाविष्ट करती हो चाहिए, तो हम सुपर ग्लोबल मध्यवर्ती का उपयोग कर सकते हैं।

`config/middleware.php` में निम्नलिखित को कॉन्फ़िगर करें:
```php
return [
    '@' => [ // मुख्य परियोजना और सभी प्लगइन के लिए ग्लोबल मध्यवर्ती जोड़ें
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // केवल मुख्य परियोजना को ग्लोबल मध्यवर्ती जोड़ें
];
```

> **सुझाव**
> `@` सुपर ग्लोबल मध्यवर्ती केवल मुख्य परियोजना में ही कॉन्फ़िगर किया जा सकता है, प्लगइन/ai/config/middleware.php के रूप में यह कॉन्फ़िगर किया जा सकता है, तो यह मुख्य परियोजना और सभी प्लगइनों को प्रभावित करेगा।

## किसी प्लगइन में मध्यवर्ती जोड़ें

> **ध्यान दें**
> यह सुविधा webman-framework >= 1.5.16 की आवश्यकता है

कुछ समय हम किसी [अनुप्रयोग प्लगइन](app/app.md) में एक मध्यवर्ती जोड़ना चाहते हैं, और उसके कोड को बदलना नहीं चाहते हैं (क्योंकि अपग्रेड से उसे ओवरराइड कर दिया जाएगा), इस स्थिति में हम मुख्य परियोजना में उसके लिए मध्यवर्ती कॉन्फ़िगर कर सकते हैं।

`config/middleware.php` में निम्नलिखित को कॉन्फ़िगर करें:
```php
return [
    'plugin.ai' => [], // ai प्लगइन को मध्यवर्ती जोड़ें
    'plugin.ai.admin' => [], // ai प्लगइन के व्यवस्थापक मॉड्यूल में मध्यवर्ती जोड़ें
];
```

> **सुझाव**
> यह भी संभव है कि किसी प्लगइन में इससे प्रभावित होने के लिए एक समान विन्यास जोड़ा जा सकता है, उदाहरण के लिए, `plugin/foo/config/middleware.php` में ऊपर की जानकारी जोड़ें, तो यह ai प्लगइन को प्रभावित करेगा।
