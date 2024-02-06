# البدء السريع

تعتمد قاعدة بيانات webman افتراضيًا على [illuminate/database](https://github.com/illuminate/database)، وهي [قاعدة بيانات Laravel](https://learnku.com/docs/laravel/8.x/database/9400)، ويكون الاستخدام مشابهًا لـ Laravel.

بالطبع يمكنك الرجوع إلى الفصل [استخدام مكونات قاعدة بيانات أخرى](others.md) لاستخدام ThinkPHP أو قاعدة بيانات أخرى.

## التثبيت

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

بمجرد التثبيت، سيكون هناك حاجة لإعادة التشغيل (إعادة التحميل غير فعالة)

> **ملاحظة**
> إذا كنت لا تحتاج إلى تثبيت التصفح، وأحداث قاعدة البيانات، وطباعة SQL، يمكنك فقط استخدام الأمر
> `composer require -W illuminate/database`

## تكوين قاعدة البيانات
`config/database.php`
```php

return [
    // قاعدة بيانات افتراضية
    'default' => 'mysql',

    // تكوينات القواعد البيانات المختلفة
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


## الاستخدام
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
