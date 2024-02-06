# कोरूटीन

> **कोरूटीन की आवश्यकता**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman अपग्रेड कमांड `composer require workerman/webman-framework ^1.5.0`
> workerman अपग्रेड कमांड `composer require workerman/workerman ^5.0.0`
> फाइबर कोरूटीन स्थापित करने के लिए `composer require revolt/event-loop ^1.0.0` इंस्टॉल करना होगा।

# उदाहरण
### देर से प्रतिक्रिया

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 सेकंड के लिए सोते रहो
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` PHP के निर्मित `sleep()` फ़ंक्शन के लिए समान है, अंतर यह है कि `Timer::sleep()` प्रक्रिया को ब्लॉक नहीं करेगा।


### HTTP अनुरोध प्रारंभ करें

> **ध्यान दें**
> इसकी आवश्यकता है कि `composer require workerman/http-client ^2.0.0` इंस्टॉल करना होगा।

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // असमंगत तरीके से अनुरोध प्रारंभ करें
        return $response->getBody()->getContents();
    }
}
```
इसी तरह से, `$client->get('http://example.com')` का अनुरोध असमंगत है, जो कि webman में असमंगत रूप से HTTP अनुरोध प्रारंभ करने के लिए इस्तेमाल किया जा सकता है, और कार्यक्षमता को बढ़ा सकता है।

और जानकारी के लिए देखें [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### समर्थन के संदर्भ की रचना को बढ़ाएं

`support\Context` वर्ग का उपयोग अनुरोध संदर्भ डेटा थोक करने के लिए किया जाता है, जब अनुरोध पूरा हो जाता है, तो संबंधित संदर्भ डेटा स्वचालित रूप से हटा दिया जाता है। यानी, संदर्भ डेटा की जीवनकाल संपूर्णतया संदर्भ जीवनकाल के अनुसार होती है। `support\Context` Fiber, Swoole, Swow कोरूटीन के वातावरण का समर्थन करता है।



### स्वोले कोरूटीन
स्वोले एक्सटेंशन स्थापित करने के बाद (स्वोले>=5.0 की आवश्यकता है), `config/server.php` को व्यवस्थित करके स्वोले कोरूटीन को सक्षम करें
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

और जानकारी के लिए देखें [workerman घटनाओं पर आधारित](https://www.workerman.net/doc/workerman/appendices/event.html)

### सर्वव्यापी मान प्रदूषण

कोरूटीन परिवेश **अनुरोध संबंधित** स्थिति जानकारी को सर्वव्यापी या स्थैतिक मान में संग्रहित करने की अनुमति नहीं देता है, क्योंकि यह संभावना है कि वह सर्वव्यापी मान प्रदूषण के कारण अद्यतन हो सकते हैं, उदाहरण के लिए

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

प्रक्रियाओं की संख्या को 1 के रूप में सेट करें, जब हम दो लगातार अनुरोध प्रारंभ करते हैं  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
हमें दोनों अनुरोधों का परिणाम अपेक्षित रूप से `lilei` और `hanmeimei` के रूप में होने की उम्मीद है, लेकिन वास्तविकता में वे दोनों `hanmeimei` हैं।
इसका कारण यह है कि दूसरा अनुरोध स्थिरता चर मान `$name` को अधिग्रहित कर चुका है, पहला अनुरोध सोता हुआ समाप्त होने पर $name वास्तविकता में `hanmeimei` रूप में हो चुका होता है।

**सही तरीके से यह होना चाहिए कि अनुरोध स्थिति डेटा को संदर्भ में संग्रहित करें**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**लोकल वेरिएबल से डेटा प्रदूषण नहीं होगा**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
क्योंकि `$name` स्थानीय वेरिएबल है, कोरूटीन के बीच डेटा प्रदूषण नहीं होती, इसलिए स्थानीय वेरिएबल का उपयोग करना कोरूटीन सुरक्षित है।

# कोरूटीन के बारे में
कोरूटीन को सिल्वर बुलेट नहीं है, कोरूटीन का प्रस्तावना संकेत करता है कि समर्थन मान/स्थिर मान प्रदूषण समस्या पर ध्यान दिया जाना चाहिए, संदर्भ सेट करने की आवश्यकता होती है। साथ ही कोरूटीन परिवेश में बीग निकासी ब्लॉक शैली की तुलना में थोड़ी कठिन होती है।

वेबमैन ब्लॉक शैली के प्रोग्रामिंग से वास्तव में काफी तेज होता है, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) के अनुसार, तीन साल के तीन परिक्षणों द्वारा वेबमेन ब्लॉक शैली डेटाबेस व्यावसायिकता के साथ जिन, इको इत्यादि वेब फ्रेमवर्क के साथ तुलना करने पर प्रदर्शन के संबंध में लगभग 1 गुना अधिक है।
![](../../assets/img/benchemarks-go-sw.png?)

जब डेटाबेस, रेडिस आदि सभी नेटवर्क में होते हैं, तो सामान्यत: कई प्रक्रियाओं में ब्लॉक शैली प्रोग्रामिंग की दक्षता को कोरूटीन नहीं उत्पन्न करता है, क्योंकि डेटाबेस, रेडिस आदि काफी तेज होने पर, कोरूटीन बनाने, अनुस्चयन बनाने, समाप्त करने की लागत संभावना से अधिक है, इसलिए इस समय कोरूटीन को लाने से प्रदर्शन को महत्वपूर्ण रूप से बढ़ाने की क्षमता नहीं है।

# कब कोरूटीन का उपयोग करें
जब व्यावसायिकता में धीमी पहुँच होती है, उदाहरण के लिए व्यावसायिकता को तीसरी पक्ष नेटवर्क की व्यवस्था को अधिग्रहण करने की आवश्यकता होती है, तो [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) का उपयोग करके असमंगत ढंग से असमंगत HTTP कॉल को शुरू करने से सुदृढ़ कार्यक्षमता को बढ़ाया जा सकता है।
