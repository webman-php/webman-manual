## Webman

### تثبيت Webman

`composer require -W webman/think-orm`

بعد التثبيت، يجب إعادة تشغيل (reload) (إعادة تشغيل غير فعالة)

> **تلميح**
> إذا فشل التثبيت، ربما بسبب استخدامك لبروكسي composer، جرب تشغيل `composer config -g --unset repos.packagist` لإلغاء بروكسي composer وتجربة مرة أخرى

> [webman/think-orm](https://www.workerman.net/plugin/14) هو في الواقع إضافة تلقائية لتثبيت `toptink/think-orm`، إذا كان إصدار webman الخاص بك أقل من `1.2` وغير قادر على استخدام الإضافة، يرجى الرجوع إلى المقال [تثبيت وتكوين think-orm يدويًا](https://www.workerman.net/a/1289).

### ملف التكوين
قم بتعديل ملف التكوين حسب الحالة الفعلية `config/thinkorm.php`

### الاستخدام

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### إنشاء النموذج

يمتد نموذج ThinkOrm من `think\Model`، مشابهة لما يلي
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * الجدول المرتبط بالنموذج.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * المفتاح الرئيسي المرتبط بالجدول.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

يمكنك أيضًا استخدام الأمر التالي لإنشاء نموذج يعتمد على thinkorm
```
php webman make:model اسم_الجدول
```

> **تلميح**
> هذا الأمر يتطلب تثبيت `webman/console`، أمر التثبيت هو `composer require webman/console ^1.2.13`

> **يرجى الملاحظة**
> إذا اكتشف أمر make:model استخدام مشروع رئيسي `illuminate/database`، سيتم إنشاء ملف نموذج يعتمد على `illuminate/database` وليس thinkorm، يمكن استخدام معلمة إضافية tp لإجبار إنشاء نموذج يعتمد على think-orm. يبدو الأمر مشابهًا لـ `php webman make:model اسم_الجدول tp` (إذا لم يكن فعالًا، يرجى تحديث `webman/console`)
