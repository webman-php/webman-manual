# قاعدة البيانات
يمكن للإضافات تكوين قاعدة بياناتها الخاصة ، على سبيل المثال `plugin/foo/config/database.php` المحتوى كالتالي
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql اسم الاتصال
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'قاعدة البيانات',
            'username'    => 'اسم المستخدم',
            'password'    => 'كلمة المرور',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // اسم الاتصال للمشرف
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'قاعدة البيانات',
            'username'    => 'اسم المستخدم',
            'password'    => 'كلمة المرور',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
يمكن الاستفادة من طريقة `Db::connection('plugin.{الإضافة}.{اسم الاتصال}');`، على سبيل المثال
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

إذا كنت ترغب في استخدام قاعدة البيانات الرئيسية ، يمكنك استخدامها مباشرة ، على سبيل المثال
```php
use support\Db;
Db::table('user')->first();
// يفترض أن البرنامج الرئيسي لا يزال يحتوي على اتصال بالمشرف
Db::connection('admin')->table('admin')->first();
```

## تكوين قاعدة البيانات لنموذج

يمكننا إنشاء فئة قاعدية لنموذج Base ، حيث تستخدم ` $connection` لتحديد اتصال قاعدة بيانات الإضافة بنفسها، على سبيل المثال

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

بهذه الطريقة ، سيستفيد جميع نماذج الإضافة من نموذج Base وسيتم استخدام قاعدة بيانات الإضافة تلقائيًا.

## إعادة استخدام تكوينات قاعدة البيانات
بالطبع ، يمكننا إعادة استخدام تكوينات قاعدة البيانات الرئيسية ، وإذا كنا قد قمنا بربط [webman-admin](https://www.workerman.net/plugin/82) ، يمكننا أيضًا إعادة استخدام تكوينات قاعدة بيانات [webman-admin](https://www.workerman.net/plugin/82)، على سبيل المثال
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
