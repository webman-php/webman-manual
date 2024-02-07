# ক্যাশ

webman ডিফল্টভাবে [symfony/cache](https://github.com/symfony/cache)কে ক্যাশ কম্পোনেন্ট হিসেবে ব্যবহার করে।

> `symfony/cache` ব্যবহার করতে আগে প্রোগ্রামের জন্য `php-cli` এর redis এক্সটেনশন ইন্সটল করতে হবে।

## ইন্সটলেশন
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

ইন্সটলেশন শেষে restart করতে হবে (reload অফ করা যায় না)


## Redis কনফিগারেশন
Redis কনফিগারেশন ফাইলটি `config/redis.php`তে রয়েছে
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

## উদাহরণ
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

> **লক্ষ্য করুন**
> ব্যবহার করার সময় key-এ একটি প্রিফিক্স যুক্ত করতে যাবে, যাতে অন্য রেডিস ব্যবহার করা পদক্ষেপে ঝগড়া না হয়।

## অন্যান্য ক্যাশ কম্পোনেন্ট ব্যবহার
[ThinkCache](https://github.com/top-think/think-cache) কম্পোনেন্ট ব্যবহারের জন্য দেখুন [অন্যান্য ডাটাবেস](others.md#ThinkCache)
