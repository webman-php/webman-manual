# ডাটাবেস
প্লাগইনগুলি তাদের নিজস্ব ডাটাবেস কনফিগার করতে পারে, উদাহরণস্বরূপ `প্লাগইন / ফু / কনফিগ / ডাটাবেস.php` এমন কনফিগারেশন দেয়া হতে পারে
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlহল সংযোগের নাম
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ডাটাবেস',
            'username'    => 'ব্যবহারকারীর নাম',
            'password'    => 'পাসওয়ার্ড',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // অ্যাডমিন হল সংযোগের নাম
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ডাটাবেস',
            'username'    => 'ব্যবহারকারীর নাম',
            'password'    => 'পাসওয়ার্ড',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
এটি উদ্ধৃত করা হবেঃ `Db::connection('plugin.{প্লাগইন}.{সংযোগ-নাম}');` যেমন
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

যদি মূল প্রকল্পের ডাটাবেস ব্যবহার করতে চান, তবে তা সরাসরি ব্যবহার করা যাবে, উদাহরণস্বরূপ
```php
use support\Db;
Db::table('user')->first();
// যদিহেতু মূল প্রকল্পে 'admin' নামক একটি কানেকশন কনফিগার করা আছে
Db::connection('admin')->table('admin')->first();
```

## মডেলের ডাটাবেস কনফিগার করা

আমরা মডেলের জন্য একটি বেস ক্লাস তৈরি করতে পারি, বেস ক্লাসটিতে `কানেকশন` এর মাধ্যমে প্লাগইনের নিজস্ব ডাটাবেস সেট করা যায়, যেমন
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

দ্বারা যাহা প্লাগইন এর সমস্ত মডেল বেস ক্লাস থেকে প্লাগইনের নিজস্ব ডাটাবেস ব্যবহার করতে পারে।

## ডাটাবেস কনফিগারেশন পুনরায় ব্যবহার করা
তবে আমরা মূল প্রজেক্টের ডাটাবেস কনফিগারেশন পুনরায় ব্যবহার করতে পারি, [webman-admin](https://www.workerman.net/plugin/82) যদি যোগ করা হয়, তাহলে [webman-admin](https://www.workerman.net/plugin/82) এর ডাটাবেস কনফিগারেশনও পুনরায় ব্যবহার করা যাবে, উদাহরণস্বরূপ
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
