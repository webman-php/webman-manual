تستخدم فئة `support\Context` لتخزين بيانات سياق الطلب، وعند اكتمال الطلب، سيتم حذف بيانات السياق المقابلة تلقائياً. وبمعنى آخر فإن دورة حياة بيانات السياق مرتبطة بدورة حياة الطلب. تدعم `support\Context` بيئات البرمجة المتزامنة مثل Fiber، Swoole، Swow.

لمزيد من المعلومات راجع [webman المتزامن](./fiber.md)

# واجهة برمجة التطبيقات
توفر السياق واجهات برمجة التطبيقات التالية

## تعيين بيانات السياق
```php
Context::set(string $name, $mixed $value);
```

## الحصول على بيانات السياق
```php
Context::get(string $name = null);
```

## حذف بيانات السياق
```php
Context::delete(string $name);
```

> **يرجى الملاحظة**
> سيقوم الإطار بالاتصال تلقائيًا بواجهة Context::destroy() لتدمير بيانات السياق بعد الانتهاء من الطلب، لا يمكن للتطبيق بشكل يدوي استدعاء Context::destroy()

# مثال
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# ملحوظة
**عند استخدام البرمجة المتزامنة**، من المهم ألا يتم تخزين **بيانات الحالة ذات الصلة بالطلب** في المتغيرات العالمية أو المتغيرات الثابتة، فقد يؤدي هذا إلى تلوث البيانات العامة، والسلوك الصحيح هو استخدام السياق لتخزينها واسترجاعها.

