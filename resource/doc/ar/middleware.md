# الوسيط
يُستخدم الوسيط عادةً لاعتراض الطلبات أو الاستجابات. على سبيل المثال، تحقق هوية المستخدم بشكل موحد قبل تنفيذ التحكم، مثل توجيه المستخدم إلى صفحة تسجيل الدخول في حالة عدم تسجيل الدخول، أو إضافة رأس رسالة معينة في الاستجابة، أو حتى إحصاء نسبة طلب URI معينة وما إلى ذلك.

## نموذج الوسيط "نموذج البصل"

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── الطلب ───────────────────────> التحكم ─ الاستجابة ───────────────────────────> العميل
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
الوسيط والتحكم يشكلان نموذج البصل الكلاسيكي، حيث يُشبه الوسيط طبقات من قشرة البصل والتحكم هو نواة البصل. كما هو موضح في الشكل، يمر الطلب مثل السهم من خلال الوسيط 1 و 2 و 3 للوصول إلى التحكم، يُرجع التحكم استجابة، ثم يعبر الاستجابة بترتيب 3 و 2 و 1 من الوسيط ليتم إرجاعها في النهاية للعميل. وهذا يعني أنه يمكننا في كل وسيط أن نحصل على الطلب والاستجابة أيضًا.

## اعتراض الطلب
في بعض الأحيان، لا نرغب في أن يصل طلب معين إلى طبقة التحكم، على سبيل المثال، عندما نجد أن المستخدم الحالي لم يقم بتسجيل الدخول في واجهة وسيط التحقق من الهوية، فيمكننا مباشرة اعتراض الطلب وإرجاع استجابة تسجيل الدخول. وبالتالي، يكون هذا السير التالي مشابهًا للشكل التالي.

```
                              
            ┌───────────────────────────────────────────────────────────┐
            │                     middleware1                           │ 
            │     ┌───────────────────────────────────────────────┐     │
            │     │               Authentication Middleware       │     │
            │     │          ┌──────────────────────────────┐     │     │
            │     │          │         middleware3          │     │     │
            │     │          │     ┌──────────────────┐     │     │     │
            │     │          │     │                  │     │     │     │
   ── Request ───────────┐   │     │    Controller    │     │     │     │
            │     │ Response │     │                  │     │     │     │
   <─────────────────────┘   │     └──────────────────┘     │     │     │
            │     │          │                              │     │     │
            │     │          └──────────────────────────────┘     │     │
            │     │                                               │     │
            │     └───────────────────────────────────────────────┘     │
            │                                                           │
            └───────────────────────────────────────────────────────────┘
```

كما هو موضح في الشكل، يتم الوصول إلى وسيط التحقق من الهوية ويتم إنشاء استجابة تسجيل الدخول، ثم تعبر الاستجابة من وسيط التحقق من الهوية مرة أخرى ويتم إرجاعها إلى المتصفح.

## واجهة الوسيط
يجب على الوساطة تنفيذ واجهة `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
وبمعنى آخر، يجب تنفيذ الطريقة `process`، ويجب على الطريقة `process` إرجاع كائن `Webman\Http\Response`، حيث إن من المقرر أن يتم إنشاء هذا الكائن من قبل `$handler($request)` (سيستمر الطلب في عبور البصل)، ويمكن أيضًا أن يكون منشأ بواسطة وظائف المساعد مثل `response()`، `json()`، `xml()`، `redirect()` وما إلى ذلك (سيتوقف الطلب عن عبور البصل).

## الحصول على الطلب والاستجابة في الوسيط
يمكننا الحصول على الطلب وكذلك الاستجابة بداخل الوسيط، لذا يتم تقسيم الوسيط الداخلي إلى ثلاثة أقسام.
1. مرحلة عبور الطلب، أي مرحلة معالجة الطلب قبل التحكم
2. مرحلة معالجة الطلب من التحكم، أي مرحلة معالجة الطلب
3. مرحلة خروج الاستجابة، أي مرحلة معالجة الاستجابة بعد معالجة الطلب

تجسد هذه المراحل الثلاث داخل الوسيط على النحو التالي:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'هذه مرحلة عبور الطلب، أي مرحلة معالجة الطلب قبل التحكم';
        
        $response = $handler($request); // الاستمرار في التوجه نحو نواة البصل، حتى الوصول إلى التحكم والحصول على الاستجابة
        
        echo 'هذه مرحلة خروج الاستجابة، أي مرحلة معالجة الاستجابة بعد معالجة الطلب';
        
        return $response;
    }
}
```
## مثال: وسيط المصادقة
إنشاء ملف `app / middleware / AuthCheckTest.php` (إذا لم يكن الدليل موجودًا ، فيرجى إنشاؤه بنفسك) على النحو التالي:

```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // تم تسجيل الدخول بالفعل ، الطلب يستمر في عبور الطبقات
            return $handler($request);
        }

        // الحصول على الأساليب التي لا تحتاج إلى تسجيل الدخول مستندة إلى الانعكاس
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // الوصول إلى الطرق يتطلب تسجيل الدخول
        if (!in_array($request->action, $noNeedLogin)) {
            // اعتراض الطلب ، وإرجاع استجابة إعادة توجيه ، وتوقف الطلب عن مرور الطبقات
            return redirect('/user/login');
        }

        // لا حاجة لتسجيل الدخول ، الطلب يستمر في عبور الطبقات
        return $handler($request);
    }
}
```

قم بإنشاء متحكم `app / controller / UserController.php` الجديد
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * الأساليب التي لا تحتاج إلى تسجيل الدخول
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'تم تسجيل الدخول بنجاح']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'نجاح', 'data' => session('user')]);
    }
}
```
> **ملاحظة**
>`$noNeedLogin` يوجد بها الأساليب التي يمكن الوصول إليها بدون تسجيل الدخول في المتحكم الحالي

أضف الوسيط العالمي في `config/middleware.php` على النحو التالي:
```php
return [
    // الوسائط العالمية
    '' => [
        // ... يُغفل في هذا المكان الوسائط الأخرى
        app\middleware\AuthCheckTest::class,
    ]
];
```

باستخدام وسيط المصادقة ، يمكننا التركيز على كتابة رموز الأعمال في طبقة المتحكم دون القلق بشأن ما إذا كان المستخدم قد قام بتسجيل الدخول أم لا.

## مثال: وسيط طلبات العبور
أنشئ ملفًا `app / middleware / AccessControlTest.php` (إذا لم يكن الدليل موجودًا ، فيرجى إنشاؤه بنفسك) على النحو التالي:

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // إذا كانت الطلبات options ، فإنه يعيد استجابة فارغة ، وإلا ينتقل عبر الطبقات ويحصل على استجابة
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // إضافة رؤوس HTTP ذات الصلة بعبور النطاق
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **تلميح**
> يمكن أن تولد عمليات العبور طلبات options ، لكننا لا نريد أن تصل طلبات options إلى المتحكم ، لذا قمنا بإعادة استجابة فارغة مباشرة (استجابة ('')) للتحقق المسبق من الطلبات.
> إذا كانت واجهة البرمجة الشبكية الخاصة بك بحاجة إلى إعداد توجيهات ، يرجى استخدام `Route :: any(..)` أو `Route :: add (['POST'، 'OPTIONS'] ، ..)`.

أضف وسيط السيطرة على الوصول في `config/middleware.php` على النحو التالي:
```php
return [
    // الوسائط العالمية
    '' => [
        // ... يُغفل في هذا المكان الوسائط الأخرى
        app\middleware\AccessControlTest::class,
    ]
];
```

> **ملاحظة**
> إذا كانت طلبات الواجهة البرمجية الشبكية تخصيص رؤوس الطلبات الخاصة بها ، يجب إضافة هذه الرؤوس الخاصة المخصصة في الوسيط باسم `Access-Control-Allow-Headers` ، وإلا ستظهر خطأ `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## الشرح

 - الوسيط مقسم إلى وسائط عالمية ووسائط التطبيق (وسائط التطبيق تعمل فقط في وضع التطبيقات المتعددة ، انظر [التطبيقات المتعددة](multiapp.md)) ووسائط التوجيه
 - حاليًا لا يدعم الوسيط الفردي للتحكم (ولكن يمكن تحقيق وظيفة الوسيط الفردي من خلال فحص `$request->controller` في الوسيط)
 - موقع ملف تكوين الوسيط في `config/middleware.php`
 - تكوين الوسيط العالمي في المفتاح `''`
 - تكوين الوسيط التطبيق في اسم التطبيق الفعلي ، مثل

```php
return [
    // الوسائط العالمية
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // وسائط التطبيق API (وسائط التطبيق تعمل فقط في وضع التطبيقات المتعددة)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## وسائط التوجيه

يمكننا تعيين وسيطًا واحدًا أو مجموعة من وسائط التوجيه لطريق واحد أو مجموعة معينة من الطرق.
على سبيل المثال ، في `config/route.php` يمكنك إضافة التكوين التالي:

```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## تمرير معلمات إلى الوسائط من خلال دالة الإعداد(route->setParams)

**تكوين مسار `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**الوسيط (نفترض أنه عالمي)**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // الطريق الافتراضي $request->route هو nul ، لذا يجب فحص $request->route إذا كان فارغًا
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## تمرير معلمات إلى المتحكم من الوسائط

في بعض الأحيان يحتاج المتحكم لاستخدام البيانات التي تم إنشاؤها في الوسيط ، وفي هذه الحالة يمكننا إضافة خاصية إلى كائن `$request` لتمرير المعلمات. على سبيل المثال:

**وسيط**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**التحكم:**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```
## الحصول على معلومات طريق الوسيط

> **ملاحظة**
> يتطلب webman-framework >= 1.3.2

يمكننا استخدام `$request->route` للحصول على كائن الطريق، من خلال استدعاء الأساليب المقابلة للحصول على المعلومات الصحيحة.

**تكوين الطريق**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**الوسيط**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // إذا لم يطابق الطلب أي طريق (باستثناء الطريق الافتراضي)، فإن $request->route سيكون null
        // على سبيل المثال، إذا طلب المستخدم عنوان "/user/111"، سيتم طباعة المعلومات التالية
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET','POST','PUT','DELETE','PATCH','HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController','view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111
        }
        return $handler($request);
    }
}
```

> **ملاحظة**
> يتطلب استخدام الأسلوب `$route->param()` webman-framework >= 1.3.16


## الحصول على الإستثناء في الوسيط
> **ملاحظة**
> يتطلب webman-framework >= 1.3.15

قد يحدث إستثناء أثناء معالجة الأعمال، يمكن استخدام `$response->exception()` في الوسيط للحصول على الإستثناء.

**تكوين الطريق**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('اختبار الإستثناء');
});
```

**الوسيط:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## الوسيط العالمي
> **ملاحظة**
> تتطلب هذه الميزة webman-framework >= 1.5.16

يؤثر الوسيط العالمي لمشروع الرئيسي فقط دون أي تأثير على [الإضافات التطبيقية](app/app.md). في بعض الأحيان نريد إضافة وسيط يؤثر عالمياً على جميع الإضافات بما في ذلك الرئيسية، يمكننا استخدام الوسيط العالمي.

يمكنك تكوين الوسيط العالمي في `config/middleware.php` كما يلي:
```php
return [
    '@' => [ // إضافة الوسيط العالمي للمشروع الرئيسي وجميع الإضافات
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // إضافة الوسيط العالمي للمشروع الرئيسي فقط
];
```

> **تلميح**
> يمكن تكوين الوسيط العالمي `@` ليس فقط في مشروع رئيسي بل في إضافة معينة، على سبيل المثال، يمكن تكوين الوسيط العالمي `@` في ملف `plugin/ai/config/middleware.php`، بحيث يؤثر على المشروع الرئيسي وجميع الإضافات.

## إضافة وسيط لإضافة معينة

> **ملاحظة**
> تتطلب هذه الميزة webman-framework >= 1.5.16

في بعض الأحيان نريد إضافة وسيط ل[الإضافات التطبيقية](app/app.md) دون الحاجة إلى تغيير كود الإضافة (لأن الترقيات ستؤدي إلى إغلاقها). في هذه الحالة، يمكننا تكوين الوسيط في المشروع الرئيسي ليؤثر على الإضافة.

يمكنك تكوين الوسيط في `config/middleware.php` كما يلي:
```php
return [
    'plugin.ai' => [], // إضافة وسيط للإضافة AI
    'plugin.ai.admin' => [], // إضافة وسيط لنموذج الإضافة AI
];
```

> **تلميح**
> يمكن أيضاً تكوين نفس الإعداد في إضافة معينة للتأثير على الإضافات الأخرى، على سبيل المثال، يمكن تكوين إعدادات الوسيط المذكورة أعلاه في ملف `plugin/foo/config/middleware.php` للتأثير على الإضافة AI.
