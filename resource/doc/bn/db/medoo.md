## Medoo

মিদু হল একটি লাইটওয়েট ডাটাবেস অপারেশন প্লাগইন, [মিদু ওয়েবসাইট](https://medoo.in/)।

## ইনস্টলেশন
`composer require webman/medoo`

## ডাটাবেস কনফিগারেশন
কনফিগারেশন ফাইলের অবস্থানের মার্গ `config/plugin/webman/medoo/database.php`

## ব্যবহার
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **পরামর্শ**
> `Medoo::get('user', '*', ['uid' => 1]);`
> এটির সমান
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## মাল্টিপল ডাটাবেস কনফিগারেশন

**কনফিগারেশন**  
`config/plugin/webman/medoo/database.php` এ একটি কনফিগারেশন যুক্ত করুন, কী যতরকম হোক, এখানে `অথার` ব্যবহার করা হয়েছে।

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    // এখানে অথার কনফিগারেশন যুক্ত করা হয়েছে
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```

**ব্যবহার**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## বিস্তারিত নথি
দেখুন [Medoo অফিশিয়াল ডকুমেন্টেশন](https://medoo.in/api/select)
