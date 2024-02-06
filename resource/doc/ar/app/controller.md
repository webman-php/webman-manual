# المتحكمون

وفقًا لمواصفات PSR4، يجب أن يبدأ مساحة اسم فئات المتحكمين بـ `plugin\{مُعرف_الإضافة}`، على سبيل المثال

قم بإنشاء ملف تحكم جديد بالطريقة التالية `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

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

عند زيارة `http://127.0.0.1:8787/app/foo/foo`، سيتم إرجاع الصفحة `hello index`

عند زيارة `http://127.0.0.1:8787/app/foo/foo/hello`، سيتم إرجاع الصفحة `hello webman`


## رابط الوصول
جميع مسارات عنوان URL للإضافات تبدأ بـ `/app`، متبوعة بمُعرف الإضافة، ثم اسم المتحكم والوظيفة المحددة.
على سبيل المثال، عنوان URL لتحكم المستخدم `plugin\foo\app\controller\UserController` هو `http://127.0.0.1:8787/app/foo/user`
