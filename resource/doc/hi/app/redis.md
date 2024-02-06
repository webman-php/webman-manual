# रेडिस
रेडिस का उपयोग डेटाबेस की तरह होता है, उदाहरण के लिए `plugin/foo/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
उपयोग करते समय
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

उसी तरह, यदि प्राथमिक परियोजना के रेडिस कॉन्फ़िगरेशन का पुनः उपयोग करना चाहते हैं
```php
use support\Redis;
Redis::get('key');
// मान लीजिए कि प्रमुख परियोजना ने एक कैश कनेक्शन भी विन्यस्त किया है
Redis::connection('cache')->get('key');
```
