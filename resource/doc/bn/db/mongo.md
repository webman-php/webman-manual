# মংগোডবি

webman পূর্বনির্ধারিতভাবে [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) ব্যবহার করে, যা laravel প্রকল্প থেকে তুলে আনা হয়েছে, এটি laravel এর মত ব্যবহার করা হয়।

`jenssegers/mongodb` ব্যবহার করার আগে এটি টা প্রথমে `php-cli` তে মংগোডবি এক্সটেনশন ইনস্টল করতে হবে।

> `php -m | grep mongodb` এই কমান্ড ব্যবহার করে দেখুন `php-cli` কি মংগোডবি এক্সটেনশন এনেবে কি না। লক্ষ্য করুন: যদি আপনি `php-fpm` এ মংগোডবি এক্সটেনশন ইনস্টল করে থাকেন, তবে এটা দর্শায় না যে আপনি `php-cli` তে এটি ব্যবহার করতে পারবেন, কারণ `php-cli` এবং `php-fpm` দুটি ভিন্ন অ্যাপ্লিকেশন, এরা কর্তৃক ব্যবহৃত `php.ini` কনফিগারেশন তারা ভিন্ন হতে পারে। `php --ini` কমান্ড ব্যবহার করে আপনার `php-cli` যে `php.ini` কনফিগারেশন ফাইল ব্যবহার করছে তা দেখুন।

## ইনস্টলেশন

PHP>7.2 হলে
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 হলে
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

ইনস্টলেশন পরে আবার restart করতে হবে (reload করলে কাজ হবে না)

## কনফিগারেশন
`config/database.php` ফাইলে `mongodb` কানেকশন যোগ করুন, নিম্নলিখিত মত।
```php
return [

    'default' => 'mysql',

    'connections' => [

         ... অন্যান্য কনফিগারেশন এখানে অপসারণ করা হয়েছে ...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // here you can pass more settings to the Mongo Driver Manager
                // see https://www.php.net/manual/en/mongodb-driver-manager.construct.php under "Uri Options" for a list of complete parameters that you can use

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## নমূনা
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

## অধিক তথ্য জানতে ভিজিট করুন

https://github.com/jenssegers/laravel-mongodb
