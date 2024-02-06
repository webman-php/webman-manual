# مكون الصفحة

## jasongrimes/php-paginator

### عنوان المشروع

https://github.com/jasongrimes/php-paginator

### التثبيت

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### الاستخدام

أنشئ `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * قائمة المستخدمين
     */
    public function get(Request $request)
    {
        $total_items = 1000;
        $items_perPage = 50;
        $current_page = (int)$request->get('page', 1);
        $url_pattern = '/user/get?page=(:num)';
        $paginator = new Paginator($total_items, $items_perPage, $current_page, $url_pattern);
        return view('user/get', ['paginator' => $paginator]);
    }
}
```
**القالب (نصي باللغة الإنجليزية)**
أنشئ قالبًا بالمسار التالي app/view/user/get.html
```html
<html>
<head>
  <!-- يدعم النمط الافتراضي للتقسيم باستخدام Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```
**القالب (تويج)**
أنشئ قالبًا بالمسار التالي app/view/user/get.html
```html
<html>
<head>
  <!-- يدعم النمط الافتراضي للتقسيم باستخدام Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```
**القالب (شفرة)**
أنشئ قالبًا بالمسار التالي app/view/user/get.blade.php
```html
<html>
<head>
  <!-- يدعم النمط الافتراضي للتقسيم باستخدام Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```
**القالب (التفكير)**
أنشئ قالبًا بالمسار التالي app/view/user/get.blade.php
```html
<html>
<head>
    <!-- يدعم النمط الافتراضي للتقسيم باستخدام Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

النتيجة كما يلي:
![](../../assets/img/paginator.png)

### المزيد من المحتوى

زيارة https://github.com/jasongrimes/php-paginator
