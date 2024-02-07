# मध्यवर्ती
मध्यवर्ती आम तौर पर अनुरोध या प्रतिक्रिया को रोकने के लिए प्रयोग किया जाता है। उदाहरण के लिए, नियंत्रक को निष्पादित करने से पहले उपयोगकर्ता की पहचान की पुष्टि करना, यदि उपयोगकर्ता लॉग इन नहीं किया है तो लॉगिन पृष्ठ पर रीडायरेक्ट करना, उदाहरण के रूप में, प्रतिक्रिया में किसी विशेष हेडर को जोड़ना, ऐसे कई उदाहरण हैं।
## मिडलवेयर प्याज़ा मॉडल

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Request ───────────────────> Controller ─ Response ───────────────────────────> Client
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```

मिडलवेयर और कंट्रोलर एक विशेष प्याज़ा मॉडल का हिस्सा हैं, मिडलवेयर को लयरों की तरह प्याज़े की चमड़ी के रूप में देखा जा सकता है, और कंट्रोलर प्याज़े की मध्य भाग है। जैसा कि चित्र में दिखाया गया है, अनुरोध मिडलवेयर 1, 2, 3 के माध्यम से जाता है और कंट्रोलर तक पहुंचता है, कंट्रोलर ने एक प्रतिक्रिया भेजी, और फिर प्रतिक्रिया 3, 2, 1 के क्रम में मिडलवेयर के माध्यम से वापस आती है और अंत में उसे ग्राहक को वापस मिलती है। यानी, हर मिडलवेयर में हम न तो अनुरोध प्राप्त कर सकते हैं और न ही प्रतिक्रिया प्राप्त कर सकते हैं।
## अनुरोध रोकथाम

कभी-कभी हम चाहते हैं कि किसी अनुरोध को कंट्रोलर पर पहुंचने नहीं दे, उदाहरण के लिए हमें middleware2 में पता चलता है कि वर्तमान उपयोगकर्ता लॉग इन नहीं है, तो हम सीधे अनुरोध को रोक सकते हैं और एक लॉग इन प्रतिक्रिया वापस कर सकते हैं। इस प्रकार की प्रक्रिया नीचे वर्णित है

```
                              
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Reqeust ─────────┐     │    │    Controller    │      │      │     │
            │     │ Response │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

जैसा कि चित्र में दिखाया गया है, middleware2 तक अनुरोध पहुंचने पर लॉग इन प्रतिक्रिया उत्पन्न हुई है, जवाब middleware2 से गुजरकर middleware1 तक पहुंचती है और फिर उपयोगकर्ता को वापस भेज दी जाती है।
## मिडलवेयर इंटरफेस
मिडलवेयर को 'Webman\MiddlewareInterface' इंटरफेस को अंगीकार करना चाहिए।
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
##

यह अर्थ है कि `process` मेथड को अमल में लाना आवश्यक है, `process` मेथड को एक `support\Response` ऑब्जेक्ट लौटाना आवश्यक है, डिफ़ॉल्ट रूप से यह ऑब्जेक्ट `$handler($request)` द्वारा उत्पन्न होता है (अनुरोध को प्याज के केंद्र तक जारी रखने के लिए), यह ऑब्जेक्ट `response()` `json()` `xml()` `redirect()` आदि हेल्पर फ़ंक्शन द्वारा उत्पन्न हो सकता है (अनुरोध को प्याज के केंद्र तक रोक देने के लिए)।

## मध्यवर्ती में अनुरोध और प्रतिक्रिया प्राप्त करना
मध्यवर्ती में हम अनुरोध प्राप्त कर सकते हैं, और नियंत्रक के बाद प्रतिक्रिया भी प्राप्त कर सकते हैं, इसलिए मध्यवर्ती आंतरिक रूप से तीन भागों में बाँटा गया है।
1. अनुरोध पार करने की चरण, यानी अनुरोध प्रसंस्करण से पहले की चरण
2. नियंत्रक ने अनुरोध के प्रसंस्करण की चरण, यानी अनुरोध प्रसंस्करण की चरण
3. प्रतिक्रिया पार करने की चरण, यानी अनुरोध प्रसंस्करण के बाद की चरण

तीनों चरण मेंट का मध्यवर्ती परिभाषा निम्नलिखित है
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
        echo 'यहां अनुरोध पार करने का चरण है, यानी रिक्वेस्ट प्रोसेसिंग से पहले';
        
        $response = $handler($request); // आगे धानी में पहुंचने, नियंत्रक का अनुपालन करने तक
        
        echo 'यहां उत्तर पार करने का चरण है, यानी रिक्वेस्ट प्रोसेसिंग के बाद';
        
        return $response;
    }
}
```
कुंजीयों की प्रामाणिकता मध्यवर्ती बनाने के लिए निम्नलिखित फ़ाइल बनाएं: `app/middleware/AuthCheckTest.php` (यदि निर्देशिका मौजूद नहीं है तो कृपया स्वयं बनाएं)। निम्नलिखित है:
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
            // पहले से ही लॉग इन किया गया है, अनुरोध को परिपत्रवार करते हुए जारी रखें
            return $handler($request);
        }

        // रिफ़्लेक्शन की मदद से नियंत्रक के कौन-कौन से विधि लॉगिन की आवश्यकता नहीं है, यह जानने के लिए
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // विधि को लॉगिन की आवश्यकता है
        if (!in_array($request->action, $noNeedLogin)) {
            // अनुरोध को रोकने के लिए, एक पुनर्निर्देशन प्रतिक्रिया लौटाएं, अनुरोध परिपत्रवार में रोक दें
            return redirect('/user/login');
        }

        // लॉगिन की आवश्यकता नहीं है, अनुरोध को परिपत्रवार करते हुए जारी रखें
        return $handler($request);
    }
}
```
नया कंट्रोलर बनाएँ `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * लॉगिन की जरूरत नहीं है
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'लॉगिन ठीक है']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ठीक है', 'data' => session('user')]);
    }
}
```
##

> **ध्यान दें**
> `$noNeedLogin` में वर्तमान कंट्रोलर के लिए ऐसी विधियों को नकदी में जा सकता है, जिन्हें लॉग इन किये बिना पहुँचा जा सकता है।

`config/middleware.php` में निम्नलिखित के रूप में वृत्तीय मध्यवर्ती जोड़ें: 
```php
return [
    // वृत्तीय मध्यवर्ती
    '' => [
        // ... अन्य मध्यवर्तियों को यहाँ छोड़ दिया गया है
        app\middleware\AuthCheckTest::class,
    ]
];
```

पहचान सत्यापन मध्यवर्ती के साथ, हम कंट्रोलर तह पर व्यापार कोड लिखने में ध्यान केंद्रित कर सकते हैं, और हमें यह की चिंता करने की आवश्यकता नहीं होती कि उपयोगकर्ता क्या लॉग इन हैं या नहीं।
## उदाहरण: क्रॉस-डोमेन अनुरोध मध्यवर्ती
`app/middleware/AccessControlTest.php` नामक फ़ाइल बनाएं (यदि निर्दिष्ट निर्देशिका मौजूद नहीं है तो स्वयं बनाएं)। फ़ाइल का निम्नलिखित होना चाहिए:
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
        // यदि विकल्प अनुरोध है तो एक खाली प्रतिक्रिया लौटाएं, अन्यथा प्रोसेस जारी रखें और एक प्रतिक्रिया प्राप्त करें
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // प्रतिक्रिया में से संबंधित क्रॉस-ओरिजन हेडर जोड़ें
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
यहां सुझाव दिया गया है कि पारदर्शिता के संदर्भ में OPTIONS अनुरोध को रोका जा सकता है, जिससे कि हमारे नियंत्रक में OPTIONS अनुरोध नहीं पहुंचे। इसके लिए, हमने OPTIONS अनुरोध के लिए खाली प्रतिक्रिया (`response('')`) लौटाई है। 
अगर आपके इंटरफ़ेस को रूटिंग की आवश्यकता है, तो कृपया `Route::any(..)` या `Route::add(['POST', 'OPTIONS'], ..)` का उपयोग करें।

फ़ाइल `config/middleware.php` में निम्नलिखित प्रकार से ग्लोबल मिडलवेयर जोड़ें:
```php
return [
    // ग्लोबल मिडलवेयर
    '' => [
        // ... यहाँ अन्य मिडलवेयर को छोड़ दिया गया है
        app\middleware\AccessControlTest::class,
    ]
];
```

> **ध्यान दें**
> यदि एजैक्स (ajax) अनुरोध में हैडर (header) को अनुकूलित किया गया है, तो मध्यवर्ती (middleware) में `Access-Control-Allow-Headers` फ़ील्ड में इस स्वनिर्धारित हेडर (header) को जोड़ना चाहिए, अन्यथा `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` त्रुटि आ सकती है।
## विवरण

- मिडलवेयर को सामान्य मिडलवेयर, ऐप्लिकेशन मिडलवेयर (ऐप्लिकेशन मोड में केवल प्रभावी होता है, देखें[मल्टी ऐप](multiapp.md)) और रूटिंग मिडलवेयर में विभाजित किया जाता है।
- वर्तमान में एकल नियंत्रक के मिडलवेयर का समर्थन उपलब्ध नहीं है (लेकिन मिडलवेयर के माध्यम से `$request->controller` का मूल्यांकन करके नियंत्रक मिडलवेयर की सुविधा को लागू किया जा सकता है)।
- मिडलवेयर कॉन्फ़िगरेशन फ़ाइल की स्थिति `config/middleware.php` में होती है।
- ग्लोबल मिडलवेयर कॉन्फ़िगरेशन कुंजी `''` के तहत होती है।
- ऐप्लिकेशन मिडलवेयर कॉन्फ़िगरेशन विशिष्ट ऐप नामों के नीचे होती है, जैसे
```php
return [
    // 全局中间件
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // api एप्लिकेशन मध्यवर्ती (एप्लिकेशन मध्यवर्ती केवल बहु एप्लिकेशन मोड में प्रभावी होती है)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```
```php
<?php
उपयोग करें support\Request;
उपयोग करें Webman\Route;

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
## मिडलवेयर कंस्ट्रक्टर पैरामीटर पास

> **ध्यान दें**
> यह फ़ीचर webman-framework >= 1.4.8 आवश्यक है

1.4.8 संस्करण के बाद, कॉन्फ़िग फ़ाइल में सीधे मिडलवेयर को तंत्रीकृत करने या निष्पक्ष फ़ंक्शन के माध्यम से पैरामीटर पास करने का समर्थन है, जिससे कंस्ट्रक्टर के माध्यम से मिडलवेयर को पैरामीटर पास करना सरल हो जाता है।
उदाहरण के लिए `config/middleware.php` में निम्नलिखित तरीके से कॉन्फ़िगर किया जा सकता है।

```php
return [
    // ग्लोबल मिडलवेयर
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // एपीआई ऐप्लिकेशन मिडलवेयर (मल्टी ऐप्लिकेशन मोड में केवल ऐप्लिकेशन मिडलवेयर प्रभावी होता है)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```
समान रूप से, रूट मध्यवर्ती को पैरामीटर पाठक के माध्यम से पास किया जा सकता है, उदाहरण के लिए `config/route.php` में
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```
## मिडलवेयर क्रम
- मिडलवेयर क्रम होता है `ग्लोबल मिडलवेयर` -> `एप्लीकेशन मिडलवेयर` -> `राउट मिडलवेयर`।
- यदि कई ग्लोबल मिडलवेयर हैं, तो उन्हें मिडलवेयर की वास्तविक कॉन्फ़िगरेशन क्रम के अनुसार चलाया जाएगा (एप्लीकेशन मिडलवेयर, राउट मिडलवेयर भी इसी तरह)।
- 404 अनुरोध किसी भी मिडलवेयर को ट्रिगर नहीं करेगा, सहित ग्लोबल मिडलवेयर को।

## रूटिंग मेंद्वार को पैरामीटर पास करना (रूट->setParams)

**रूट कॉन्फ़िगरेशन `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```
## मध्यवर्ती (यदि सभी मध्यवर्ती के रूप में मान लिया जाए)

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
        // डिफ़ॉल्ट रूट $request->route के लिए null है, इसलिए $request->route को खाली है या नहीं जांचने की आवश्यकता है
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## मिडलवेयर से कंट्रोलर को पैरामीटर पास करना

कभी-कभी कंट्रोलर को मिडलवेयर द्वारा उत्पन्न डेटा का उपयोग करने की आवश्यकता होती है, इस स्थिति में हम `$request` ऑब्जेक्ट में एट्रिब्यूट को जोड़कर कंट्रोलर को पैरामीटर के रूप में पास कर सकते हैं। उदाहरण के लिए:

**मिडलवेयर**

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
        $request->data = 'some value';
        return $handler($request);
    }
}
```

##


**कंट्रोलर：**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## मिडलवेयर से वर्तमान अनुरोध रूटिंग जानकारी प्राप्त करना
> **ध्यान दें**
> webman-framework >= 1.3.2 की आवश्यकता होगी

हम `$request->route` का उपयोग करके रूटिंग ऑब्जेक्ट प्राप्त कर सकते हैं, और उसके मेथड को कॉल करके संबंधित जानकारी प्राप्त कर सकते हैं।

##

**रूटिंग कॉन्फ़िगरेशन**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```
##

**मिडलवेयर**
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
        // यदि अनुरोध किसी भी मार्ग से मेल नहीं खाता है (डिफ़ॉल्ट मार्ग छोड़कर), तो $request->route का मान शून्य होगा
        // मान लें कि ब्राउज़र पता /user/111 को ब्राउज़ करता है, तो निम्नलिखित जानकारी प्रिंट होगी
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
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
> `$route->param()` विधि के लिए webman-framework >= 1.3.16 आवश्यक है।

## मध्यमाध्यम प्राप्ति असामान्यता
> **ध्यान दें**
> webman-framework >= 1.3.15 आवश्यक है

व्यावसायिक प्रसंस्करण के दौरान असामान्यता उत्पन्न हो सकती है, मध्यवर्ती में `$response->exception()` का उपयोग करके असामान्यता प्राप्त की जा सकती हैं।

**मार्ग कॉन्फ़िगरेशन**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```
##

**मिडलवेयर:**
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
## सुपर ग्लोबल मिडलवेयर

> **ध्यान दें**
> यह सुविधा webman-framework >= 1.5.16 की आवश्यकता है।

मुख्य परियोजना का ग्लोबल मिडलवेयर केवल मुख्य परियोजना पर प्रभाव डालता है, वह ऐप प्लगइन्स पर कोई प्रभाव नहीं डालेगा। कभी-कभी हमें एक ऐसा मिडलवेयर जो सभी प्लगइन्स को समाहित करने वाला हो, चाहिए होता है। इस स्थिति में हम सुपर ग्लोबल मिडलवेयर का उपयोग कर सकते हैं।
##

कृपया `config/middleware.php` में निम्नलिखित कॉन्फ़िगर करें:
```php
return [
    '@' => [ // मुख्य परियोजना और सभी प्लगइन्स में ग्लोबल मध्यवर्ती जोड़ें
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // केवल मुख्य परियोजना में ग्लोबल मध्यवर्ती जोड़ें
];
```

##

> **सुझाव**
>`@` सुपर ग्लोबल मध्यवर्ती केवल मुख्य परियोजना में ही नहीं, बल्कि किसी प्लगइन में भी कॉन्फ़िगर किया जा सकता है, उदाहरण के लिए, `plugin/ai/config/middleware.php` में `@` सुपर ग्लोबल मध्यवर्ती कॉन्फ़िगर किया है, तो यह मुख्य परियोजना और सभी प्लगइन्स पर भी प्रभाव डालेगा।
## एक प्लगइन में मिडलवेयर जोड़ना

> **ध्यान दें**
> यह विशेषता webman-framework >= 1.5.16 की आवश्यकता है।

कभी-कभी हम किसी [एप्लिकेशन प्लगइन](app/app.md) में एक मिडलवेयर जोड़ना चाहते हैं, लेकिन प्लगइन कोड में परिवर्तन नहीं करना चाहते (क्योंकि अपग्रेड करने पर यह अधिग्रहण किया जाएगा), इस स्थिति में हम इसे मुख्य प्रोजेक्ट में मिडलवेयर की विन्यास दे सकते हैं।

`config/middleware.php` में निम्नलिखित कॉन्फ़िगर करें:
```php
return [
    'plugin.ai' => [], // ai प्लगइन में मिडलवेयर जोड़ें
    'plugin.ai.admin' => [], // ai प्लगइन के व्यवस्थापक मॉड्यूल में मिडलवेयर जोड़ें
];
```

> **सुझाव**
> बेशक आप इस तरह की कॉन्फ़िगरेशन को किसी अन्य प्लगइन पर असर डालने के लिए किसी प्लगइन में भी जोड़ सकते हैं, जैसे कि `plugin/foo/config/middleware.php` में उपरोक्त कॉन्फ़िगरेशन जोड़ें, तो यह ai प्लगइन पर असर डालेगा।
