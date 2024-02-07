# পৃষ্ঠাবিভাজক বিষয়ক

## jasongrimes/php-paginator

### প্রকল্প ঠিকানা

https://github.com/jasongrimes/php-paginator

### ইনস্টলেশন

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### ব্যবহার

"app/controller/UserController.php" নতুন তৈরি করুন
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * ব্যবহারকারীর তালিকা
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

**টেম্পলেট (পিএইচপি নেটিভ)** "app/view/user/get.html" নতুন তৈরি করুন
```html
<html>
<head>
  <!-- অভ্যন্তরীণ Bootstrap পৃষ্ঠানুযায়ী স্টাইল সমর্থন -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**টেম্পলেট (টুইগ)** "app/view/user/get.html" নতুন তৈরি করুন
```html
<html>
<head>
  <!-- অভ্যন্তরীণ Bootstrap পৃষ্ঠানুযায়ী স্টাইল সমর্থন -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**টেম্পলেট (ব্লেড)** "app/view/user/get.blade.php" নতুন তৈরি করুন
```html
<html>
<head>
  <!-- অভ্যন্তরীণ Bootstrap পৃষ্ঠানুযায়ী স্টাইল সমর্থন -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**টেম্পলেট (থিংকপিএইচপি)** "app/view/user/get.blade.php" নতুন তৈরি করুন
```html
<html>
<head>
    <!-- অভ্যন্তরীণ Bootstrap পৃষ্ঠানুযায়ী স্টাইল সমর্থন -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

প্রভাব দেখুন:
![](../../assets/img/paginator.png)

### আরো বিস্তারিত

https://github.com/jasongrimes/php-paginator
