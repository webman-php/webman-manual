# রেডিস

ওয়েবম্যানর রেডিস কম্পোনেন্টটি ডিফল্টভাবে [illuminate/redis](https://github.com/illuminate/redis) ব্যবহার করে, যা হলো লারাভেলের রেডিস লাইব্রেরি, এটি লারাভেল সাথে ব্যবহারের জন্য।

`illuminate/redis` ব্যবহার করার আগে আপনার অবশ্যই `php-cli` তে রেডিস এক্সটেনশনটি ইনস্টল করতে হবে।

> **নোট**
> `php-cli` তে রেডিস এক্সটেনশন ইনস্টল করা আছে কি না তা চেক করতে `php -m | grep redis` কমান্ডটি ব্যবহার করুন। মনে রাখবেন: আপনি যদি `php-fpm` এ রেডিস এক্সটেনশন ইনস্টল করেন, তাহলে এর মাধ্যমে বুঝায় না যে `php-cli` তে আপনি এটি ব্যবহার করতে পারবেন, কারণ `php-cli` এবং `php-fpm` দুটি আলাদা কিছু, যা আলাদা `php.ini` কনফিগারেশন ব্যবহার করতে পারে। আপনার `php-cli` এর কোনটি `php.ini` কনফিগারেশন ফাইল ব্যবহার করছে তা চেক করতে `php --ini` কমান্ড ব্যবহার করুন।

## ইনস্টলেশন

```php
composer require -W illuminate/redis illuminate/events
```

ইনস্টলেশন পরে পুনরায় restart করতে হবে (reload অফ হয় না)


## কনফিগুরেশন
রেডিস কনফিগারেশন ফাইল রয়েছে `config/redis.php`
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
> `Redis::select($db)` ইন্টারফেস ব্যবহারে সাবধান থাকুন, কারণ ওয়েবম্যান একটি স্থায়ী মেমোরি ফ্রেমওয়ার্ক, একটি অনুরোধ যদি `Redis::select($db)` ব্যবহার করে ডাটাবেস চেঞ্জ করে, তবে পরবর্তী অন্য অনুরোধগুলির প্রভাব পড়তে পারে। একাধিক ডাটাবেসের জন্য পরামর্শ দেওয়া হয় যে ডিফল্ট না থাকা ডাটাবেসগুলি পৃথক রেডিস সংযোগ কনফিগার করুন।

## বহু রেডিস সংযোগ ব্যবহার
উদাহরণস্বরূপ, কনফিগারেশন ফাইল `config/redis.php`
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
ডিফল্ট রুটে ব্যবহৃত হচ্ছে `default` কনফিগারেশনের সংযোগ, `Redis::connection()` মেথডের মাধ্যমে আপনি যে রেডিস সংযোগ ব্যবহার করতে চান তা বেছে নিতে পারেন।
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## ক্লাষ্টার কনফিগারেশন
আপনার এপ্লিকেশনে যদি রেডিস সার্ভার ক্লাষ্টার ব্যবহার করে, আপনার রেডিস কনফিগারেশন ফাইলে ক্লাস্টার বন্ধু সম্পর্কে সংজ্ঞা দেওয়া উচিত:
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

ডিফল্টভাবে, ক্লাস্টার নোডের উপর ক্লায়েন্ট শার্ড করা হতে পারে, যা আপনাকে নোড পুল এবং বড় পরিমাণের উপলব্ধ মেমোরি তৈরি করতে দেয়। এখানে মনে রাখা দরকার যে, ক্লায়েন্ট বেটিয়ে কাজ চলার সময় কোনো ভুল নিয়ে গ্রহণ না করার জন্য, এটা প্রাথমিকভাবে অন্য মূল ডাটাবেস থেকে ক্যাশ ডাটাগুলি প্রাপ্ত করতে ব্যবহার করা হয়। কিছুটা বেশি দক্ষতার সাথে রেডিসের মূল ক্লাস্টার ব্যবহার করতে, কনফিগারেশন ফাইলে options কীতে নিম্নলিখিত বিন্যাস করতে হবে:

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

## পাইপল কমান্ড
যখন আপনি সার্ভারে অনেক কমান্ড পাঠাতে চান, তখন আপনি পাইপল কমান্ড ব্যবহার করার জন্য পেশাদার করো থাকে। pipeline মেথডটি একটি রেডিস এর ইনস্ট্যান্সের একটি বিস্তারিত ফাংশন নেয়। আপনি সমস্ত কমান্ড রেডিস ইনস্ট্যান্সে পাঠিয়ে দিতে পারেন, সেগুলি একটি অপারেশনে পূরণ করা হবে:

```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
