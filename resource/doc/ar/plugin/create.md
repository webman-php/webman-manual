# عملية إنشاء ونشر البرنامج الأساسي

## المبدأ
1. كمثال على البرنامج العابر للنطاقات، يتكون البرنامج من ثلاثة أجزاء، أحدها ملف برنامج وسيط العابر للنطاقات، وآخر هو ملف تكوين وسيط middleware.php، وثالثهما هو ملف Install.php الذي يتم إنشاؤه تلقائيًا من خلال الأمر.
2. نحن نستخدم الأمر لضغط الثلاثة ملفات ونشرها عبر المكون.
3. عندما يقوم المستخدم بتثبيت برنامج العابر للنطاقات باستخدام المكون، سيقوم ملف Install.php في البرنامج بنسخ ملف برنامج وسيط العابر للنطاقات وملف التكوين إلى `{المشروع_الرئيسي}/config/plugin`، مما يتيح لـ webman تحميلها. وبالتالي تحقيق فعالية تكوين ملف برنامج وسيط العابر للنطاقات تلقائيًا.
4. عندما يقوم المستخدم بحذف البرنامج باستخدام المكون، سيقوم ملف Install.php بحذف ملف برنامج وسيط العابر للنطاقات المقابل وملف التكوين، مما يحقق إزالة البرنامج تلقائيًا.

## المعايير
1. يتكون اسم البرنامج من جزأين، "الشركة المصنعة" و "اسم البرنامج"، على سبيل المثال `webman/push`، وهذا متطابق مع اسم حزمة المكون.
2. توضع ملفات تكوين البرنامج بشكل موحد في `config/plugin/الشركة المصنعة/اسم البرنامج/`. إذا لم يكن البرنامج يحتاج إلى تكوين، يجب حذف دليل التكوين الذي يتم إنشاؤه تلقائيًا.
3. يدعم دليل تكوين البرنامج فقط ملفات التكوين الرئيسية app.php، وتكوين بدء العمليات bootstrap.php، وتكوين المسار route.php، وتكوين وسيط middleware.php، وتكوين عمليات مخصصة process.php، وتكوين قاعدة بيانات database.php، وتكوين Redis redis.php، وتكوين thinkorm thinkorm.php. سيتم التعرف على هذه التكوينات تلقائيًا من قبل webman.
4. يمكن للبرنامج الحصول على التكوين باستخدام الأسلوب التالي `config('plugin.الشركة المصنعة.اسم البرنامج.ملف التكوين.البند المحدد');`، على سبيل المثال `config('plugin.webman.push.app.app_key')`
5. إذا كان لدى البرنامج تكوين قاعدة بيانات خاص به، فيجب الوصول إليه عبر الطريقة التالية. `illuminate/database` هو `Db::connection('plugin.الشركة المصنعة.اسم البرنامج.اتصال محدد')`، `thinkrom` هو `Db::connection('plugin.الشركة المصنعة.اسم البرنامج.اتصال محدد')`
6. إذا كان البرنامج بحاجة إلى وضع ملفات تجارية في الدليل `app/` يجب التأكد من عدم تعارضها مع مشروع المستخدم أو مع برامج أخرى.
7. يجب على البرنامج تجنب نسخ الملفات أو الدلائل إلى المشروع الرئيسي قدر الإمكان، على سبيل المثال، برنامج العابر للنطاقات بالإضافة إلى ملفات التكوين يجب وضعها في `vendor/webman/cros/src`، بدلاً من نسخها إلى المشروع الرئيسي.
8. يُفضل استخدام مساحة الاسم الجغرافية للبرنامج بأحرف كبيرة، على سبيل المثال Webman/Console.

## مثال

**تثبيت أمر webman/console**

`composer require webman/console`

#### إنشاء برنامج

لنفترض أن البرنامج الذي تم إنشاؤه يسمى `foo/admin` (وهو أيضًا اسم المشروع الذي سيتم نشره في composer، يجب أن يكون الاسم بحروف صغيرة)
تشغيل الأمر
`php webman plugin:create --name=foo/admin`

بعد إنشاء البرنامج، سيتم إنشاء الدليل `vendor/foo/admin` لتخزين الملفات ذات الصلة بالبرنامج والدليل `config/plugin/foo/admin` لتخزين تكوينات البرنامج ذات الصلة.

> تنبيه
> يدعم `config/plugin/foo/admin` التكوينات التالية، app.php تكوين البرنامج الرئيسي، bootstrap.php تكوين بدء العمليات، route.php تكوين المسار، middleware.php تكوين وسيط البرنامج، process.php تكوين العمليات المخصصة، database.php تكوين قاعدة البيانات، redis.php تكوين Redis، thinkorm.php تكوين thinkorm. يتم التعرف على هذه التكوينات تلقائيًا من قبل webman، ويتم ضمها إلى التكوينات.
يتم الوصول إليها باستخدام `plugin` كبادئة، على سبيل المثال `config('plugin.foo.admin.app');`.


#### تصدير البرنامج

عندما ننهي تطوير البرنامج، نقوم بتشغيل الأمر التالي لتصدير البرنامج
`php webman plugin:export --name=foo/admin`

> شرح
> بعد التصدير، سيتم نسخ دليل `config/plugin/foo/admin` إلى `vendor/foo/admin/src`، وسيتم إنشاء ملف Install.php تلقائيًا. يستخدم ملف Install.php لتنفيذ بعض العمليات عند تثبيت البرنامج تلقائيًا وعند حذفه.
العملية الافتراضية للتثبيت هي نسخ تكوينات الملفات من `vendor/foo/admin/src` إلى تكوين البرنامج الحالي في المشروع.
والعملية الافتراضية عند الحذف هي حذف ملفات التكوين من تكوين البرنامج الحالي في المشروع.
بإمكانك تعديل Install.php لتنفيذ بعض العمليات المخصصة أثناء تثبيت وحذف البرنامج.

#### تقديم البرنامج
* فلنفترض أن لديك بالفعل حساب على [github](https://github.com) و [packagist](https://packagist.org)
* قم بإنشاء مشروع admin على [github](https://github.com) وقم بتحميل الكود، ولنفترض أن عنوان المشروع هو `https://github.com/اسم المستخدم/admin`
* انتقل إلى العنوان `https://github.com/اسم المستخدم/admin/releases/new` وقم بنشر إصدار جديد مثل `v1.0.0`
* انتقل إلى [packagist](https://packagist.org) واضغط على `Submit` في القائمة التنقلية، قم بتقديم عنوان مشروع github الخاص بك `https://github.com/اسم المستخدم/admin` وهكذا تم إتمام نشر برنامج.

> **تلميح**
> إذا كان تقديم البرنامج في `packagist` يظهر صراعاً في الكلمات، يمكنك اختيار اسم شركة جديد، على سبيل المثال `foo/admin` يمكن تغييره إلى `myfoo/admin`

بعد ذلك، عندما يكون لمشروع البرنامج تحديث، يجب مزامنة الكود مع github ومن ثم الذهاب إلى العنوان `https://github.com/اسم المستخدم/admin/releases/new` مرة أخرى لنشر إصدار جديد، ثم الانتقال إلى صفحة `https://packagist.org/packages/foo/admin` والنقر على زر `Update` لتحديث الإصدار.

## إضافة أوامر للبرنامج
أحيانًا نحتاج إلى بعض الأوامر المخصصة للبرنامج لتوفير بعض الوظائف المساعدة، على سبيل المثال، بعد تثبيت برنامج `webman/redis-queue`، سيتم إضافة أمر `redis-queue:consumer` إلى المشروع بشكل تلقائي، مما يسمح للمستخدم بتشغيل `php webman redis-queue:consumer send-mail` بحيث يتم إنشاء فئة مستهلك SendMail.php في المشروع، وهذا يساعد في التنمية السريعة.

لنفترض أن برنامج `foo/admin` يحتاج إلى إضافة أمر `foo-admin:add`، يمكنك متابعة الخطوات التالية.

#### إنشاء أمر جديد
**إنشاء ملف الأمر `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'وصف السطر الأمر';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'إضافة اسم');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("إضافة مسؤول $name");
        return self::SUCCESS;
    }

}
```

> **ملحوظة**
> من أجل تجنب تضارب الأوامر بين البرامج، يُفضل أن تكون صيغة الأمر هي `الشركة المصنعة-اسم البرنامج: الأمر الخاص`، على سبيل المثال، يجب أن تكون جميع أوامر برنامج `foo/admin` تحتوي على بادئة `foo-admin:`، مثل `foo-admin:add`.

#### إضافة تكوين
**إنشاء تكوين `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....يمكنك إضافة التكوينات المتعددة...
];
```

> **تلميح**
> `command.php` تُستخدم لتكوين الأوامر المخصصة للبرنامج، وتعنى كل عنصر في المصفوفة بملف فئة أمر، ويتطابق كل ملف فئة بأمر واحد. عند تشغيل المستخدم لسطر الأمر `webman/console`، سيتم تحميل الأوامر المخصصة التي تم تكوينها في ملف `command.php` لكل برنامج. لمزيد من المعلومات المتعلقة بسطر الأوامر، يُرجى الرجوع إلى [سطر الأوامر](console.md).

#### تنفيذ التصدير
تنفيذ الأمر `php webman plugin:export --name=foo/admin` لتصدير البرنامج وتقديمه إلى `packagist`. وبهذه الطريقة، سيقوم المستخدم بتثبيت برنامج `foo/admin`، سيتم إضافة أمر `foo-admin:add`. وعند تشغيل `php webman foo-admin:add jerry`، سيتم طباعة `إضافة مسؤول jerry`.
