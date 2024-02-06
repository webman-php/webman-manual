# الذاكرة المؤقتة

تستخدم ويبمان افتراضيًا [symfony/cache](https://github.com/symfony/cache) كمكون ذاكرة مؤقتة.

> يجب تثبيت إضافة redis لـ `php-cli` قبل استخدام `symfony/cache`.

## التثبيت
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

بعد التثبيت، يجب إعادة التشغيل (reload لا يعمل).

## إعدادات Redis
ملف إعدادات redis في `config/redis.php`
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

## مثال
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **ملاحظة**
> يُفضل تضمين بادئة لمفتاح التخزين، لتجنب التعارض مع عمليات استخدام ذاكرة المؤقتة في redis من قبل تطبيقات أخرى.

## استخدام مكونات ذاكرة مؤقتة أخرى

يُرجى الرجوع إلى [هنا](others.md#ThinkCache) لمعرفة كيفية استخدام مكون ذاكرة مؤقتة آخر.
