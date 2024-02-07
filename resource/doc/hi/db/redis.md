# रेडिस

webman का रेडिस कॉंपोनेन्ट डिफ़ॉल्ट रूप से [illuminate/redis](https://github.com/illuminate/redis) का उपयोग करता है, यहाँ तक कि लारावेल का रेडिस लाइब्रेरी, जिसे लारावेल का रेडिस लाइब्रेरी कहा जाता है, इसका उपयोग लारावेल के साथ किया जाता है।

`illuminate/redis` का उपयोग करने से पहले `php-cli` में रेडिस एक्सटेंशन को इंस्टॉल करना आवश्यक है।

> **ध्यान दें**
> `php -m | grep redis` कमांड का उपयोग करके देखें कि `php-cli` में रेडिस एक्सटेंशन इंस्टॉल है या नहीं। ध्यान दें: यदि आपने `php-fpm` में रेडिस एक्सटेंशन इंस्टॉल कर लिया है, तो यह निश्चित नहीं है कि आप `php-cli` में उसका उपयोग कर पाएंगे, क्योंकि `php-cli` और `php-fpm` अलग-अलग एप्लीकेशन्स हैं, जो संभावतः अलग-अलग `php.ini` कॉन्फ़िगरेशन का उपयोग करते हैं। आप अपने `php-cli` द्वारा किस `php.ini` कॉन्फ़िगरेशन फ़ाइल का उपयोग किया जा रहा है यह देखने के लिए `php --ini` कमांड का उपयोग करें।

## इंस्टॉलेशन

```php
composer require -W illuminate/redis illuminate/events
```

इंस्टॉलेशन के बाद रिस्टार्ट करना आवश्यक है (रिलोड कार्यरत नहीं होगा)

## कॉन्फ़िगरेशन
रेडिस कॉन्फ़िगरेशन फ़ाइल `config/redis.php` में होता है
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

## रेडिस एपीआई
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
समर्थन के रूप में
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
> `Redis::select($db)` एपीआई का सावधानीपूर्वक उपयोग करें, क्योंकि वेबमैन बकाया मेमोरी वाला फ़्रेमवर्क है, अगर किसी एक अनुरोध ने `Redis::select($db)` का उपयोग करके डेटाबेस को स्विच किया तो यह आगे आने वाले अन्य अनुरोधों पर प्रभाव डाल सकता है। मल्टी डेटाबेस के लिए सुझाव दिया जाता है कि विभिन्न `db` कोन्फ़िगरेशन को अलग-अलग रेडिस कनेक्शन कॉन्फ़िगरेशन में कॉन्फ़िगर किया जाए।

## एक से अधिक रेडिस कनेक्शन का उपयोग करना
उदाहरण के लिए कॉन्फ़िगरेशन फ़ाइल `config/redis.php` में
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
यहाँ उपयोग हो रहा है `default` अनुस्मारक का उपयोग सबसे पहले जोड़ी गयी कनेक्शन का करता है, आप `Redis::connection()` मेथड का उपयोग करके चुन सकते हैं कि कौन सा रेडिस कनेक्शन उपयोग करना है।
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## समूह कॉन्फ़िगरेशन
यदि आपके एप्लिकेशन में रेडिस सर्वर समूह का उपयोग हो रहा है, तो आपको रेडिस कॉन्फ़िगरेशन फ़ाइल में इन समूहों को परिभाषित करने के लिए clusters की कुंजी का उपयोग करना चाहिए:
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

डिफ़ॉल्ट रूप में, समूह क्लाइंट फ्रेगमेंटेर्शन को नोडस पर लागू किया जा सकता है, जिससे आप नोड पूल को अमलानुय कर सकते हैं और बड़ी मात्रा में योग्य मेमोरी बना सकते हैं। यहाँ द्यान रहे, क्लाइंट षेयरिंग नहीं असफलताओं को नहीं हैंडल करेगा; इसलिए, यह सुविधा मुख्य रूप से दूसरे प्रमुख डेटाबेस से कैश डेटा प्राप्त करने के लिए योग्य है। यदि Redis मूल समूह का उपयोग करना है, तो आपको कॉन्फ़िगरेशन फ़ाइल के नीचे की दिशा में निम्नलिखित करना होगा:

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

## पाइपलाइन आदेश
जब आपको सर्वर पर बहुत सारे आदेश भेजने की आवश्यकता होती है, तो आपको पाइपलाइन आदेश का उपयोग करना सुझाया जाता है। पाइपलाइन मेथड एक रेडिस इंस्टेंस के एक क्लोजर को स्वीकार करता है। आप सभी आदेश रेडिस इंस्टेंस को भेज सकते हैं, और वे सभी एक ही कार्य के अंदर पूरा हो जाएँगे:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
