## मेडू

मेडू एक हल्के भार के डेटाबेस ऑपरेशन प्लगइन है, [मेडू वेबसाइट](https://medoo.in/)।

## स्थापना
`composer require webman/medoo`

## डेटाबेस सेटअप
कॉन्फ़िग फ़ाइल का स्थान है `config/plugin/webman/medoo/database.php`

## उपयोग
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

> **संकेत**
>`Medoo::get('user', '*', ['uid' => 1]);`
>तुलनात्मक है
>`Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## एकाधिक डेटाबेस सेटअप

**सेटअप**  
`config/plugin/webman/medoo/database.php` में एक नया सेटअप जोड़ें, कुंजी कोई भी, यहाँ `other` का उपयोग किया गया है।

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
    // यहाँ एक अन्य सेटअप जोड़ा गया है
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

**उपयोग**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## विस्तृत दस्तावेज़
देखें [मेडू आधिकारिक दस्तावेज़](https://medoo.in/api/select)
