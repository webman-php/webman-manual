# vlucas/phpdotenv

## التعريف
`vlucas/phpdotenv` هو عنصر استحملال متغيرات البيئة، يتم استخدامه لتحميل إعدادات مختلفة في بيئات مختلفة (مثل البيئة التطويرية، بيئة الاختبار، إلخ).

## رابط المشروع

https://github.com/vlucas/phpdotenv
  
## التثبيت
 
```php
composer require vlucas/phpdotenv
 ```
  
## الاستخدام

#### إنشاء ملف `.env` في الدليل الجذري للمشروع
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### تعديل ملف الإعدادات
**config/database.php**
```php
return [
    // قاعدة البيانات الافتراضية
    'default' => 'mysql',

    // تكوينات مختلفة لقواعد البيانات
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **ملاحظة**
> من الأفضل إضافة ملف `.env` إلى قائمة `.gitignore` لتجنب تقديمه لمستودع الكود. يمكن إضافة ملف نموذج `.env.example` إلى مستودع الكود، وعند نشر المشروع يتم نسخ `.env.example` إلى `.env` وتعديل الإعدادات في `.env` وفقًا للبيئة الحالية، وبذلك يتم تحميل إعدادات مختلفة للمشروع في بيئات مختلفة.

> **يرجى ملاحظة**
> من الممكن أن يكون لدى `vlucas/phpdotenv` بعض الأخطاء في الإصدار المتوافق مع الخيوط (TS) في PHP، لذا يفضل استخدام الإصدار الغير متوافق مع الخيوط (NTS).
> يمكن التحقق من إصدار PHP الحالي عبر تنفيذ الأمر `php -v`.

## المزيد من المحتوى

يمكنك زيارة https://github.com/vlucas/phpdotenv
