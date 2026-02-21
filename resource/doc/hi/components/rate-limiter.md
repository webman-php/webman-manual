# दर सीमित्र

webman दर सीमित्र, एनोटेशन आधारित सीमा समर्थन।
apcu, redis और memory ड्राइवर समर्थन करता है।

## स्रोत कोड

https://github.com/webman-php/limiter

## स्थापना

```
composer require webman/limiter
```

## उपयोग

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // डिफ़ॉल्ट IP आधारित सीमा, डिफ़ॉल्ट समय विंडो 1 सेकंड
        return 'प्रति IP प्रति सेकंड अधिकतम 10 अनुरोध';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, उपयोगकर्ता ID के अनुसार सीमा, session('user.id') खाली नहीं होना चाहिए
        return 'प्रति उपयोगकर्ता प्रति 60 सेकंड अधिकतम 100 खोज';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'प्रति व्यक्ति प्रति मिनट केवल 1 ईमेल')]
    public function sendMail(): string
    {
        // key: Limit::SID, session_id के अनुसार सीमा
        return 'ईमेल सफलतापूर्वक भेजा गया';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'आज के कूपन समाप्त, कल पुनः प्रयास करें')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'प्रत्येक उपयोगकर्ता प्रति दिन केवल एक कूपन प्राप्त कर सकता है')]
    public function coupon(): string
    {
        // key: 'coupon', वैश्विक सीमा के लिए कस्टम कुंजी, प्रति दिन अधिकतम 100 कूपन
        // उपयोगकर्ता ID के अनुसार भी सीमा, प्रत्येक उपयोगकर्ता प्रति दिन एक कूपन
        return 'कूपन सफलतापूर्वक भेजा गया';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'प्रति नंबर प्रति दिन अधिकतम 5 एसएमएस')]
    public function sendSms2(): string
    {
        // जब key चर है: [क्लास, स्टैटिक_मेथड], जैसे [UserController::class, 'getMobile'] UserController::getMobile() रिटर्न मान को कुंजी के रूप में उपयोग करता है
        return 'एसएमएस सफलतापूर्वक भेजा गया';
    }

    /**
     * कस्टम कुंजी, मोबाइल नंबर प्राप्त करें, स्टैटिक मेथड होना चाहिए
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'दर सीमित', exception: RuntimeException::class)]
    public function testException(): string
    {
        // सीमा पार करने पर डिफ़ॉल्ट अपवाद: support\limiter\RateLimitException, exception पैरामीटर से बदला जा सकता है
        return 'ok';
    }

}
```

**नोट**

* फिक्स्ड विंडो एल्गोरिदम उपयोग करता है
* डिफ़ॉल्ट ttl समय विंडो: 1 सेकंड
* ttl के माध्यम से विंडो सेट करें, जैसे `ttl:60` 60 सेकंड के लिए
* डिफ़ॉल्ट सीमा आयाम: IP (डिफ़ॉल्ट `127.0.0.1` सीमित नहीं, नीचे कॉन्फ़िगरेशन देखें)
* अंतर्निहित: IP, UID (`session('user.id')` खाली नहीं होना चाहिए), SID (`session_id` के अनुसार) सीमा
* nginx प्रॉक्सी उपयोग करते समय IP सीमा के लिए `X-Forwarded-For` हेडर पास करें, देखें [nginx proxy](../others/nginx-proxy.md)
* सीमा पार करने पर `support\limiter\RateLimitException` ट्रिगर करता है, `exception:xx` से कस्टम अपवाद क्लास
* सीमा पार करने पर डिफ़ॉल्ट त्रुटि संदेश: `Too Many Requests`, `message:xx` से कस्टम संदेश
* डिफ़ॉल्ट त्रुटि संदेश [अनुवाद](translation.md) से भी बदला जा सकता है, Linux संदर्भ:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

कभी-कभी डेवलपर कोड में सीमित्र को सीधे कॉल करना चाहते हैं, निम्न उदाहरण देखें:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // यहाँ mobile कुंजी के रूप में उपयोग होता है
        Limiter::check($mobile, 5, 24*60*60, 'प्रति नंबर प्रति दिन अधिकतम 5 एसएमएस');
        return 'एसएमएस सफलतापूर्वक भेजा गया';
    }
}
```

## कॉन्फ़िगरेशन

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // ये IP सीमित नहीं हैं (केवल key Limit::IP होने पर प्रभावी)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: दर सीमा सक्षम करें
* **driver**: `auto`, `apcu`, `memory`, `redis` में से एक; `auto` स्वचालित रूप से `apcu` (प्राथमिकता) और `memory` के बीच चुनता है
* **stores**: Redis कॉन्फ़िगरेशन, `connection` `config/redis.php` में कुंजी से मेल खाता है
* **ip_whitelist**: Whitelist में IP सीमित नहीं हैं (केवल key `Limit::IP` होने पर प्रभावी)

## ड्राइवर चयन

**memory**

* परिचय
  कोई एक्सटेंशन आवश्यक नहीं, सर्वोत्तम प्रदर्शन।

* सीमाएं
  सीमा केवल वर्तमान प्रक्रिया के लिए प्रभावी, प्रक्रियाओं के बीच डेटा साझा नहीं, क्लस्टर सीमा समर्थित नहीं।

* उपयोग के मामले
  Windows विकास वातावरण; कड़ी सीमा की आवश्यकता नहीं वाले व्यवसाय; CC हमले से रक्षा।

**apcu**

* एक्सटेंशन स्थापना
  apcu एक्सटेंशन आवश्यक, php.ini सेटिंग्स:

```
apc.enabled=1
apc.enable_cli=1
```

php.ini स्थान `php --ini` से खोजें

* परिचय
  बहुत अच्छा प्रदर्शन, मल्टी-प्रोसेस शेयर समर्थन करता है।

* सीमाएं
  क्लस्टर समर्थित नहीं

* उपयोग के मामले
  कोई भी विकास वातावरण; प्रोडक्शन सिंगल सर्वर सीमा; कड़ी सीमा की आवश्यकता नहीं वाला क्लस्टर; CC हमले से रक्षा।

**redis**

* निर्भरताएं
  redis एक्सटेंशन और Redis कंपोनेंट आवश्यक, स्थापना:

```
composer require -W webman/redis illuminate/events
```

* परिचय
  apcu से कम प्रदर्शन, सिंगल सर्वर और क्लस्टर सटीक सीमा समर्थन करता है

* उपयोग के मामले
  विकास वातावरण; प्रोडक्शन सिंगल सर्वर; क्लस्टर वातावरण
