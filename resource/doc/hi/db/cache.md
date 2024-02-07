# कैश

webman में डिफ़ॉल्ट रूप से [symfony/cache](https://github.com/symfony/cache) को कैश के कंपोनेंट के रूप में इस्तेमाल किया जाता है।

> `symfony/cache` का उपयोग करने से पहले `php-cli` को रेडिस एक्सटेंशन इंस्टॉल करना आवश्यक है।

## स्थापना
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

स्थापना के बाद रिस्टार्ट (रीलोड काम नहीं करेगा)

## रेडिस कॉन्फ़िगरेशन
रेडिस कॉन्फ़िगरेशन फ़ाइल `config/redis.php` में होती है
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
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **नोट:**
> की और चुरा लिखें, रेडिस का उपयोग करने वाले अन्य व्यापार से टकराव से बचें

## दूसरे कैश कंपोनेंट का उपयोग
[ThinkCache](https://github.com/top-think/think-cache) कंपोनेंट का उपयोग देखने के लिए [दूसरे डेटाबेस](others.md#ThinkCache) का संदर्भ लें
