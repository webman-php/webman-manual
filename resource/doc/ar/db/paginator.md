# الترقيم

# 1. طريقة تقسيم الصفحات مستندة على ORM في Laravel
توفر `illuminate/database` في Laravel وظيفة تقسيم مريحة.

## التثبيت
`composer require illuminate/pagination`

## الاستخدام
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## طرق تقسيم الصفحات المتاحة
|  الوظيفة   | الوصف  |
|  ----  |-----|
|$paginator->count()|الحصول على إجمالي بيانات الصفحة الحالية|
|$paginator->currentPage()|الحصول على رقم الصفحة الحالية|
|$paginator->firstItem()|الحصول على رقم البيانات الأول في مجموعة النتائج|
|$paginator->getOptions()|الحصول على خيارات تقسيم الصفحات|
|$paginator->getUrlRange($start, $end)|إنشاء مجموعة من الصفحات مع URL المحدد|
|$paginator->hasPages()|ما إذا كانت هناك بيانات كافية لإنشاء عدة صفحات|
|$paginator->hasMorePages()|ما إذا كانت هناك صفحات أخرى متاحة للعرض|
|$paginator->items()|الحصول على بنود البيانات الحالية|
|$paginator->lastItem()|الحصول على رقم آخر بيانات في مجموعة النتائج|
|$paginator->lastPage()|الحصول على رقم الصفحة الأخيرة (غير متاحة في simplePaginate)|
|$paginator->nextPageUrl()|الحصول على عنوان URL للصفحة التالية|
|$paginator->onFirstPage()|ما إذا كانت الصفحة الحالية هي الصفحة الأولى|
|$paginator->perPage()|الحصول على العدد الإجمالي للبيانات في كل صفحة|
|$paginator->previousPageUrl()|الحصول على عنوان URL للصفحة السابقة|
|$paginator->total()|الحصول على إجمالي بيانات مجموعة النتائج (غير متاحة في simplePaginate)|
|$paginator->url($page)|الحصول على رابط URL المحدد للصفحة|
|$paginator->getPageName()|الحصول على اسم معلمة الاستعلام المستخدمة لتخزين رقم الصفحة|
|$paginator->setPageName($name)|ضبط اسم معلمة الاستعلام المستخدمة لتخزين رقم الصفحة|

> **ملاحظة**
> لا يدعم طريقة `$paginator->links()`.

## مكون تقسيم الصفحات
في webman ، لا يمكن استخدام طريقة `$paginator->links()` لعرض أزرار التقسيم. ومع ذلك ، يمكن استخدام مكونات أخرى للعرض ، مثل `jasongrimes/php-paginator`.

**التثبيت**
`composer require "jasongrimes/paginator:~1.0"`


**الجانب الخلفي**
```php
<?php
namespace app\controller;

use JasonGrimes\Paginator;
use support\Request;
use support\Db;

class UserController
{
    public function get(Request $request)
    {
        $per_page = 10;
        $current_page = $request->input('page', 1);
        $users = Db::table('user')->paginate($per_page, '*', 'page', $current_page);
        $paginator = new Paginator($users->total(), $per_page, $current_page, '/user/get?page=(:num)');
        return view('user/get', ['users' => $users, 'paginator'  => $paginator]);
    }
}
```

**القالب (PHP النقي)**
إنشاء القالب في app/view/user/get.html
```html
<html>
<head>
  <!-- الدعم المدمج لأنماط تقسيم Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**القالب (twig)** 
إنشاء القالب في app/view/user/get.html
```html
<html>
<head>
  <!-- الدعم المدمج لأنماط تقسيم Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**القالب (blade)** 
إنشاء القالب في app/view/user/get.blade.php
```html
<html>
<head>
  <!-- الدعم المدمج لأنماط تقسيم Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**القالب (thinkphp)**
إنشاء القالب في app/view/user/get.html
```html
<html>
<head>
    <!-- الدعم المدمج لأنماط تقسيم Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

النتيجة كما يلي:
![](../../assets/img/paginator.png)

# 2. طريقة تقسيم الصفحات استنادًا إلى ORM في Thinkphp
لا حاجة لتثبيت مكتبة إضافية، فقط تثبيت think-orm يكفي.
## الاستخدام
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**القالب (thinkphp)**
```html
<html>
<head>
    <!-- الدعم المدمج لأنماط تقسيم Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
