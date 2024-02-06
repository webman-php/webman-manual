# রেডিস

webman এর রেডিস কম্পোনেন্ট ডিফল্টভাবে [illuminate/redis](https://github.com/illuminate/redis) ব্যবহার করে, অর্থাৎ লারাভেল এর রেডিস লাইব্রেরি। লারাভেল এর মত ব্যবহার করা যায়।

`illuminate/redis` ব্যবহার করার আগে `php-cli` এ রেডিস এক্সটেনশন ইন্সটল করা আবশ্যক।

> **নোট**
> `php-cli` এর রেডিস এক্সটেনশন ইন্সটল কিনা দেখতে `php -m | grep redis` কমান্ড ব্যবহার করুন। মনে রাখবেন: আপনি যদি `php-fpm` তে রেডিস এক্সটেনশন ইন্সটল করে থাকেন, তাহলে এর মাধ্যমে `php-cli` এ ব্যবহার করা যাবে না, কারণ `php-cli` এবং `php-fpm` দুটি আলাদা অ্যাপ্লিকেশন এন্ড তাদের জন্য ব্যবহৃত `php.ini` কনফিগারেশন আলাদা হতে পারে। কোন কনফিগারেশন ফাইল ব্যবহার হচ্ছে তা দেখতে `php --ini` কমান্ড ব্যবহার করুন।

## ইন্সটল করুন

```php
composer require -W illuminate/redis illuminate/events
```

ইন্সটল করার পরে আবার restart করুন (reload করা ব্যার্থ)

## কনফিগার

রেডিস কনফিগারেশন ফাইল পাওয়া যাবে `config/redis.php` এ

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

## রেডিস ইন্টারফেস
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
...
```

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

> **নোট**
> `Redis::select($db)` ইন্টারফেস ব্যবহারে সতর্ক হোন, webman একটি স্থায়ী মেমোরি ফ্রেমওয়ার্ক, একটি রিকোয়েস্ট প্রয়োগ করা হলে `Redis::select($db)` ব্যবহার করলে পরবর্তী অন্যান্য রিকোয়েস্ট প্রভাবিত হতে পারে। একাধিক ডাটাবেস ব্যবহারে প্রস্তাবিত প্রত্যেকটি `db`-কে আলাদা রেডিস কানেকশন কনফিগার করা উচিত।

## একাধিক রেডিস কানেকশন ব্যবহার
উদাহরণস্বরূপ `config/redis.php` ফাইলে
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
ডিফল্টভাবে `default` কনফিগারেশন ব্যবহার করা হয়, `Redis::connection()` মেথড দিয়ে আপনি কোন রেডিস কানেকশন ব্যবহার করতে চাইবেন তা নির্বাচন করতে পারেন।
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## ক্লাস্টার কনফিগারেশন
আপনার অ্যাপ্লিকেশনের জন্য যদি রেডিস সার্ভার ক্লাস্টার ব্যবহার করছেন, তবে আপনাকে রেডিস কনফিগারেশন ফাইলে clusters ব্যবহার করে সেটা ডিফাইন করতে হবে:
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

ডিফল্টভাবে, ক্লাস্টার নোড অনুযায়ী ক্লায়েন্ট শার করা যেতে পারে, ডেটা পুল বানানোর জন্য ও অনেক মেমোরি উপলব্ধ করা যেতে পারে। এ জন্য মনে রাখতে হবে যে, ক্লায়েন্ট শেয়ারিং সতর্কবাণী নিয়ে সম্পাদিত হতে পারে, যে কারণে, এ বৈশিষ্ট্যটি সাধারণভাবে অন্য মূল ডাটাবেস থেকে ক্যাশে ডেটা পেতে ব্যবহার করা হয়। যদি রেডিস অট্যামাটিক ক্লাস্টার ব্যবহার করতে চান, তবে কনফিগারেশন ফাইলে নিম্নোক্ত ধরনের সংকেতানুসারে options ব্যবহার করতে হবে:
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

## পাইপলাইন কমান্ড
আপনার যখন একটি অপারেশনে সার্ভারে অনেকগুলি কমান্ড পাঠাতে চাইবেন, তখন পাইপলাইন কমান্ড ব্যবহারকরা বিশেষভাবে অনুমোদিত। `pipeline` মেথডটি একটি রেডিস ইন্সট্যান্সটি পরলে ডেটা ভারী হওয়ার সময়ে ব্যবহার করা যায়। আপনি সকল কমান্ড রেডিস ইন্স্ট্যান্সে পাঠাতে পারবেন, যা আপনি চান, এগুলি সকলেরই একটি অপারেশনে অনুষ্ঠিত হবে:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
