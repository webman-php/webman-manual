# اللغات المتعددة

يستخدم اللغات المتعددة مكون [symfony/translation](https://github.com/symfony/translation) .

## التثبيت
```
composer require symfony/translation
```

## إنشاء حزمة اللغة
يقوم webman بتخزين حزم اللغات افتراضيًا في الدليل `resource/translations` (إذا لم يكن موجودًا ، يرجى إنشاؤه بنفسك) ، وإذا كنت بحاجة إلى تغيير الدليل ، يرجى ضبطه في `config/translation.php`.
يوجد مجلد فرعي لكل لغة ، وتعريف اللغة يتم وضعه افتراضيًا في `messages.php`. هناك مثال كما يلي:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

كل ملفات اللغة تعيد جملة إلى مصفوفة مثل:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```
## التكوين

`config/translation.php`

```php
return [
    // اللغة الافتراضية
    'locale' => 'zh_CN',
     // اللغة البديلة، إذا كان من غير الممكن العثور على ترجمة في اللغة الحالية ، يقوم بمحاولة استخدام الترجمة في اللغة البديلة.
    'fallback_locale' => ['zh_CN', 'en'],
     // مجلد تخزين ملف اللغة
    'path' => base_path() . '/resource/translations',
];
```

## الترجمة

تستخدم الترجمة الطريقة `trans()`.

إنشاء ملف لغة `resource/translations/zh_CN/messages.php` كما يلي:
```php
return [
    'hello' => 'مرحباً بالعالم!',
];
```

إنشاء ملف `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // مرحباً بالعالم!
        return response($hello);
    }
}
```

عند زيارة `http://127.0.0.1:8787/user/get` ستعيد "مرحباً بالعالم!"

## تغيير اللغة الافتراضية

استخدام الطريقة `locale()` لتغيير اللغة.

إنشاء ملف لغة جديد `resource/translations/en/messages.php` كما يلي:
```php
return [
    'hello' => 'مرحبا العالم!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // تغيير اللغة
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
زيارة `http://127.0.0.1:8787/user/get` ستعيد "hello world!"

يمكنك أيضًا استخدام البرمجية التابعة `trans()` والتي تحتوي على المعامل الرابعة لتغيير اللغة مؤقتًا، على سبيل المثال:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // تغيير اللغة بالمعامل الرابع
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## تعيين اللغة بوضوح لكل طلب
translation هي عنصر فردي، مما يعني أن جميع الطلبات تشترك في هذا الفردي، إذا قامت طلب ما باستخدام `locale()` لتحديد اللغة الافتراضية، فسيؤثر ذلك على جميع الطلبات اللاحقة في العملية. لذا يجب علينا تعيين اللغة بوضوح لكل طلب. على سبيل المثال باستخدام الوسيط التالي

إنشاء ملف `app/middleware/Lang.php` (إذا لم يكن موجودًا يرجى إنشاؤه) كما يلي:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

في `config/middleware.php`، يرجى إضافة الوسيط العالمي كما يلي:
```php
return [
    // الوسيط العالمي
    '' => [
        // ... هنا يتم حذف بقية الوسطاء
        app\middleware\Lang::class,
    ]
];
```
## استخدام الحجة
أحيانًا، تحتوي المعلومات على متغيرات يجب ترجمتها، مثل
```php
trans('hello ' . $name);
```
عندما تواجه هذا السيناريو نستخدم حجة للتعامل معها.

تعديل `resource/translations/zh_CN/messages.php` كما يلي:
```php
return [
    'hello' => 'مرحباً %name%!',
];
```
عند الترجمة سنمرر البيانات التي تتطابق مع الحجة من خلال الحجة الثانية
```php
trans('hello', ['%name%' => 'webman']); // مرحباً بك، webman!
```

## التعامل مع الأعداد
بعض اللغات تستخدم صيغ مختلفة بناءً على كمية الأشياء، على سبيل المثال `There is %count% apple`، عندما يكون `%count%` مساوٍ لواحد يجب أن تكون الصيغة صحيحة، عندما يكون أكثر من واحد فستكون خاطئة.

عند مواجهة هذا السيناريو نستخدم **الخط الرأسي** (`|`) لتحديد صيغ الجمع.

إضافة الفرع `apple_count` في ملف اللغة `resource/translations/en/messages.php` كما يلي:
```php
return [
    // ...
    'apple_count' => 'ثمة تفاحة واحدة|هناك %count% تفاحة',
];
```

```php
trans('apple_count', ['%count%' => 10]); // هناك 10 تفاحة
```

حتى يمكننا تحديد نطاق الأرقام، وإنشاء قواعد جمع أكثر تعقيداً.
```php
return [
    // ...
    'apple_count' => '{0} ليس هناك تفاح|{1} هناك تفاحة واحدة|]1,19] هناك %count% تفاحة|]20,Inf[ هناك الكثير من التفاح'
];
```

```php
trans('apple_count', ['%count%' => 20]); // هناك الكثير من التفاح
```

## تحديد ملف اللغة

الملف الافتراضي للغة هو `messages.php`، ولكن في الواقع يمكنك إنشاء ملفات لغة بأسماء مختلفة.

إنشاء ملف لغة `resource/translations/zh_CN/admin.php` كما يلي:
```php
return [
    'hello_admin' => 'مرحباً بالمسؤول!',
];
```

يمكنك تحديد ملف اللغة باستخدام الحجة الثالثة لـ `trans()` (بدون اللاحقة `.php`)، على سبيل المثال:
```php
trans('hello', [], 'admin', 'zh_CN'); // مرحباً بالمسؤول!
```

## للمزيد من المعلومات
يرجى الرجوع إلى [دليل symfony/translation](https://symfony.com/doc/current/translation.html)
