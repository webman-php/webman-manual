webman هو إطار عمل PHP عالي الأداء مبني على Workerman. 以下是webman文档، يرجى التأكد من تفضيل جملة المفتاح إلى العربية:

# الشرح

## الحصول على كائن الطلب
يقوم webman تلقائياً بحقن كائن الطلب في الوسيطة الأولى لطريقة الإجراء، على سبيل المثال

**مثال**

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return response('hello ' . $name);
    }
}
```

من خلال كائن `$request`، يمكننا الحصول على أي بيانات ذات صلة بالطلب.

**في بعض الأحيان نريد الحصول على كائن الطلب `$request` في فئة أخرى، في هذه الحالة يُمكِننا استخدام دالة المساعدة `request()`**.

## الحصول على معلمات الطلب get

**الحصول على مصفوفة get بالكامل**
```php
$request->get();
```
إذا لم تحتوي الطلبات على معلمات get، ستُرجَع مصفوفة فارغة.

**الحصول على قيمة get محددة في المصفوفة**
```php
$request->get('name');
```
إذا لم تحتوي مصفوفة get على هذا القيمة، سترجع قيمة فارغة.

يُمكِنك أيضاً تمرير قيمة افتراضية كمعامل ثانوي في الدالة get، حيث إذا لم تجد القيمة المقابلة في مصفوفة get، ستعيد القيمة الافتراضية. على سبيل المثال:
```php
$request->get('name', 'tom');
```

## الحصول على معلمات الطلب post

**الحصول على مصفوفة post بالكامل**
```php
$request->post();
```
إذا لم تحتوي الطلبات على معلمات post، ستُرجَع مصفوفة فارغة.

**الحصول على قيمة post محددة في المصفوفة**
```php
$request->post('name');
```
إذا لم تحتوي مصفوفة post على هذا القيمة، سترجع قيمة فارغة.

مثل دالة get، يُمكِنك أيضاً تمرير قيمة افتراضية كمعامل ثانوي في دالة post، حيث إذا لم تجد القيمة المقابلة في مصفوفة post، ستعيد القيمة الافتراضية. على سبيل المثال:
```php
$request->post('name', 'tom');
```

## الحصول على الجسم الأصلي للطلب post
```php
$post = $request->rawBody();
```
هذه الوظيفة مشابهة لعملية `file_get_contents("php://input")` في `php-fpm`. يتم استخدامها للحصول على جسم الطلب الأصلي لـHTTP. يتم استخدام هذا عند الحصول على بيانات الطلبات post بتنسيق غير `application/x-www-form-urlencoded`.

## الحصول على الرأس
**الحصول على مصفوفة الرأس بالكامل**
```php
$request->header();
```
إذا لم تحتوي الطلبات على معلمات الرأس، ستُرجَع مصفوفة فارغة. يُرجى ملاحظة أن جميع المفاتيح تكون بحروف صغيرة.

**الحصول على قيمة محددة في مصفوفة الرأس**
```php
$request->header('host');
```
إذا لم تحتوي مصفوفة الرأس على هذ القيمة، سترجع قيمة فارغة. يُمكِنك أيضاً تمرير قيمة افتراضية كمعامل ثانوي في دالة الرأس، حيث إذا لم تجد القيمة المقابلة في مصفوفة الرأس، ستعيد القيمة الافتراضية. على سبيل المثال:
```php
$request->header('host', 'localhost');
```

## الحصول على الكوكيز
**الحصول على مصفوفة الكوكيز بالكامل**
```php
$request->cookie();
```
إذا لم تحتوي الطلبات على معلمات الكوكيز، ستُرجَع مصفوفة فارغة.

**الحصول على قيمة محددة في مصفوفة الكوكيز**
```php
$request->cookie('name');
```
إذا لم تحتوي مصفوفة الكوكيز على هذ القيمة، سترجع قيمة فارغة. يُمكِنك أيضاً تمرير قيمة افتراضية كمعامل ثانوي في دالة الكوكيز، حيث إذا لم تجد القيمة المقابلة في مصفوفة الكوكيز، ستعيد القيمة الافتراضية. على سبيل المثال:
```php
$request->cookie('name', 'tom');
```

## الحصول على كل المُدخلات
تشمل `post` و `get` مجتمع.

```php
$request->all();
```

## الحصول على قيمة مُحدد من المُدخل 
الحصول على قيمة مُحدد من مجتمع `post` و `get`. 
```php
$request->input('name', $default_value);
```

## الحصول على جزء من بيانات المُدخلات 
الحصول على جزء من بيانات `post` و `get`.
```php
// الحصول على مصفوفة تتكون من username و password، إذا لم تكن لديها قيم للمفتاح المُحدد فستتم تجاهلها
$only = $request->only(['username', 'password']);
// الحصول على جميع البيانات باستثناء avatar و age
$except = $request->except(['avatar', 'age']);
```
## الحصول على ملفات التحميل
**الحصول على مصفوفة ملفات التحميل بالكامل**
```php
$request->file();
```

مثل النموذج:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

سيُرجَع `$request->file()` بتنسيق مشابه للآتي:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
هو مصفوفة من مثيلات `webman\Http\UploadFile`. يورث الفصل `webman\Http\UploadFile` من الفئة الأساسية لـ PHP [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) ويوفر بعض الدوال العملية.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // ما إذا كان الملف صالحًا، على سبيل المثال true|false
            var_export($spl_file->getUploadExtension()); // احصل على امتداد الملف المحمل، على سبيل المثال 'jpg'
            var_export($spl_file->getUploadMimeType()); // احصل على نوع MIME الذي تم تحميله الى الملف، على سبيل المثال'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // احصل على رمز الخطأ المحمل، على سبيل المثال UPLOAD_ERR_NO_TMP_DIRUPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // احصل على اسم الملف المحمل، على سبيل المثال 'my-test.jpg'
            var_export($spl_file->getSize()); // احصل على حجم الملف، على سبيل المثال 13364، بالبايت
            var_export($spl_file->getPath()); // احصل على مجلد التحميل، على سبيل المثال '/tmp'
            var_export($spl_file->getRealPath()); // احصل على مسار الملف المؤقت، على سبيل المثال `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**ملاحظة:**

- يتم تسمية الملفات التي تم تحميلها كملفات مؤقتة، على سبيل المثال `/tmp/workerman.upload.SRliMu`
- حجم الملفات المحملة مقيدة بـ[defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) ، الذي يبلغ 10 ميجا بايت افتراضيًا، ويُمكِنك تغيير القيمة الافتراضية في ملف `config/server.php` باستخدام `max_package_size`.
- سيتم حذف الملفات المؤقتة تلقائياً بعد انتهاء الطلبات
- إذا لم تكن هناك ملفات محملة، فإن `$request->file()` ترجع مصفوفة فارغة
- لا تدعم تحميل الملفات استخدام طريقة `move_uploaded_file()`، بدلًا من ذلك يرجى استخدام طريقة `$file->move()`، كما في المثال التالي

### الحصول على ملف تحميل محدد
```php
$request->file('avatar');
``` 
إذا كان الملف موجودًا، سيتم إرجاع مثيل `webman\Http\UploadFile` المقابل للملف، وإلا فستُرجَع قيمة فارغة.

**مثال**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## الحصول على المضيف
الحصول على معلومات المضيف للطلب.
```php
$request->host();
``` 
إذا كان عنوان الطلب غير القياسي 80 أو 443، فقد يحتوي معلومات المضيف على منفذ، على سبيل المثال `example.com:8080`. إذا كنت لا تحتاج إلى المنفذ، يُمكِنك التمرير في الهدف الأول كـ `true`.

```php
$request->host(true);
``` 

## الحصول على طريقة الطلب
```php
$request->method();
``` 
يمكن أن تكون القيم إما `GET`، `POST`، `PUT`، `DELETE`، `OPTIONS`، `HEAD`.

## الحصول على معرف الطلب
```php
$request->uri();
``` 
ترجع معرف الطلب، بما في ذلك الجزء الرئيسي وجزء queryString.

## الحصول على مسار الطلب
```php
$request->path();
``` 
ترجع جزء الطلب الرئيسي.

## الحصول على جزء queryString من الطلب
```php
$request->queryString();
``` 
ترجع الجزء queryString من الطلب.

## الحصول على رابط الطلب
تُرْجَع الدالة `url()` برابط ليس له قيمة `Query`.
```php
$request->url();
``` 
يتم إرجاع نوعٍ مماثل لـ`//www.workerman.net/workerman-chat`

الدالة `fullUrl()` ترجع رابط مع قيمة `Query`.
```php
$request->fullUrl();
``` 
يتم إرجاع نوعٍ مماثل لـ`//www.workerman.net/workerman-chat?type=download`

> **ملاحظة**
> `url()` و `fullUrl()` لا تُرجع جزء البروتوكول (لا تُرجع http أو https).
> لأن إستخدام `//example.com` من هذا النوع سيتم التعرف بشكل تلقائي على بروتوكول موقعه الحالي، وسيُرسَل الطلب بنفس نقل البيانات http أو https.
> إذا كنت تستخدم خادمًا proxy nginx، يُرجى إضافة `proxy_set_header X-Forwarded-Proto $scheme;` إلى تكوين nginx، [يُرجى الرجوع إلى خادم proxy nginx](others/nginx-proxy.md)، بهذه الطريقة يُمكِن استخدام `$request->header('x-forwarded-proto');` لتحديد ما إذا كان الطلب http أو https، على سبيل المثال:
```php
echo $request->header('x-forwarded-proto'); // الناتج هو http أو https
``` 

## الحصول على نسخة الطلب
```php
$request->protocolVersion();
``` 
تُرجع السلسلة `
يمكنك الحصول على عنوان IP الحقيقي للعميل med-$safe_mode=true من طلب ،
عند استخدام الاستبدالات (مثل nginx) في المشروع ، قد يكون العنوان IP الذي يتم الحصول عليه من خلال `$request->getRemoteIp()` هو عنوان خادم الوكيل (مثل `127.0.0.1` `192.168.x.x`) وليس عنوان IP الحقيقي للعميل. في هذه الحالة ، يمكنك محاولة استخدام `$request->getRealIp()` للحصول على عنوان IP الحقيقي للعميل.

`$request->getRealIp()` سيحاول الحصول على العنوان IP الحقيقي من رؤوس HTTP `x-real-ip` ، `x-forwarded-for` ، `client-ip` ، `x-client-ip` ، `via`.

> نظرًا لأن رؤوس HTTP يمكن تزويرها بسهولة ، فإن العنوان IP الذي تم الحصول عليه باستخدام هذه الطريقة ليس 100٪ موثوقًا ، خاصةً إذا كان `$safe_mode` يساوي `false`. طريقة أكثر موثوقية للحصول على عنوان IP الحقيقي للعميل من خلال الوكيل هي معرفة عنوان خادم الوكيل الآمن ومعرفة بوضوح أي رأس HTTP يحمل العنوان IP الحقيقي. إذا كان العنوان IP الذي يرجعه `$request->getRemoteIp()` يتم تأكيده على أنه عنوان خادم وكيل آمن معروف ، فيمكن الحصول على العنوان IP الحقيقي باستخدام `$request->header('اسم رأس HTTP الذي يحمل العنوان IP الحقيقي')`.

## الحصول على عنوان IP الخادم
```php
$request->getLocalIp();
``` 

## الحصول على ميناء الخادم
```php
$request->getLocalPort();
``` 

## التحقق مما إذا كان الطلب هو طلب Ajax
```php
$request->isAjax();
``` 

## التحقق مما إذا كان الطلب هو طلب Pjax
```php
$request->isPjax();
``` 

## التحقق مما إذا كان الطلب يتوقع استلام استجابة JSON 
```php
$request->expectsJson();
``` 

## التحقق مما إذا كان العميل يقبل استلام استجابة JSON 
```php
$request->acceptJson();
```
## الحصول على اسم الإضافة المطلوبة
عندما لا تكون هناك طلبات إضافة ، يُعاد سلسلة فارغة ''
```php
$request->plugin;
```
> هذه الميزة تتطلب webman>=1.4.0

## الحصول على اسم التطبيق المطلوب
عندما يكون هناك تطبيق واحد ، يتم إعادة السلسلة فارغة دائمًا '' ؛ [عندما يكون هناك تطبيقات متعددة](multiapp.md) ، يتم إعادة اسم التطبيق 
```php
$request->app;
```

> بسبب أن الدوال الإغلاقية لا تنتمي إلى أي تطبيق ، يُعاد دائمًا السلسلة فارغة '' للطلبات القادمة من توجيهات الإغلاق.
> انظر [التوجيهات](route.md) للحصول على مزيد من المعلومات حول توجيهات الإغلاق.

## الحصول على اسم فئة التحكم المطلوبة
الحصول على اسم الفئة المقابلة للتحكم
```php
$request->controller;
```
يرجع شيء مشابه لـ `app\controller\IndexController`

> بسبب أن الدوال الإغلاقية لا تنتمي إلى أي تحكم ، يُعاد دائمًا السلسلة فارغة '' للطلبات القادمة من توجيهات الإغلاق.
> انظر [التوجيهات](route.md) للحصول على مزيد من المعلومات حول توجيهات الإغلاق.

## الحصول على اسم الطريقة المطلوبة
الحصول على اسم طريقة التحكم المطلوبة
```php
$request->action;
```
يرجع شيء مشابه لـ `index`

> بسبب أن الدوال الإغلاقية لا تنتمي إلى أي تحكم ، يُعاد دائمًاً السلسلة فارغة '' للطلبات القادمة من توجيهات الإغلاق.
> انظر [التوجيهات](route.md) للحصول على مزيد من المعلومات حول توجيهات الإغلاق.
