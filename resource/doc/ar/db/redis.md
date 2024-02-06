مكون Redis في webman يستخدم افتراضيًا [illuminate/redis](https://github.com/illuminate/redis) ، وهو مكتبة Redis لـ laravel ، ويتم استخدامه بنفس الطريقة كما هو الحال في laravel.

يجب تثبيت إضافة Redis على `php-cli` قبل استخدام `illuminate/redis`.

> **ملاحظة**
> استخدم الأمر `php -m | grep redis` للتحقق من تثبيت إضافة Redis على `php-cli`. يرجى ملاحظة أن وجود إضافة Redis على `php-fpm` لا يعني أنه يمكنك استخدامه على `php-cli` ، لأن `php-cli` و `php-fpm` هما تطبيقان مختلفان وقد يستخدمان تكوين مختلف من ملف `php.ini`. يمكنك استخدام الأمر `php --ini` لمعرفة ملف `php.ini` الذي يستخدمه `php-cli`.

## التثبيت

```php
composer require -W illuminate/redis illuminate/events
```

بعد التثبيت ، يجب أن تقوم بإعادة التشغيل (إعادة التحميل غير فعال)


## الإعداد
ملف تهيئة Redis موجود في `config/redis.php`
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
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## واجهة Redis
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
...

```

> **ملاحظة**
> ينصح باستخدام واجهة `Redis::select($db)` بحذر، نظرًا لأن webman هو إطار عمل دائم البقاء في الذاكرة ، فإن استخدام `Redis::select($db)` في طلب معين سيؤثر على الطلبات الأخرى التالية. يفضل تحديد قواعد بيانات مختلفة لتكوينات الاتصال Redis المختلفة.

## استخدام اتصالات Redis متعددة
على سبيل المثال في ملف التكوين `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
يتم استخدام التكوين الذي يحمل الاسم `default` بشكل افتراضي، يمكنك استخدام طريقة `Redis::connection()` لتحديد أي اتصال Redis يجب استخدامه.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## تكوين العنقود
إذا كنت تستخدم مجموعة خوادم Redis في تطبيقك ، يجب عليك تعريف هذه المجموعة في ملف تكوين Redis باستخدام المفاتيح المجموعات:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

بشكل افتراضي ، يمكن للعنقود تنفيذ التجزئة على العقد ، مما يتيح لك إنشاء مجموعة كبيرة من الذاكرة المتاحة. ومع ذلك ، يجب ملاحظة أن المشترك القابل للتبادل لن يتعامل مع حالات الفشل. لذلك ، يجب استخدام هذه الميزة بشكل رئيسي للاستيلاء على البيانات المخزنة من قاعدة بيانات أخرى. إذا كنت ترغب في استخدام عنقود Redis الأصلي ، يجب القيام بالتكوين المذكور أدناه تحت مفتاح options في ملف التكوين:

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## أوامر أنابيب
في الحالات التي تحتاج فيها إلى إرسال العديد من الأوامر إلى الخادم في عملية واحدة ، يوصى باستخدام أوامر الأنبوبة. تقبل الطريقة الانابيبية مثيلًا لإغلاق Redis كمعلمة. يمكنك إرسال جميع الأوامر إلى مثيل Redis ، حيث يتم تنفيذها جميعًا في عملية واحدة:

```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
