# المتحكمون

إنشاء ملف تحكم جديد `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

عند زيارة `http://127.0.0.1:8787/foo`، ستعرض الصفحة `hello index`.

عند زيارة `http://127.0.0.1:8787/foo/hello`، ستعرض الصفحة `hello webman`.

بالطبع يمكنك تغيير قواعد التوجيه عبر تكوين الطرق، انظر إلى [الطرق](route.md).

> **نصيحة**
> في حالة حدوث خطأ 404 غير قادر على الوصول، يرجى فتح `config/app.php`، وتعيين `controller_suffix` إلى `Controller`، ثم أعد تشغيل الخدمة.

## لاحقة المتحكم
ابتداءً من إصدار 1.3 من webman، يدعم تعيين لاحقة المتحكم في `config/app.php`. إذا تم تعيين `controller_suffix` في `config/app.php` إلى فارغ `''`، فسيكون اسم المتحكم كما يلي

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

يُوصى بشدة بتعيين لاحقة المتحكم إلى `Controller`، حيث يمكن تجنب تضارب أسماء المتحكم مع أسماء النماذج وزيادة الأمان في الوقت نفسه.

## شرح
- سيقوم الإطار تلقائيًا بتمرير كائن `support\Request` إلى المتحكم، ويمكن من خلاله الحصول على بيانات إدخال المستخدم (البيانات التي تم استلامها من الطلب مثل GET و POST والرأس والكوكيز)، انظر إلى [الطلبات](request.md).
- يمكن للمتحكم أن يُرجع أرقامًا أو سلاسل أو كائن `support\Response` ولكن لا يمكنه أن يُرجع أي نوع آخر من البيانات.
- يمكن إنشاء كائن `support\Response` من خلال دوال المساعد مثل `response()` `json()` `xml()` `jsonp()` `redirect()`.

## دورة حياة المتحكم

عندما تكون قيمة `controller_reuse` في `config/app.php` تساوي `false`، سيتم تهيئة كل نسخة من متحكم لكل طلب، وعند انتهاء الطلب سيتم تدمير هذه النسخة، ويكون هذا مشابهًا لكيفية عمل الأطر الأخرى.

عندما تكون قيمة `controller_reuse` في `config/app.php` تساوي `true`، فسيتم إعادة استخدام كل نسخة من متحكم بحيث بمجرد إنشائها ستكون في الذاكرة وستُعاد استخدامها لكل الطلبات.

> **ملاحظة**
> يتطلب تعطيل إعادة استخدام المتحكم إصدار webman>=1.4.0، وهذا يعني أنه في الإصدار 1.4.0 وقبل ذلك كان المتحكم يعاد استخدامه لكل الطلبات افتراضيًا ولا يمكن تغييره.

> **ملاحظة**
> عندما يتم تمكين إعادة استخدام المتحكم، لا ينبغي أن يتم تغيير أي من خصائص المتحكم خلال الطلب، لأن تلك التغييرات ستؤثر على الطلبات التالية، على سبيل المثال

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // سيتم الاحتفاظ بهذا الكائن بعد أول طلب update?id=1.
        // عند إجراء طلب delete?id=2 مرة أخرى، سيتم حذف البيانات 1.
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **نصيحة**
> لن يكون للرد عبر `return` من داخل مُنشئ المتحكم `__construct()` أي تأثير، كما في المثال التالي

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // الرد عبر المنشئ لن يكون له أي تأثير، ولن يتلقى المستعرض هذا الاستجابة
        return response('hello'); 
    }
}
```

## اختلاف الاستخدام وعدم الاستخدام للمتحكم
الاختلافات على النحو التالي

#### عدم استخدام المتحكم
سيتم إنشاء نسخة جديدة من المتحكم لكل طلب، وعند انتهاء الطلب سيتم تدمير هذه النسخة، وسيتم استرداد الذاكرة. عدم استخدام المتحكم مماثلاً لكيفية عمل الأطر التقليدية. نظرًا لتكرار إنشاء المتحكم وتدميره، ستكون الأداء أقل قليلاً من استخدام المتحكم حيث يمكن تجاهل الأداء (أداء helloworld -10% تقريبًا) مع التطبيقات التجارية.

#### استخدام المتحكم
بالاستخدام، ستتم إعادة استخدام المتحكم مرة واحدة في العملية، وعند انتهاء الطلب لن يتم تدمير هذه النسخة وستتم إعادة استخدامها للطلبات التالية. الأداء يكون في حالة الاستخدام أفضل، ولكن لا يتوافق ذلك مع معظم عادات المطورين.

#### الحالات التي لا يمكن فيها استخدام استخدام المتحكم
عندما يؤدي الطلب إلى تغيير خصائص المتحكم، لا يمكن تشغيل استخدام المتحكم، لأن هذه التغييرات في الخصائص ستؤثر على الطلبات التالية.

يفضل بعض المطورين إجراء بعض الإعدادات لكل طلب داخل مُنشئ المتحكم `__construct()`، في هذه الحالة لا يمكن استخدام استخدام المتحكم، لأن مُنشئ العملية سيُستدعى مرة واحدة فقط لكل عملية وليس لكل طلب.


