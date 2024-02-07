# रेडिस
रेडिस का उपयोग डेटाबेस के तरीके से होता है, उदाहरण के लिए `plugin/foo/config/redis.php`

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

इसी तरह, यदि प्रमुख परियोजना के रेडिस कॉन्फ़िगरेशन का पुनर्चक्रण करना चाहते हैं
```php
use support\Redis;
Redis::get('key');
// मान लीजिए प्रमुख परियोजना ने cache कनेक्शन भी कॉन्फ़िगर किया है
Redis::connection('cache')->get('key');
```
