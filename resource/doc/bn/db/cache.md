# ক্যাশ

webman ডিফল্টভাবে [symfony/cache](https://github.com/symfony/cache) ক্যাশ কম্পোনেন্ট ব্যবহার করে।

> `symfony/cache` ব্যবহার করার আগে, `php-cli` কে রেডিস এক্সটেনশন ইন্সটল করা আবশ্যক।

## ইনস্টলেশন
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

ইনস্টলেশন সম্পূর্ণ হওয়ার পরে রিস্টার্ট বা রিলোড করা প্রয়োজন নেই।


## রেডিস কনফিগারেশন
রেডিস কনফিগারেশন ফাইল পাওয়া যায় `config/redis.php`
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

> **নোট**
> যত সম্ভব কীটি একটি প্রিফিক্স যোগ করুন, যাতে অন্যান্য রেডিস ব্যবহার করা কোন উপযোগীতা এসে না যায়।

## অন্য ক্যাশ কম্পোনেন্ট ব্যবহার
[ThinkCache](https://github.com/top-think/think-cache) কম্পোনেন্ট ব্যবহারের জন্য দেখুন [অন্যান্য ডেটাবেস](others.md#ThinkCache)
