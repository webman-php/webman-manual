## التوجيه
## قواعد التوجيه الافتراضية
قاعدة التوجيه الافتراضية في webman هي `http://127.0.0.1:8787/{المتحكم}/{الإجراء}`.

المتحكم الافتراضي هو `app\controller\IndexController`، والإجراء الافتراضي هو `index`.

على سبيل المثال، عندما تقوم بزيارة:
- `http://127.0.0.1:8787` ستزور طريق `app\controller\IndexController` وإجراء `index`
- `http://127.0.0.1:8787/foo` سيؤدي إلى زيارة طريق `app\controller\FooController` وإجراء `index`
- `http://127.0.0.1:8787/foo/test` سيؤدي إلى زيارة طريق `app\controller\FooController` وإجراء `test`
- `http://127.0.0.1:8787/admin/foo/test` سيؤدي إلى زيارة طريق `app\admin\controller\FooController` وإجراء `test` (انظر [تطبيق متعدد](multiapp.md))

بالإضافة إلى ذلك، بدءاً من الإصدار 1.4، يدعم webman قواعد توجيه افتراضية أكثر تعقيدًا، على سبيل المثال
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

عندما ترغب في تغيير توجيه الطلبات، يرجى تعديل ملف التكوين `config/route.php`.

إذا كنت ترغب في تعطيل توجيه الافتراضي، يمكنك إضافة الضبط التالي في نهاية ملف التكوين `config/route.php`:
```php
Route::disableDefaultRoute();
```

## التوجيه الغير المتاحة
يمكنك إضافة رمز الطريقة المصغرة في `config/route.php` كالآتي
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **يرجى الملاحظة**
> نظرًا لأن الوظيفة المصغرة ليست جزءًا من أي متحكم، فإن `$request->app` `$request->controller` `$request->action` ستكون جميعها سلاسل فارغة.

عندما يتم زيارة العنوان `http://127.0.0.1:8787/test`، ستُرجع سلسلة نصية `test`.

> **يرجى الملاحظة**
> يجب أن يبدأ مسار التوجيه بـ `/`، على سبيل المثال

```php
// الاستخدام الخاطئ
Route::any('test', function ($request) {
    return response('test');
});

// الاستخدام الصحيح
Route::any('/test', function ($request) {
    return response('test');
});
```


## التوجيه الصنفي
يمكنك إضافة رمز التوجيه الصنفي في `config/route.php` كالآتي
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
عندما يتم زيارة العنوان `http://127.0.0.1:8787/testclass`، ستُرجع قيمة الوظيفة `test` في فئة `app\controller\IndexController`.

## معلمات التوجيه
إذا كانت هناك معلمات في التوجيه، يمكن استخدام `{key}` للتطابق، وسيُمرر نتيجة التطابق إلى معلمات وظيفة المتحكم المقابلة (ابتداء من البارامتر الثاني على التوالي)، على سبيل المثال:
```php
// تطابق /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('تم استقبال المعلمة'.$id);
    }
}
```

مزيد من الأمثلة:
```php
// تطابق /user/123، لا يتطابق /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// تطابق /user/foobar، لا يتطابق /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// تطابق /user /user/123 و /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// تطابق كل الطلبات المستندة إلى الخيارات
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## تجميع التوجيه
أحيانًا يحتوي التوجيه على بادئة مشتركة كبيرة، فيمكننا استخدام تجميع التوجيه لتبسيط النطاق. على سبيل المثال:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('إنشاء');});
   Route::any('/edit', function ($request) {return response('تحرير');});
   Route::any('/view/{id}', function ($request, $id) {return response("عرض $id");});
}
```
تعادل
```php
Route::any('/blog/create', function ($request) {return response('إنشاء');});
Route::any('/blog/edit', function ($request) {return response('تحرير');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("عرض $id");});
```

استخدام التجميع المتداخل

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('إنشاء');});
      Route::any('/edit', function ($request) {return response('تحرير');});
      Route::any('/view/{id}', function ($request, $id) {return response("عرض $id");});
   });  
}
```

## الوساطة في التوجيه
يمكننا تعيين وسيطًا لطريق واحد أو مجموعة من الطرق.
على سبيل المثال:

```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('إنشاء');});
   Route::any('/edit', function () {return response('تحرير');});
   Route::any('/view/{id}', function ($request, $id) {response("عرض $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **يرجى الملاحظة**: 
> في webman-framework <= 1.5.6 عندما يتم تطبيق `->middleware()` على تجميع group، يجب أن تكون الطريقة الحالية تقع تحت تلك الفئة.

```php
# مثال على الاستخدام الخاطئ (عندما يكون webman-framework >= 1.5.7، يصبح هذا الاستخدام صالحًا)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('إنشاء');});
      Route::any('/edit', function ($request) {return response('تحرير');});
      Route::any('/view/{id}', function ($request, $id) {return response("عرض $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# مثال على الاستخدام الصحيح
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('إنشاء');});
      Route::any('/edit', function ($request) {return response('تحرير');});
      Route::any('/view/{id}', function ($request, $id) {return response("عرض $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## توجيه الموارد
```php
Route::resource('/test', app\controller\IndexController::class);

// تحديد توجيه الموارد
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// توجيه الموارد غير المعرف
// مثل زيارة notify تصبح any-type route /test/notify أو /test/notify/{id} routeName is test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| الفعل   | الرابط                 | الإجراء   | اسم الطريق    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT   | /test/{id}           | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery| recovery | test.recovery |

## إنشاء عنوان
> **يرجى الملاحظة** 
> حالياً لا يدعم إنتاج عناوين للتوجيه المدمج

على سبيل المثال عند استخدام التوجيه:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
يمكننا استخدام الأسلوب التالي لإنشاء عنوان لهذا التوجيه:
```php
route('blog.view', ['id' => 100]); // النتيجة /blog/100
```

يمكن استخدام هذا الأسلوب عند استخدام عناوين التوجيه في العرض، وبهذه الطريقة سيتم إنتاج العنوان تلقائيًا بغض النظر عن تغييرات قواعد التوجيه، لتجنب الحاجة لتغيير كثير من ملفات العرض بسبب تغيير عناوين التوجيه.

## الحصول على معلومات التوجيه
> **يرجى الملاحظة**
> يتطلب webman-framework >= 1.3.2

يمكننا الحصول على معلومات التوجيه الحالي من خلال كائن `$request->route`، على سبيل المثال

```php
$route = $request->route; // يعادل $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // يتطلب هذه الميزة webman-framework >= 1.3.16
}
```

> **يرجى الملاحظة**
> إذا لم تتطابق الطلبات الحالية مع أي من توجيهات config/route.php، فإن `$request->route` سيكون القيمة `null`، وهذا يعني أن التوجيه الافتراضي سيكون null

## معالجة الأخطاء 404
عندما لايتم العثور على التوجيه، يتم إرجاع حالة 404 افتراضية ونص `public/404.html` يتم عرضه.

إذا كان المطور يرغب في التدخل عندما لايتم العثور على توجيه، يمكنه استخدام وظيفة التوجيه البديلة المقدمة من webman `Route::fallback($callback)`، كما في الشيفرة التالية وهي إعادة التوجيه إلى الصفحة الرئيسية عندما لايتم العثور على توجيه.
```php
Route::fallback(function(){
    return redirect('/');
});
```
على سبيل المثال، عندما لا يتم العثور على توجيه، يتم إرجاع بيانات json، وهذا يعد مناسبًا لاستخدام webman كواجهة برمجية.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 لم يتم العثور']);
});
```

الروابط ذات الصلة [صفحات الأخطاء المخصصة](others/custom-error-page.md)
## واجهة التوجيه
```php
// تعيين مسار $uri لأي طلب لأي طريقة
Route::any($uri, $callback);
// تعيين مسار $uri للطلبات من نوع GET
Route::get($uri, $callback);
// تعيين مسار $uri للطلبات من نوع POST
Route::post($uri, $callback);
// تعيين مسار $uri للطلبات من نوع PUT
Route::put($uri, $callback);
// تعيين مسار $uri للطلبات من نوع PATCH
Route::patch($uri, $callback);
// تعيين مسار $uri للطلبات من نوع DELETE
Route::delete($uri, $callback);
// تعيين مسار $uri للطلبات من نوع HEAD
Route::head($uri, $callback);
// تعيين مسار $uri لعدة أنواع من الطلبات معًا
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// تجميع المسارات
Route::group($path, $callback);
// مسارات الموارد
Route::resource($path, $callback, [$options]);
// تعطيل المسارات
Route::disableDefaultRoute($plugin = '');
// المسار البديل، تعيين المسار الافتراضي للتعويض عن الطلبات
Route::fallback($callback, $plugin = '');
```
إذا لم يكن هناك مسار مطابق لـ uri (بما في ذلك المسار الافتراضي) ولم يتم تعيين مسار بديل، سيتم إرجاع رمز الخطأ 404.

## ملفات تكوين المسارات المتعددة
إذا كنت ترغب في استخدام ملفات تكوين مسارات متعددة لإدارة المسارات، مثل [تطبيقات متعددة](multiapp.md) حيث يوجد لكل تطبيق تكوين مسارات خاص به، يمكنك استخدام الـ `require` لتحميل ملفات تكوين المسارات الخارجية.
على سبيل المثال في `config/route.php`
```php
<?php

// تحميل تكوين مسارات التطبيق الإداري
require_once app_path('admin/config/route.php');
// تحميل تكوين مسارات التطبيق الـ API
require_once app_path('api/config/route.php');

```
