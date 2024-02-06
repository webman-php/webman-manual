يدعم webman الوصول إلى الملفات الثابتة ، حيث يتم وضع الملفات الثابتة جميعها في الدليل "public" ، على سبيل المثال ، الوصول إلى "http://127.0.0.8787/upload/avatar.png" في الواقع يعني الوصول إلى "{المسار الرئيسي للمشروع}/public/upload/avatar.png".

> **ملاحظة**
> بدءًا من الإصدار 1.4 ، يدعم webman الإضافات التطبيقية ، حيث يعني الوصول إلى ملفات ثابتة تبدأ بـ "/app/xx/اسم الملف" في الواقع الوصول إلى الدليل "public" لإضافة التطبيق ، وبمعنى آخر ، webman >=1.4.0 لا يدعم الوصول إلى الدليل "{المسار الرئيسي للمشروع}/public/app/".
> لمزيد من المعلومات ، يرجى الرجوع إلى [الإضافات التطبيقية](./plugin/app.md)

### إيقاف دعم الملفات الثابتة
إذا كانت هناك حاجة لإيقاف دعم الملفات الثابتة ، يتعين فتح `config/static.php` وتغيير الخيار `enable` إلى القيمة false. بمجرد إيقاف التشغيل ، سيتم إرجاع 404 لجميع طلبات الوصول إلى الملفات الثابتة.

### تغيير دليل الملفات الثابتة
تستخدم webman افتراضيًا الدليل "public" كدليل للملفات الثابتة. إذا كنت بحاجة إلى تغيير ذلك ، يرجى تعديل دالة المساعد `public_path()` في `support/helpers.php`.

### حاجز ملفات الوسيطة الثابتة
يأتي webman مع حاجز ملفات وسيطة ثابتة افتراضيًا ، والذي يقع في `app/middleware/StaticFile.php`.
بعض الأحيان ، نحتاج إلى تنفيذ بعض المعالجات على الملفات الثابتة ، مثل إضافة رؤوس HTTP للملفات الثابتة أو منع الوصول إلى الملفات التي تبدأ بنقطة (`.`)

محتوى `app/middleware/StaticFile.php` يمكن أن يكون على النحو التالي:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // يمنع الوصول إلى الملفات الخفية التي تبدأ بنقطة
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 محظور</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // إضافة رؤوس HTTP للتعامل مع الوصول العابر
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
إذا كنت بحاجة إلى استخدام هذا الوسيط ، يجب تمكينه من خلال الذهاب إلى `config/static.php` وتشغيل الخيار `middleware`.
