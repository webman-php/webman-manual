# تثبيت الإضافات التطبيقية
كل إضافة تطبيق هي تطبيق كامل، ويطبق الشفرة المصدرية في مسار "{المشروع الرئيسي}/plugin"

> **نصيحة**
> باستخدام الأمر `php webman app-plugin:create {اسم الإضافة}` (يتطلب webman/console>=1.2.16) يمكنك إنشاء إضافة تطبيقية محلية،
> على سبيل المثال `php webman app-plugin:create cms` سينشئ هيكل الدليل التالي.

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

نرى أن لدى الإضافة التطبيقية هيكل دليل وملفات تكوين مشابهة لتلك في webman. في الواقع، تجربة تطوير إضافة تطبيقية مشابهة تقريبًا لتجربة تطوير مشروع webman العادي، مع الانتباه للنقاط التالية.

## مساحة الأسماء
معيار PSR4 يتبع مساحة الأسماء وتسمية الدلائل في الإضافات التطبيقية، ونظرًا لأن الإضافات توضع جميعها في مجلد الإضافات، فإن مساحة الأسماء تبدأ بـ plugin، على سبيل المثال `plugin\cms\app\controller\UserController`، حيث يعتبر cms المجلد الرئيسي للشفرة المصدرية في الإضافة.

## عنوان URL
مسارات عناوين الإضافات التطبيقية تبدأ بـ `/app`، على سبيل المثال، عنوان URL لـ `plugin\cms\app\controller\UserController` هو `http://127.0.0.1:8787/app/cms/user`.

## الملفات الثابتة
يتم وضع الملفات الثابتة في `plugin/{الإضافة}/public`، على سبيل المثال، عند الوصول ل`http://127.0.0.1:8787/app/cms/avatar.png` فإنه في الواقع يتم الحصول على الملف `plugin/cms/public/avatar.png`.

## ملفات التكوين
تكوين الإضافة التطبيقية مماثل لمشروع webman العادي، ومع ذلك، يكون تكوين الإضافة التطبيقية عادة ما يكون ساري المفعول فقط للإضافة الحالية دون تأثير على المشروع الرئيسي.
على سبيل المثال، قيمة `plugin.cms.app.controller_suffix` تؤثر فقط على لاحقة المتحكم للإضافة، ولا تؤثر على المشروع الرئيسي.
على سبيل المثال، قيمة `plugin.cms.app.controller_reuse` تؤثر فقط على ما إذا كان سيتم إعادة استخدام المتحكم في الإضافة، ولا تؤثر على المشروع الرئيسي.
على سبيل المثال، قيمة `plugin.cms.middleware` تؤثر فقط على الوسيطة في الإضافة، ولا تؤثر على المشروع الرئيسي.
على سبيل المثال، قيمة `plugin.cms.view` تؤثر فقط على العرض الذي تستخدمه الإضافة، ولا تؤثر على المشروع الرئيسي.
على سبيل المثال، قيمة `plugin.cms.container` تؤثر فقط على الحاوية التي تستخدمها الإضافة، ولا تؤثر على المشروع الرئيسي.
على سبيل المثال، قيمة `plugin.cms.exception` تؤثر فقط على فئة معالجة الاستثناء في الإضافة، ولا تؤثر على المشروع الرئيسي.

ولكن نظرًا لأن المسار هو عام، فإن تكوين الإضافة يؤثر أيضًا على المستوى العالمي.

## الحصول على التكوين
طريقة الحصول على تكوين إضافة معينة هي `config('plugin.{الإضافة}.{التكوين المحدد}')`، على سبيل المثال، يمكن الحصول على كل التكوينات في `plugin/cms/config/app.php` عن طريق `config('plugin.cms.app')` بالإضافة إلى أنه بإمكان المشروع الرئيسي أو الإضافات الأخرى استخدام `config('plugin.cms.xxx')` للحصول على تكوينات الإضافة cms.

## تكوينات غير مدعومة
الإضافات التطبيقية لا تدعم تكوينات server.php، session.php، `app.request_class`، `app.public_path`، `app.runtime_path`.

## قاعدة البيانات
يمكن للإضافة تكوين قاعدة البيانات الخاصة بها، على سبيل المثال، محتوى `plugin/cms/config/database.php` كالتالي
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
        'admin' => [ // admin اسم الاتصال
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
الطريقة المستخدمة للإشارة إليها هي `Db::connection('plugin.{الإضافة}.{اسم الاتصال}');`، على سبيل المثال
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

إذا كنت ترغب في استخدام قاعدة بيانات المشروع الرئيسي، يمكنك استخدامها مباشرة، على سبيل المثال
```php
use support\Db;
Db::table('user')->first();
// يفترض أن المشروع الرئيسي لديه أيضًا اتصالًا بـ admin
Db::connection('admin')->table('admin')->first();
```

> **نصيحة**
> كذلك يمكنك استخدام تفكير orm بنفس الطريقة

## الذاكرة المؤقتة (Redis)
طريقة استخدام Redis مشابهة لاستخدام قاعدة البيانات، على سبيل المثال، `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
عند الاستخدام
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

بنفس الطريقة، إذا كنت ترغب في إعادة استخدام تكوين Redis في المشروع الرئيسي
```php
use support\Redis;
Redis::get('key');
// يفترض أن المشروع الرئيسي لديه أيضًا اتصالًا cache
Redis::connection('cache')->get('key');
```

## التسجيل (اللوق)
طريقة استخدام فئة اللوق مشابهة لاستخدام قاعدة البيانات
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

إذا كنت ترغب في إعادة استخدام تكوين اللوق في المشروع الرئيسي، يمكنك استخدامها مباشرة
```php
use support\Log;
Log::info('محتوى السجل');
// يفترض أن المشروع الرئيسي يحتوي على تكوين سجل اختبار
Log::channel('test')->info('محتوى السجل');
```

# تثبيت وإلغاء تثبيت الإضافات التطبيقية
عند تثبيت الإضافات التطبيقية، يكفي نسخ مجلد الإضافة إلى مسار `{المشروع الرئيسي}/plugin`، ويجب إعادة تحميل أو إعادة تشغيل لجعلها نشطة.
أما عند إلغاء التثبيت، يمكن حذف مجلد الإضافة المقابل في مسار `{المشروع الرئيسي}/plugin`.
