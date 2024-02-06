# مثال بسيط

## إرجاع سلسلة نصية
**إنشاء تحكم جديد**

إنشاء ملف `app/controller/UserController.php` كما يلي

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // الحصول على معلمة الاسم من طلب الـget، وإذا لم يتم تمريرها، فسيتم إعادة $default_name
        $name = $request->get('name', $default_name);
        // إرجاع سلسلة نصية إلى المتصفح
        return response('hello ' . $name);
    }
}
```

**الوصول**

في المتصفح، ادخل `http://127.0.0.1:8787/user/hello?name=tom`

سيتم إرجاع `hello tom` في المتصفح

## إرجاع json
تغيير ملف `app/controller/UserController.php` كما يلي

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
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**الوصول**

في المتصفح، ادخل `http://127.0.0.1:8787/user/hello?name=tom`

سيتم إرجاع `{"code":0,"msg":"ok","data":"tom""}` في المتصفح

استخدام دالة json المساعدة لإرجاع البيانات سيضيف تلقائيا رأس `Content-Type: application/json`

## إرجاع xml
بنفس الطريقة، باستخدام دالة المساعد `xml($xml)` سيتم إرجاع استجابة `xml` مع رأس `Content-Type: text/xml`.

حيث يمكن للمعامل `$xml` أن يكون سلسلة نصية `xml` أو كائن `SimpleXMLElement`.

## إرجاع jsonp
بنفس الطريقة، باستخدام دالة المساعد `jsonp($data, $callback_name = 'callback')` سيتم إرجاع استجابة `jsonp`.

## إرجاع عرض
تغيير ملف `app/controller/UserController.php` كما يلي

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
        return view('user/hello', ['name' => $name]);
    }
}
```

إنشاء ملف `app/view/user/hello.html` كما يلي

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

في المتصفح، ادخل `http://127.0.0.1:8787/user/hello?name=tom`
سيتم إرجاع صفحة html بمحتوى `hello tom`.

ملاحظة: webman يستخدم افتراضيًا بنية PHP الأصلية كقالب. إذا كنت ترغب في استخدام عارض العرض الجديد، راجع [العرض](view.md).
