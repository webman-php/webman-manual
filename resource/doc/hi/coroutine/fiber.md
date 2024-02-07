# को-रूटीन

> **को-रूटीन आवश्यकताएं**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman अपग्रेड कमांड `composer require workerman/webman-framework ^1.5.0`
> workerman अपग्रेड कमांड `composer require workerman/workerman ^5.0.0`
> फाइबर को-रूटीन को इंस्टॉल करने के लिए `composer require revolt/event-loop ^1.0.0`

# उदाहरण
### विलंबित प्रतिक्रिया

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 सेकंड की नींद
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` PHP की डिफ़ॉल्ट `sleep()` फ़ंक्शन के तरह है, अंतर यह है कि `Timer::sleep()` प्रक्रिया को ब्लॉक नहीं करेगा।


### HTTP अनुरोध प्रारंभ करें

> **ध्यान दें**
> इंस्टॉल करने के लिए यहां क्लिक करें `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // इसके लिए संर्चनात्मक तरीके से असमर्थ अनुरोध प्रारंभ करें
        return $response->getBody()->getContents();
    }
}
```
इसी तरह का `$client->get('http://example.com')` अनुरोध ब्लॉक नहीं होता है, यह वेबमन में असमर्थता का लिए किया जा सकता है।

और जानकारी के लिए[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) देखें।

### सपोर्ट/संदर्भ सहित वस्तुओं को जोड़ें

`support\Context` वस्तु, जो अनुरोध संदर्भ डेटा को संग्रहीत करने के लिए उपयोग किया जाता है, जब अनुरोध पूर्ण होता है, तो संबंधित संदर्भ डेटा स्वचालित रूप से हटा दिया जाएगा। यानी की संदर्भ डेटा का जीवनकाल अनुरोध के जीवनकाल के साथ है। `support\Context` फाइबर, स्वोल, स्वौव को-रूटीन वातावरण का समर्थन करता है।



### स्वोल को-रूटीन
स्वोल एक्सटेंशन इंस्टॉल करें (स्वोल>=5.0 की आवश्यकता है) के बाद, `config/server.php` को कॉन्फ़िगर करके स्वोल को-रूटीन को शुरू करें
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

और जानकारी के लिए[workerman इवेंट-लूप](https://www.workerman.net/doc/workerman/appendices/event.html) देखें।

### वैश्विक चर दूषण

को-रूटीन परिवेश में **अनुरोध संबंधित** स्थिति सूचनाएँ को वैश्विक चर या स्थिर चर में स्टोर करने पर प्रतिबंध लगाया जाना चाहिए, क्योंकि यह वैश्विक चर दूषण का कारण बन सकता है, जैसे

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

प्रक्रिया की संख्या को 1 पर सेट करें, जब हम लगातार दो अनुरोध करते हैं
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei  
हमें यह उम्मीद है कि दोनों अनुरोधों के परिणाम अलग-अलग होंगे 
`lilei` और `hanmeimei`, लेकिन वास्तव में वापसी कोई भी `hanmeimei` है।
यह इसलिए है क्योंकि दूसरे अनुरोध ने स्थिर चर `$name` को ओवरराइड कर दिया है, पहले अनुरोध नींद समाप्त होते ही स्थिर चर `$name` पहले से ही `hanmeimei` हो जाता है।

**सही तरीके से करना चाहिए अनुरोध की स्थिति डेटा को संग्रहीत करने के लिए संदर्भ का प्रयोग करना चाहिए**
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

**स्थानीय चर डेटा दूषण का कारण नहीं होता**
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
क्योंकि `$name` स्थानीय चर है, को-रूटीनों के बीच स्थानीय चर के बीच आपसी पहुंच नहीं होती है, इसलिए स्थानीय चर का उपयोग करना भरोसेमंद है।

# को-रूटीन के बारे में
को-रूटीन सोना नहीं है, को-रूटीन लाने का मतलब है कि ग्लोबल चर/स्थिर चर दूषण समस्या पर ध्यान देना, संदर्भ अवस्था को सेट करना। इसके अलावा, को-रूटीन संदर्भ में बग खोजना ब्लॉकिंग प्रोग्रामिंग से कठिन होता है।

webman ब्लॉकिंग प्रोग्रामिंग वास्तव में पर्याप्त तेज़ है, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) ने तीन साल की तैयारियों में webman ब्लॉकिंग प्रोग्रामिंग पर डेटाबेस व्यवसाय की तुलना में गो वेब फ्रेमवर्क जिन, इको आदि की तेजी को लगभग दोगुना बढ़ते हुए देखा है।
![](../../assets/img/benchemarks-go-sw.png?)



जब डेटाबेस, रेडिस सभी इनट्रानेट में होते हैं, तो बहुत से प्रक्रियाओं वेबराह ब्लॉकिंग प्रोग्रामिंग की गति को बहतर ढंग पर बढ़ाते हैं, संविदान तैयारियों और नियंत्रण की खर्च कोड़ी से बड़ी है, जबकि ऐसा करते हुए कुशलता ज्यादा हो सकती है, इसलिए इस हालत में को-रूटीन न कारगर नहीं होता है।

# कब को-रूटीन प्रयोग करना
जब किसी व्यापार में धीमी पहुंच होती है, जैसे व्यापार को तीसरे पक्षीय एपीआई की यातायात की आवश्यकता होती है, अभ्यास[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) को-रूटीन पद्धति के निर्देश देने से अनुरोध की असांगठितता बढ़ाने हेतु योग्य होता है।
