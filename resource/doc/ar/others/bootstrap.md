# تهيئة الأعمال

أحيانًا نحتاج إلى تنفيذ بعض تهيئة الأعمال بعد تشغيل العملية، وتُنفذ هذه التهيئة مرة واحدة خلال دورة حياة العملية، مثل تعيين مؤقت، أو تهيئة اتصال قاعدة البيانات على سبيل المثال. فيما يلي سنشرح ذلك بالتفصيل.

## المبدأ
وفقًا للشرح في **[سير التنفيذ](process.md)** ، بعد بدء تشغيل العملية، يقوم webman بتحميل الفئات المعينة في `config/bootstrap.php` (بما في ذلك `config/plugin/*/*/bootstrap.php`) وتنفيذ طريقة start الموجودة في الفئة. بإضافة رمز الأعمال في طريقة start، يتم تنفيذ تهيئة الأعمال بعد بدء تشغيل العملية.

## العملية
لنفترض أننا نريد إنشاء مؤقت يقوم بتقرير استخدام الذاكرة الخاص بالعملية بانتظام. سيكون اسم الفئة هو `MemReport`.

#### تنفيذ الأمر

قم بتنفيذ الأمر `php webman make:bootstrap MemReport` لإنشاء ملف البدء `app/bootstrap/MemReport.php`.

> **ملاحظة**
> إذا كان لديك webman ولم يتم تثبيت `webman/console`، قم بتنفيذ الأمر `composer require webman/console` للتثبيت

#### تحرير ملف البدء
قم بتحرير `app/bootstrap/MemReport.php`، وسيكون محتواه مشابهًا للمثال التالي:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // هل هو بيئة السطر الأمر ؟
        $is_console = !$worker;
        if ($is_console) {
            // إذا لم تكن ترغب في تنفيذ هذه البدء في بيئة السطر الأمر، يمكنك العودة هنا مباشرة
            return;
        }
        
        // تنفيذ كل 10 ثوانٍ
        \Workerman\Timer::add(10, function () {
            // لأغراض العرض فقط، سنستبدل التقرير بالإخراج
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **ملاحظة**
> عند استخدام السطر الأمر، يقوم الإطار بتنفيذ طريقة start الموجودة في `config/bootstrap.php` أيضًا. يمكننا استخدام `$worker` للتحقق ما إذا كان بيئة السطر الأمر أم لا، وبالتالي تحديد ما إذا كان يجب تنفيذ رمز تهيئة الأعمال.

#### تكوين البدء مع بدء التشغيل
افتح `config/bootstrap.php` وأضف فئة `MemReport` إلى البنود التي تبدأ.
```php
return [
    // ...هنا تم حذف الإعدادات الأخرى...
    
    app\bootstrap\MemReport::class,
];
```

بهذه الطريقة نكون قد أكملنا عملية تهيئة الأعمال.

## ملحق

[عندما يبدأ العمل](../process.md) تنفيذ العمليات المخصصة، يقوم أيضًا بتنفيذ طريقة start المحددة في `config/bootstrap.php`. يمكننا استخدام `$worker->name` للتحقق من نوع العملية الحالية، ومن ثم قرار ما إذا كان يجب تنفيذ رمز تهيئة الأعمال في هذه العملية. على سبيل المثال، إذا كنا لا نحتاج إلى مراقبة عملية monitor، سيكون محتوى `MemReport.php` مشابهًا للمثال التالي:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // هل هو بيئة السطر الأمر ؟
        $is_console = !$worker;
        if ($is_console) {
            // إذا لم تكن ترغب في تنفيذ هذا البدء في بيئة السطر الأمر، يمكنك العودة هنا مباشرة
            return;
        }
        
        // لا تقوم عملية monitor بتنفيذ المؤقت
        if ($worker->name == 'monitor') {
            return;
        }
        
        // تنفيذ كل 10 ثوانٍ
        \Workerman\Timer::add(10, function () {
            // لأغراض العرض فقط، سنستبدل التقرير بالإخراج
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
