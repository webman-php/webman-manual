يعتمد webman افتراضيًا على [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) كمكون mongodb ، وهو مشتق من مشروع laravel ، ويتم استخدامه بنفس الطريقة التي يتم فيها استخدام laravel.

يجب تثبيت الامتداد mongodb لـ `php-cli` قبل استخدام `jenssegers/mongodb`.

> استخدم الأمر `php -m | grep mongodb` للتحقق مما إذا كانت الامتدادات mongodb مثبتة لـ `php-cli`. يرجى ملاحظة: حتى لو كنت قد قمت بتثبيت امتدادات mongodb في `php-fpm` ، فهذا لا يعني أنك يمكنك استخدامه في `php-cli` ، لأن `php-cli` و `php-fpm` هما تطبيقات مختلفة ، وقد يستخدمان تكوينات `php.ini` مختلفة. استخدم الأمر `php --ini` لمعرفة ملف تكوين `php.ini` الذي يستخدمه `php-cli` الخاص بك.

## التثبيت

PHP>7.2 حينئذٍ
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 حينئذٍ
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

بعد التثبيت ، يجب إعادة التشغيل (إعادة التحميل غير فعالة)

## التكوين
في `config/database.php` ، قم بإضافة اتصال `mongodb` ، مثل ما يلي:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...التكوينات الأخرى تم حذفها هنا...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // يمكنك هنا تمرير المزيد من الإعدادات إلى Mongo Driver Manager
                // انظر https://www.php.net/manual/en/mongodb-driver-manager.construct.php في "Uri Options" للحصول على قائمة من المعلمات الكاملة التي يمكنك استخدامها

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## مثال
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

## لمزيد من المعلومات يرجى زيارة

https://github.com/jenssegers/laravel-mongodb
