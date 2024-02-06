webman তার MongoDB কম্পোনেন্ট হিসেবে [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) ব্যবহার করে, যা লারাভেল প্রকল্প থেকে নেওয়া হয়েছে এবং এর ব্যবহারও লারাভেলের সাথে একই।

`jenssegers/mongodb` ব্যবহার করতে আগে `php-cli` এ mongodb এক্সটেনশন ইনস্টল করতে হবে।

> `php -m | grep mongodb` কমান্ড ব্যবহার করে দেখুন `php-cli` -তে mongodb এক্সটেনশন ইনস্টল আছে কি না। লক্ষ্য করুন: আপনি যদি `php-fpm` -এ mongodb এক্সটেনশন ইনস্টল করে থাকেন, তবে এটা মানে করেননা যে `php-cli` -তে আপনি এটি ব্যবহার করতে পারবেন, কারণ `php-cli` এবং `php-fpm` দুটি ভিন্ন অ্যাপ্লিকেশন, যা দুটিরই ভিন্ন `php.ini` কনফিগারেশন ব্যবহার করতে পারে। আপনার `php-cli` -এ কোন `php.ini` কনফিগারেশন ফাইল ব্যবহার হচ্ছে তা দেখতে `php --ini` কমান্ড ব্যবহার করুন।

## ইনস্টলেশন

PHP>7.2 এর জন্য
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 এর জন্য
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

ইনস্টলেশন এর পর পুনরারম্ভ(restart) করতে হবে (reload করা বা কার্যকর নয়)

## কনফিগারেশন
 `config/database.php` ফাইলে `mongodb` কানেকশন যুক্ত করুন, এমনভাবে:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...অন্যান্য কনফিগারেশন অংশগুলি এখানে অংশগুলিসহ নির্দিষ্ট করা আছে...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // এখানে আপনি মোর নির্দিষ্ট ইউ আই বিহীনোকে অ্ধ্যায়তে Mongo ড্রাইভার ম্যানেজারকে নির্দেশ করার আরও সেটিংস পাস করতে পারেন
                // বিস্তারিত প্যারামিটারগুলির তালিকা দেখতে https://www.php.net/manual/en/mongodb-driver-manager.construct.php এ "Uri Options" অধীনে দেওয়া হয়েছে

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## উদাহরণ
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## অধিক তথ্যের জন্য দেখুন

https://github.com/jenssegers/laravel-mongodb
