# معالجة الاستثناءات

## الضبط
`config/exception.php`
```php
return [
    // قم بتأكيد فئة معالجة الاستثناءات هنا
    '' => support\exception\Handler::class,
];
```
في حالة وجود وضع التطبيقات المتعددة، يمكنك تكوين فئة معالجة الاستثناءات بشكل منفصل لكل تطبيق، راجع [وضع التطبيقات المتعددة](multiapp.md)


## فئة معالجة الاستثناءات الافتراضية
يقوم webman افتراضيًا بمعالجة الاستثناءات باستخدام  فئة `support\exception\Handler`. يمكنك تغيير فئة معالجة الاستثناءات الافتراضية من خلال تعديل ملف الضبط `config/exception.php`. يجب على فئة معالجة الاستثناءات أن تنفذ واجهة `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * سجل السجل
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * اقرأ الاستجابة
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## جلب الاستجابة
يُستخدم الطريقة `render` في فئة معالجة الاستثناءات لجلب الاستجابة.

إذا كانت قيمة `debug` في ملف الضبط `config/app.php` تساوي `true` (مختصرا باسم `app.debug=true`)، سيتم إرجاع معلومات الاستثناء التفصيلية، في حين سيتم إرجاع معلومات الاستثناء على نحو مختصر إذا كان ذلك غير متوقع.

إذا كان من المتوقع أن يكون الاستجابة عبارة عن JSON، ستتم إرجاع معلومات الاستثناء بتنسيق JSON كما يلي
```json
{
    "code": "500",
    "msg": "معلومات الاستثناء"
}
```
إذا كان `app.debug=true`، سيتم إضافة حقل إضافي للبيانات الJSON يحمل اسم `trace` ويُظهر تفاصيل دقيقة حول الشريط الزمني للمكالمات.

يمكنك كتابة فئة معالجة الاستثناء الخاصة بك لتغيير المنطق الافتراضي لمعالجة الاستثناءات.

# استثناء العمل BusinessException
في بعض الأحيان نريد إيقاف الطلب في داخل دالة مدمجة وإرجاع رسالة خطأ إلى العميل، فيمكن القيام بذلك عن طريق رمي استثناء `BusinessException`.
مثلاً:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('مرحباً بكم في الفهرس');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('خطأ في المعلمات', 3000);
        }
    }
}
```

سترد النموذج أعلاه
```json
{"code": 3000, "msg": "خطأ في المعلمات"}
```

> **ملاحظة**
> لا يلزم التعامل مع استثناء BusinessException، سيقوم الإطار بالتقاطه تلقائيًا وسيُرجع الناتج المناسب وفقًا لنوع الطلب.

## استثناء العمل المخصص

إذا لم تتوافق الاستجابة المعروضة مع احتياجاتك وأردت تغيير `msg` إلى `message` على سبيل المثال، يمكنك تخصيص استثناء `MyBusinessException`.

قم بإنشاء `app/exception/MyBusinessException.php` مع المحتوى التالي
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // إذا كان الطلب عبارة عن JSON، سيتم إرجاع بيانات JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // في حالة عدم الطلب الخاص JSON، سيتم إرجاع صفحة
        return new Response(200, [], $this->getMessage());
    }
}
```

بهذه الطريقة، عند استدعاء العمل الخاص
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('خطأ في المعلمات', 3000);
```
سيتلقى الطلب الذي يقوم بالمطالبة بال JSON الخطأ الآتي على سبيل المثال
```json
{"code": 3000, "message": "خطأ في المعلمات"}
```

> **تلميح**
> نظرًا لأن استثناء BusinessException يتعلق بالاستثناءات التجارية (مثل أخطاء مدخلات المستخدم) التي يمكن التنبؤ بها، فإن الإطار لن يعتبرها استثناءً قاتلاً وبالتالي فلن يقوم بتسجيلها في السجلات.

## تلخيص
في أي وقت ترغب في إيقاف الطلب الحالي وإرجاع معلومات للعميل يمكنك النظر في استخدام استثناء `BusinessException`.
