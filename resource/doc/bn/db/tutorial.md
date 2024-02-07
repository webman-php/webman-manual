# দ্রুত শুরু

webman ডাটাবেস ডিফল্টভাবে [illuminate/database](https://github.com/illuminate/database) ব্যবহার করে, যার অর্থ হল [laravel এর ডাটাবেস](https://learnku.com/docs/laravel/8.x/database/9400) ব্যবহার করা হয়, এটি লারাভেলের মতো ব্যবহার করা যায়।

তবে আপনি [অন্যান্য ডাটাবেস কম্পোনেন্ট ব্যবহার করা](others.md) বিষয়বস্তু চেক করতে পারেন।

## ইনস্টলেশন

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

রিস্টার্ট করুন ইনস্টলেশনের পরে (রিলোড করা মান্য না)

> **পরামর্শ**
> যদি পেজিনেশন, ডাটাবেস ইভেন্ট, এবং SQL প্রিন্টিং প্রয়োজন না হয় তাহলে শুধুমাত্র নিম্নলিখিত কমান্ড চালানোর প্রয়োজন হবে
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
