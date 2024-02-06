## تخصيص الصفحة الخطأ 404
في حالة حدوث خطأ 404 في webman، سيتم عرض محتوى الملف `public/404.html` تلقائيًا، لذا يمكن للمطورين تغيير الملف `public/404.html` مباشرة.

إذا كنت ترغب في التحكم الديناميكي في محتوى الصفحة الخطأ 404، على سبيل المثال إرجاع بيانات JSON `{"code:"404", "msg":"404 not found"}` أثناء طلبات AJAX، أو إعادة استجابة قالب `app/view/404.html` أثناء طلبات الصفحة، يُرجى الرجوع إلى المثال التالي

> يُستخدم القالب الأساسي PHP في المثال التالي، ولكن يتم استخدام المبادئ الأساسية المتشابهة في القوالب الأخرى مثل `twig` `blade` و `think-tmplate`

**إنشاء ملف `app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**قم بإضافة الكود التالي إلى `config/route.php`**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // إعادة استجابة JSON أثناء طلبات AJAX
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // إعادة استجابة إلى قالب 404.html أثناء طلبات الصفحة
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## تخصيص الصفحة الخطأ 500
**إنشاء `app/view/500.html` جديد**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
قالب الخطأ المخصص:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**إنشاء** `app/exception/Handler.php` **(إذا كان الدليل غير موجود، يُرجى إنشاؤه يدويًا)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * رسم الاستجابة
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // إعادة استجابة بيانات JSON أثناء طلبات AJAX
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // إعادة استجابة إلى قالب 500.html أثناء طلبات الصفحة
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**تكوين `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
