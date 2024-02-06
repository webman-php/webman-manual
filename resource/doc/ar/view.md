## العرض
يستخدم webman افتراضيًا بنية اللغة الأصلية للـ PHP كقالب، ويعمل بأفضل أداء عند استخدام `opcache`. بالإضافة إلى قالب PHP الأصلي، يقدم webman أيضًا محركات القوالب [Twig](https://twig.symfony.com/doc/3.x/) و [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) و [think-template](https://www.kancloud.cn/manual/think-template/content).

## تشغيل opcache
عند استخدام العرض، نوصي بشدة بتشغيل الخيارين `opcache.enable` و `opcache.enable_cli` في ملف الـ php.ini لضمان أفضل أداء لمحرك القوالب.

## تثبيت Twig
1. تثبيت عبر composer

    `composer require twig/twig`

2. قم بتعديل ملف التكوين `config/view.php` ليكون كالتالي
    ```php
    <?php
    use support\view\Twig;

    return [
        'handler' => Twig::class
    ];
    ```
    > **ملاحظة**
    > يمكن إرسال خيارات التكوين الأخرى من خلال الـ options، مثل  

    ```php
    return [
        'handler' => Twig::class,
        'options' => [
            'debug' => false,
            'charset' => 'utf-8'
        ]
    ];
    ```

## تثبيت Blade
1. تثبيت عبر composer

    ```
    composer require psr/container ^1.1.1 webman/blade
    ```

2. قم بتعديل ملف التكوين `config/view.php` ليكون كالتالي
    ```php
    <?php
    use support\view\Blade;

    return [
        'handler' => Blade::class
    ];
    ```

## تثبيت think-template
1. تثبيت عبر composer

    `composer require topthink/think-template`

2. قم بتعديل ملف التكوين `config/view.php` ليكون كالتالي
    ```php
    <?php
    use support\view\ThinkPHP;

    return [
        'handler' => ThinkPHP::class,
    ];
    ```
    > **ملاحظة**
    > يمكن إرسال خيارات التكوين الأخرى من خلال الـ options، مثل

    ```php
    return [
        'handler' => ThinkPHP::class,
        'options' => [
            'view_suffix' => 'html',
            'tpl_begin' => '{',
            'tpl_end' => '}'
        ]
    ];
    ```

## مثال عن محرك القالب PHP الأصلي
أنشئ ملف `app/controller/UserController.php` كما يلي

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

أنشى ملف `app/view/user/hello.html` كما يلي

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

## مثال عن محرك القوالب Twig
قم بتعديل ملف التكوين `config/view.php` ليكون كالتالي
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` كما يلي

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

الملف `app/view/user/hello.html` كما يلي

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

يمكن الاطلاع على المزيد من الوثائق [هنا](https://twig.symfony.com/doc/3.x/) لـ Twig.

## Blade مثال
قم بتعديل ملف التكوين `config/view.php` ليكون كالتالي
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` كما يلي

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

الملف `app/view/user/hello.blade.php` كما يلي

> يُلاحظ أن اسم ملف الـ blade-template ينتهي بالامتداد `.blade.php`

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

يمكن الاطلاع على المزيد من الوثائق [هنا](https://learnku.com/docs/laravel/8.x/blade/9377) لـ Blade.

## مثال عن محرك القوالب think-template
قم بتعديل ملف التكوين `config/view.php` ليكون كالتالي
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` كما يلي

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

الملف `app/view/user/hello.html` كما يلي

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

يمكن الاطلاع على المزيد من الوثائق [هنا](https://www.kancloud.cn/manual/think-template/content) لـ think-template.

## تكليف القوالب
بالإضافة إلى استخدام `view(القالب, مصفوفة المتغيرات)` لتكليف القوالب، يمكننا أيضًا في أي مكان بالاتصال بـ `View::assign()` لتكليف القوالب. على سبيل المثال:

```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` مفيدة في بعض الحالات، مثل عرض معلومات المستخدم الحالي في رأس كل صفحة. إذا كان من الإزعاج تكليف هذه المعلومات في كل صفحة باستخدام `view('القالب', ['user_info' => 'معلومات المستخدم']);`، يمكن التغلب على ذلك من خلال الحصول على معلومات المستخدم من خلال middleware ومن ثم الاتصال بـ `View::assign()` لتكليف القالب بمعلومات المستخدم.

## حول مسارات ملفات القوالب
#### الكنترولر
عند استدعاء الكنترولر `view('اسم_القالب', []);`، يتم البحث عن ملفات القوالب وفق القواعد التالية:

1. في حالة عدم وجود تطبيقات متعددة، يُستخدم الملف تحت `app/view/`
2. [في حالة التطبيقات المتعددة](multiapp.md)، يُستخدم الملف تحت `app/اسم_التطبيق/view/`

بشكل عام، إذا كان `$request->app` فارغًا، سيتم استخدام ملفات القوالب تحت `app/view/`، وإلا سيتم استخدام ملفات القوالب تحت `app/{$request->app}/view/`.

#### الدالة الإغلاق
بما أن `$request->app` فارغ، ولا ينتمي إلى أي تطبيق، يُستخدم الملفات تحت `app/view/`، على سبيل المثال في ملف التأجير `config/route.php` الذي يحدد المسار

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
ستُستخدم `app/view/user.html` كملف قالب (عند استخدام قالب blade، يكون ملف القالب `app/view/user.blade.php`).


#### تحديد التطبيق
من أجل إمكانية إعادة استخدام ملفات القوالب في حالة التطبيقات المتعددة، يقدم `view(القالب, البيانات, التطبيق = null)` الباراميتر الثالث `$app` لتحديد استخدام ملفات القالب في دليل التطبيق الذي ترغب فيه. على سبيل المثال `view('user', [], 'admin');` سيقوم بقوة باستخدام ملفات القوالب تحت `app/admin/view/`.

## توسيع twig

> **ملاحظة**
> هذه الميزة تتطلب webman-framework>=1.4.8

يمكننا توسيع مثيل twig للأوجه القالبية بتقديم الدالة `view.extension` في التكوين، مثل `config/view.php` كما يلي
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // إضافة الأمتداد
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // إضافة الفلتر
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // إضافة الدالة
    }
];
```

## توسيع blade
> **ملاحظة**
> هذه الميزة تتطلب webman-framework>=1.4.8
بالمثل، يمكننا توسيع مثيل القوالب blade بتقديم الدالة `view.extension` في التكوين، مثل `config/view.php` كما يلي

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // إضافة الأمر
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## استخدام مكونات blade

> **ملاحظة**
> يتطلب هذه الميزة webman/blade>=1.5.2

من المفترض أنه في حالة الحاجة إلى إضافة مكون Alert

**قم بإنشاء `app/view/components/Alert.php`**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**قم بإنشاء `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` سيكون على هذا النحو**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

وبهذا، يكون مكون Alert مُعد وجاهز للتحديد في القوالب. على سبيل المثال:
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```


