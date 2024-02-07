# ডাটাবেস
প্লাগইনগুলি তাদের নিজস্ব ডাটাবেস কনফিগার করতে পারে, যেমন `plugin/foo/config/database.php` এর মধ্যের কনফিগারেশন নিম্নলিখিত হতে পারে
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql হল কানেকশনের নাম
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ডাটাবেস',
            'username'    => 'ব্যবহারকারীনাম',
            'password'    => 'পাসওয়ার্ড',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // অ্যাডমিন হল কানেকশনের নাম
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ডাটাবেস',
            'username'    => 'ব্যবহারকারীনাম',
            'password'    => 'পাসওয়ার্ড',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
উদাহরণস্বরূপ উল্লেখনীয় সিদ্ধান্তটি হল `Db::connection('plugin.{প্লাগইন}.{কানেকশননাম}');` যেমন
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

যদি মূল প্রকল্পের ডাটাবেস ব্যবহার করতে চান তবে সরাসরি ব্যবহার করতে হবে, উদাহরণস্বরূপ
```php
use support\Db;
Db::table('user')->first();
// ধরা যাক মূল প্রকল্পটি একটি অ্যাডমিন কানেকশনও কনফিগার করেছে
Db::connection('admin')->table('admin')->first();
```

## Model কে ডাটাবেস কনফিগার করা
আমরা Model এর জন্য একটি Base ক্লাস তৈরি করতে পারি, Base ক্লাসে ` $connection ` ব্যবহার করে প্লাগইনের নিজের ডাটাবেস কানেকশনটি নির্দিষ্ট করা হয়েছে, উদাহরণস্বরূপ

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

এভাবে প্লাগইনে রয়েছে Base ক্লাসের উপর ভিত্তি করে সমস্ত মডেল স্বয়ংক্রিয়ভাবে প্লাগইনের নিজস্ব ডাটাবেস ব্যবহার করছে।

## ডাটাবেস কনফিগার পুনরায় ব্যবহার করা
ধরা যাক আমরা মূল প্রকল্পের ডাটাবেস কনফিগারেশন পুনরায় ব্যবহার করতে চাই, আবার যদি [webman-admin](https://www.workerman.net/plugin/82) সাথে যুক্ত করা হয়েছে তাহলে [webman-admin](https://www.workerman.net/plugin/82) ডাটাবেস কনফিগারেশনও ব্যবহার করা সম্ভব, উদাহরণস্বরূপ
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
