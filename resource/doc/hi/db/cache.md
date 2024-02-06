# Cache

वेबमैन में [Symfony/Cache](https://github.com/symfony/cache) को कैश कंपोनेंट के रूप में डिफ़ॉल्ट रूप से उपयोग किया जाता है।

> `Symfony/Cache` का उपयोग करने से पहले, `php-cli` को redis एक्सटेंशन को स्थापित करना आवश्यक है।

## स्थापना
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

स्थापना के बाद restart पुनः आरंभ करें (रीलोड कार्यरत नहीं होगा)

## रेडिस कॉन्फ़िगरेशन
रेडिस कॉन्फ़िगरेशन फ़ाइल `config/redis.php` में होती है।
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

> **ध्यान दें**
> कुंजी को संभावना से बचाने के लिए एक प्रीफ़िक्स जोड़ें, अन्य रेडिस का उपयोग करने वाले व्यावसायिक काम के साथ टकराव से बचें।

## अन्य कैश कंपोनेंट का उपयोग
[ThinkCache](https://github.com/top-think/think-cache) कंपोनेंट का उपयोग करने के लिए देखें: [अन्य डेटाबेस](others.md#ThinkCache)
