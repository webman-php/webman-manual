# الاستجابة
الاستجابة فعلياً هي كائن `support\Response`، ولتسهيل إنشاء هذا الكائن، يوفر webman بعض دوال المساعدة.

## إرجاع استجابة أي
**مثال**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

تتم إنشاء دالة الاستجابة على النحو التالي:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

يمكنك أيضًا إنشاء كائن استجابة فارغًا أولاً، ثم استخدام ` $response->cookie()` ، `$response->header()` ، `$response->withHeaders()` ، `$response->withBody()` لتعيين محتوى الإرجاع في الموقع المناسب.
```php
public function hello(Request $request)
{
    // إنشاء كائن
    $response = response();
    
    // .... تجاهل منطق الأعمال
    
    // تعيين الكوكي
    $response->cookie('foo', 'value');
    
    // .... تجاهل منطق الأعمال
    
    // تعيين رؤوس http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... تجاهل منطق الأعمال

    // تعيين البيانات التي سيتم إرجاعها
    $response->withBody('البيانات المُراد إرجاعها');
    return $response;
}
```

## إرجاع json
**مثال**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
تنفيذ دالة json كالتالي:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## إرجاع XML
**مثال**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
يتم تنفيذ دالة xml على النحو التالي:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## إرجاع عرض
انشاء ملف جديد `app/controller/FooController.php` كالتالي

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

إنشاء ملف جديد `app/view/foo/hello.html` كالتالي

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

## إعادة توجيه
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
يتم تنفيذ دالة redirect كالتالي:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## إعداد الرأسين
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header Value' 
        ]);
    }
}
```
يمكن أيضًا استخدام الدالة `header` و `withHeaders` لتعيين رأس واحد أو مجموعة من الرؤوس.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
    }
}
```
يمكنك أيضًا تعيين الرؤوس مُعينة مسبقًا، ثم تحديد البيانات التي سيتم إرجاعها في النهاية.
```php
public function hello(Request $request)
{
    // إنشاء كائن
    $response = response();
    
    // .... تجاهل منطق الأعمال
  
    // تعيين رؤوس http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... تجاهل منطق الأعمال

    // تعيين البيانات التي سيتم إرجاعها
    $response->withBody('البيانات التي سيتم إرجاعها');
    return $response;
}
```

## تعيين الكوكي
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```
يمكنك أيضًا تعيين الكوكي مُعينًا مسبقًا، ثم تحديد البيانات التي سيتم إرجاعها في النهاية.
```php
public function hello(Request $request)
{
    // إنشاء كائن
    $response = response();
    
    // .... تجاهل منطق الأعمال
    
    // تعيين الكوكي
    $response->cookie('foo', 'value');
    
    // .... تجاهل منطق الأعمال

    // تعيين البيانات التي سيتم إرجاعها
    $response->withBody('البيانات التي سيتم إرجاعها');
    return $response;
}
```

معلمات دالة الكوكي الكاملة على النحو التالي:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## إرجاع تيار الملف
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- يدعم webman إرسال ملفات ضخمة للغاية
- بالنسبة للملفات الكبيرة (أكبر من 2 ميجابايت)، لن يقوم webman بقراءة الملف كاملاً داخل الذاكرة، بل سيقوم بقراءة الملف بشكل متفاوت وإرساله في وقت مناسب
- سيقوم webman بتحسين سرعة قراءة وإرسال الملف وفقًا لسرعة استقبال العميل، مما يضمن إرسال الملف بأسرع سرعة ممكنة مع تقليل استخدام الذاكرة إلى الحد الأدنى
- إرسال البيانات غير المُحددة، لن يؤثر على معالجة طلبات أخرى
- ستضيف دالة الملف `if-modified-since` رأسًا تلقائياً وستقوم بفحص `if-modified-since` رأس في الطلب التالي، إذا لم يتم تعديل الملف، فسيتم إرجاع الرد 304 مما يوفر عرض النطاق الترددي
- سيتم إرسال الملف تلقائياً باستخدام الرأس `Content-Type` المناسب لإرساله إلى المتصفح
- إذا كان الملف غير موجود ، فسيتم تحويله تلقائيًا إلى استجابة الخطأ 404


## تحميل الملف
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'اسم_الملف.ico');
    }
}
```
تكاد دالة download و file تكون متطابقة، باختلاف
1- بعد تعيين اسم الملف، سيتم تنزيل الملف بدلاً من عرضه في المتصفح
2- لن يتم فحص رأس `if-modified-since` عند استخدام دالة download
