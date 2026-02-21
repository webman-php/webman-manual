# التوجيه (Routing)
## قواعد التوجيه الافتراضية
قاعدة التوجيه الافتراضية لـ webman هي `http://127.0.0.1:8787/{المتحكم}/{الإجراء}`.

المتحكم الافتراضي هو `app\controller\IndexController`، والإجراء الافتراضي هو `index`.

على سبيل المثال، عند الوصول:
- `http://127.0.0.1:8787` سيتجه افتراضياً إلى دالة `index` في فئة `app\controller\IndexController`
- `http://127.0.0.1:8787/foo` سيتجه افتراضياً إلى دالة `index` في فئة `app\controller\FooController`
- `http://127.0.0.1:8787/foo/test` سيتجه افتراضياً إلى دالة `test` في فئة `app\controller\FooController`
- `http://127.0.0.1:8787/admin/foo/test` سيتجه افتراضياً إلى دالة `test` في فئة `app\admin\controller\FooController` (راجع [تطبيقات متعددة](multiapp.md))

بالإضافة إلى ذلك، بدءاً من webman 1.4، يُدعم توجيه افتراضي أكثر تعقيداً، مثل:
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

عند الرغبة في تغيير توجيه طلب معين، يرجى تعديل ملف التكوين `config/route.php`.

لتعطيل التوجيه الافتراضي، أضف التكوين التالي في نهاية ملف `config/route.php`:
```php
Route::disableDefaultRoute();
```

## توجيه الإغلاق (Closure)
أضف كود التوجيه التالي في `config/route.php`:
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});

```
> **ملاحظة**
> بما أن دوال الإغلاق لا تنتمي لأي متحكم، فإن `$request->app` و `$request->controller` و `$request->action` ستكون كلها سلاسل فارغة.

عند الوصول إلى العنوان `http://127.0.0.1:8787/test`، سيعيد السلسلة `test`.

> **ملاحظة**
> يجب أن يبدأ مسار التوجيه بـ `/`، مثلاً:

```php
use support\Request;
// استخدام خاطئ
Route::any('test', function (Request $request) {
    return response('test');
});

// استخدام صحيح
Route::any('/test', function (Request $request) {
    return response('test');
});
```


## توجيه الفئات
أضف كود التوجيه التالي في `config/route.php`:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
عند الوصول إلى العنوان `http://127.0.0.1:8787/testclass`، سيعيد نتيجة دالة `test` في فئة `app\controller\IndexController`.


## توجيه التعليقات التوضيحية

تعريف المسارات باستخدام التعليقات التوضيحية على دوال المتحكم دون التكوين في `config/route.php`.

> **ملاحظة**
> تتطلب هذه الميزة webman-framework >= v2.2.0

### الاستخدام الأساسي

```php
namespace app\controller;
use support\annotation\route\Get;
use support\annotation\route\Post;

class UserController
{
    #[Get('/user/{id}')]
    public function show($id)
    {
        return "user $id";
    }

    #[Post('/user')]
    public function store()
    {
        return 'created';
    }
}
```

التعليقات التوضيحية المتاحة: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (أي طريقة). يجب أن تبدأ المسارات بـ `/`. يمكن للبارامتر الثاني تحديد اسم المسار لاستخدامه في `route()` لتوليد الرابط.

### تعليقات بلا مسار: تقييد طرق HTTP للطرق الافتراضية

عند عدم تحديد مسار، يتم فقط تقييد طرق HTTP المسموحة لهذا الإجراء؛ يبقى مسار التوجيه الافتراضي قيد الاستخدام:

```php
#[Post]
public function create() { ... }  // POST فقط، المسار يبقى /user/create

#[Get]
public function index() { ... }   // GET فقط
```

يمكن دمج تعليقات متعددة للسماح بعدة طرق:

```php
#[Get]
#[Post]
public function form() { ... }  // يسمح بـ GET و POST
```

طرق HTTP غير المعلن عنها ستعيد 405.

التعليقات المتعددة ذات المسارات تُسجّل كمسارات منفصلة: `#[Get('/a')] #[Post('/b')]` ينشئ مسارَي GET /a و POST /b.

### بادئة مجموعة المسارات

استخدم `#[RouteGroup]` على الفئة لإضافة بادئة لجميع مسارات الدوال:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // المسار الفعلي: /api/v1/user/{id}
    public function show($id) { ... }
}
```

### طرق HTTP مخصصة واسم المسار

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### البرمجيات الوسيطة

`#[Middleware]` على المتحكم أو الدالة تنطبق على مسارات التعليقات التوضيحية، بنفس استخدام `support\annotation\Middleware`.


## معاملات المسار
إذا كانت هناك معاملات في المسار، يمكن مطابقتها باستخدام `{key}` وستُمرر النتيجة للمعامل المقابل في دالة المتحكم (بدءاً من المعامل الثاني)، مثلاً:
```php
// يطابق /user/123 و /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('تم استلام المعامل'.$id);
    }
}
```

مزيد من الأمثلة:
```php
use support\Request;
// يطابق /user/123، لا يطابق /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// يطابق /user/foobar، لا يطابق /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// يطابق /user و /user/123 و /user/abc   [] يدل على اختياري
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// يطابق جميع الطلبات التي تبدأ بـ /user/
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// يطابق جميع طلبات options   : يليها تعبير منتظم لتحديد نمط المعامل المسمى
Route::options('[{path:.+}]', function () {
    return response('');
});
```
ملخص الاستخدام المتقدم

> بناء جملة `[]` في توجيه Webman يتعامل بشكل أساسي مع أجزاء المسار الاختيارية أو مطابقة المسارات الديناميكية، مما يتيح تعريف هياكل مسارات وقواعد مطابقة أكثر تعقيداً
>
> يُستخدم `:` لتحديد التعابير النمطية


## مجموعات المسارات

أحياناً تحتوي المسارات على الكثير من البادئات المتشابهة، في هذه الحالة يمكن استخدام مجموعات المسارات لتبسيط التعريف. مثلاً:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
يعادل
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

استخدام المجموعات المتداخلة
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## برمجيات وسيطة المسارات
يمكننا تعيين برمجية وسيطة لمسار معين أو مجموعة مسارات. مثلاً:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# مثال استخدام خاطئ (صالح في webman-framework >= 1.5.7)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# مثال استخدام صحيح
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## التوجيه المبنى على الموارد
```php
Route::resource('/test', app\controller\IndexController::class);

// تحديد مسارات الموارد
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// مسارات موارد غير معرفة
// عند الوصول إلى notify يصبح مساراً من نوع any /test/notify أو /test/notify/{id} و routeName يكون test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| الطريقة | URI                 | الإجراء   | اسم المسار    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## توليد الرابط
> **ملاحظة**
> لا يُدعم حالياً توليد رابط المسارات للمجموعات المتداخلة

مثلاً، للمسار:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
يمكننا استخدام الطريقة التالية لتوليد رابط هذا المسار:
```php
route('blog.view', ['id' => 100]); // النتيجة /blog/100
```

عند استخدام روابط المسارات في الواجهة، يمكن استخدام هذه الطريقة بحيث بغض النظر عن تغييرات قواعد التوجيه، سيتم توليد الرابط تلقائياً، وتجنب الحاجة لتعديل الكثير من ملفات الواجهة بسبب تغييرات عناوين المسارات.

## الحصول على معلومات المسار

يمكننا الحصول على معلومات مسار الطلب الحالي عبر كائن `$request->route`، مثلاً:

```php
$route = $request->route; // يعادل $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **ملاحظة**
> إذا لم يتطابق الطلب الحالي مع أي مسار مُكوّن في `config/route.php`، فستكون `$request->route` قيمة null، أي عند استخدام التوجيه الافتراضي ستكون `$request->route` قيمة null.

## التعامل مع خطأ 404
عند عدم العثور على المسار، يتم افتراضياً إرجاع رمز الحالة 404 وإخراج محتوى 404.

إذا أراد المطورون التدخل في العملية عند عدم العثور على المسار، يمكنهم استخدام مسار الاحتياطي المقدم من webman `Route::fallback($callback)`. مثلاً، الكود التالي يعيد التوجيه للصفحة الرئيسية عند عدم العثور على المسار:
```php
Route::fallback(function(){
    return redirect('/');
});
```
مثال آخر هو إرجاع استجابة JSON عند عدم وجود المسار، وهو مفيد جداً عند استخدام webman كنقطة نهاية API:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## إضافة برمجية وسيطة لـ 404

افتراضياً لا تمر طلبات 404 بأي برمجية وسيطة. إذا احتجت إضافة برمجية وسيطة لطلبات 404، راجع الكود التالي:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

رابط ذو صلة [تخصيص صفحات الخطأ 404 و 500](others/custom-error-page.md)

## تعطيل المسار الافتراضي

```php
// تعطيل المسار الافتراضي للمشروع الرئيسي، لا يؤثر على إضافات التطبيق
Route::disableDefaultRoute();
// تعطيل مسار تطبيق admin للمشروع الرئيسي، لا يؤثر على إضافات التطبيق
Route::disableDefaultRoute('', 'admin');
// تعطيل المسار الافتراضي لإضافة foo، لا يؤثر على المشروع الرئيسي
Route::disableDefaultRoute('foo');
// تعطيل المسار الافتراضي لتطبيق admin لإضافة foo، لا يؤثر على المشروع الرئيسي
Route::disableDefaultRoute('foo', 'admin');
// تعطيل المسار الافتراضي للمتحكم [\app\controller\IndexController::class, 'index']
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## تعطيل المسار الافتراضي بالتعليقات التوضيحية

يمكنك تعطيل المسار الافتراضي لمتحكم باستخدام التعليقات التوضيحية، مثلاً:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

#[DisableDefaultRoute]
class IndexController
{
    public function index()
    {
        return 'index';
    }
}
```

بالمثل، يمكن أيضاً تعطيل المسار الافتراضي لدالة معينة في متحكم باستخدام التعليقات التوضيحية، مثلاً:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

class IndexController
{
    #[DisableDefaultRoute]
    public function index()
    {
        return 'index';
    }
}
```

## واجهة المسارات
```php
// تعيين مسار لأي طلب طريقة لـ $uri
Route::any($uri, $callback);
// تعيين مسار لطلب GET لـ $uri
Route::get($uri, $callback);
// تعيين مسار لطلب POST لـ $uri
Route::post($uri, $callback);
// تعيين مسار لطلب PUT لـ $uri
Route::put($uri, $callback);
// تعيين مسار لطلب PATCH لـ $uri
Route::patch($uri, $callback);
// تعيين مسار لطلب DELETE لـ $uri
Route::delete($uri, $callback);
// تعيين مسار لطلب HEAD لـ $uri
Route::head($uri, $callback);
// تعيين مسار لطلب OPTIONS لـ $uri
Route::options($uri, $callback);
// تعيين مسارات لأنواع طلبات متعددة دفعة واحدة
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// مسارات المجموعة
Route::group($path, $callback);
// مسارات الموارد
Route::resource($path, $callback, [$options]);
// تعطيل المسار الافتراضي
Route::disableDefaultRoute($plugin = '');
// مسار الاحتياطي، تعيين احتياطي المسار الافتراضي
Route::fallback($callback, $plugin = '');
// الحصول على جميع معلومات المسارات
Route::getRoutes();
```
إذا لم يكن هناك مسار مطابق للـ URI (بما في ذلك المسار الافتراضي)، ولم يتم تعيين مسار احتياطي، سيتم إرجاع 404.

## ملفات تكوين مسارات متعددة
إذا أردت إدارة المسارات باستخدام عدة ملفات تكوين، مثلاً [تطبيقات متعددة](multiapp.md) حيث لكل تطبيق تكوين مسارات خاص، يمكن تحميل ملفات تكوين المسارات الخارجية باستخدام `require`.
مثلاً في `config/route.php`:

```php
<?php

// تحميل تكوين المسارات لتطبيق admin
require_once app_path('admin/config/route.php');
// تحميل تكوين المسارات لتطبيق api
require_once app_path('api/config/route.php');

```
