# দ্রুত শুরু

webman ডাটাবেস ডিফল্টভাবে [illuminate/database](https://github.com/illuminate/database) ব্যবহার করে, অর্থাৎ [লারাভেল ডাটাবেসে](https://learnku.com/docs/laravel/8.x/database/9400)। এটি লারাভেলের মতো ব্যবহার করা যায়।

তবে আপনি [অন্যান্য ডাটাবেস কাস্টমাইজেশন](others.md) ফাইল পর্যায়ে থিংকপিএইচপি বা অন্য ডাটাবেস ব্যবহার করতে পারেন।

## ইনস্টলেশন

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

ইনস্টলেশন সম্পন্ন হলে রিস্টার্ট করতে হবে (রিলোড করা যাবে না)

> **সাজাক্ষী**
> পেজিনেশন, ডাটাবেস ইভেন্ট, SQL প্রিন্ট এর প্রয়োজন না হলে, কেবলমাত্র নিম্নবর্ণিত কমান্ড চালু করতে হবে
> `composer require -W illuminate/database`

## ডাটাবেস কনফিগারেশন
`config/database.php`
```php

return [
    // ডিফল্ট ডাটাবেস
    'default' => 'mysql',

    // বিভিন্ন ডাটাবেস কনফিগারেশন
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```


## ব্যবহার
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("hello $name");
    }
}
```
