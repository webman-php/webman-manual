# रेडिस

webman का रेडिस component डिफ़ॉल्ट रूप से [illuminate/redis](https://github.com/illuminate/redis) का उपयोग करता है, जो कि लारावेल का रेडिस लाइब्रेरी है, और इसका उपयोग लारावेल के साथ समान है।

`illuminate/redis` का उपयोग करने से पहले `php-cli` पर रेडिस extension को install करना आवश्यक है।

> **ध्यान दें**
> `php-cli` पर रेडिस extension install है या नहीं की जाँच के लिए `php -m | grep redis` कमांड का उपयोग करें। ध्यान दें: यदि आपने `php-fpm` पर रेडिस extension install किया है, तो यह बिल्कुल निश्चित नहीं है की आप `php-cli` पर उसका उपयोग कर सकते हैं, क्योंकि `php-cli` और `php-fpm` अलग-अलग एप्लीकेशन हैं, जो शायद अलग-अलग `php.ini` कॉन्फ़िगरेशन का उपयोग करते हैं। आप अपनी `php-cli` द्वारा किस `php.ini` कॉन्फ़िगरेशन फ़ाइल का उपयोग कर रहें हैं, इसकी जाँच के लिए कमांड `php --ini` का उपयोग करें।

## इंस्टॉलेशन

```php
कंज़ोल पर निम्नलिखित कमांड चलाएं
composer require -W illuminate/redis illuminate/events
```

इंस्टॉलेशन के बाद restart या फिर रीलोड(non-effective) की आवश्यकता है।

## कॉन्फ़िगरेशन
रेडिस कॉन्फ़िग फ़ाइल में `config/redis.php` 
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## उदाहरण
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## रेडिस इंटरफेस
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
यही का संबंधित कोड 
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **ध्यान दें**
> सावधानी बरतें `Redis::select($db)` इंटरफेस का उपयोग करते समय, webman एक परजीवी मेमोरी फ़्रेमवर्क है, इसलिए यदि किसी अनुरोध ने `Redis::select($db)` का उपयोग करके डाटाबेस को switch किया है तो इससे आने वाले अन्य अनुरोधों पर असर पड़ सकता है। अगर आपको अलग-अलग डेटाबेस का उपयोग करना है, तो आपको अलग-अलग `db` को Redis कनेक्शन कॉन्फ़िगरेशन में अलग-अलग कनेक्सन कॉन्फ़िगरेशन में अलग-अलग प्रारंभ करना चाहिए।

## एक से अधिक रेडिस कनेक्शन का उपयोग
उदाहरण के लिए कॉन्फ़िग फ़ाइल `config/redis.php` 
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
डिफ़ॉल्ट रूप से `default` कनेक्शन का इस्तेमाल होता है, आप `Redis::connection()` फ़ंक्शन का इस्तेमाल करके बता सकते हैं की आप किस रेडिस कनेक्शन का उपयोग करना चाहते हैं।
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## समूह कॉन्फ़िगरेशन
अगर आपका एप्लिकेशन रेडिस सर्वर समूह का उपयोग करती है, तो आपको रेडिस कॉन्फ़िग फ़ाइल में clusters की कुंजी का उपयोग करें।
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

डिफ़ॉल्ट रूप से, समूह नोड्स पर क्लाइंट शार्डिंग कर सकते हैं, इससे आप नोड पूल बना सकते हैं और बड़े पैमाने पर मेमोरी उपयोग कर सकते हैं। ध्यान रहे, क्लाइंट साझा होने के बावजूद यह सुनिश्चित नहीं करता है कि न कामी के स्थिति को संभाला जाए; इसलिए, यह सुविधा मुख्य रूप से अन्य मुख्य डेटाबेस से प्राप्त करने के लिए उपयोगी है। यदि आप रेडिस की नेटिव समूह का उपयोग करना चाहते हैं, तो आपको कॉन्फ़िग फ़ाइल के नीचे के options की कुंजी में निम्न रूप से निर्दिष्ट करना चाहिए:

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## पाइपलाइन कमांड
जब आपको सर्वर पर कई कमांड भेजने की जरूरत होती है, तो आपको पाइपलाइन कमांड का उपयोग करना चाहिए। पाइपलाइन मेथड एक रेडिस इंस्टेंस की तालिका को मानक मानकों द्वारा लेती है। आप रेडिस इंस्टेंस को सभी कमांड भेज सकते हैं, इससे वे सभी कमांड एक ही ऑपरेशन के भीतर पूर्ण हो जाएंगे:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
