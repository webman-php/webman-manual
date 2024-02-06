# পৃষ্ঠাবিভাজক কম্পোনেন্ট

## jasongrimes/php-paginator

### প্রকল্প লিংক

https://github.com/jasongrimes/php-paginator
  
### ইনস্টলেশন

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### ব্যবহার

নতুন করে `app/controller/UserController.php` তৈরি করুন
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
**টেম্পলেট (পিএইচপি মূল)**
নতুন টেম্পলেট তৈরি করুন app/view/user/get.html
```html
<html>
<head>
  <!-- ইনবিল্ট সাপোর্ট ফর Bootstrap পেজিনেশন স্টাইল -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**টেম্পলেট (টুইগ)**
নতুন টেম্পলেট তৈরি করুন app/view/user/get.html
```html
<html>
<head>
  <!-- ইনবিল্ট সাপোর্ট ফর Bootstrap পেজিনেশন স্টাইল -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**টেম্পলেট (ব্লেড)**
নতুন টেম্পলেট তৈরি করুন app/view/user/get.blade.php
```html
<html>
<head>
  <!-- ইনবিল্ট সাপোর্ট ফর Bootstrap পেজিনেশন স্টাইল -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**টেম্পলেট (থিংকপিএইচপি)**
নতুন টেম্পলেট তৈরি করুন app/view/user/get.blade.php
```html
<html>
<head>
    <!-- ইনবিল্ট সাপোর্ট ফর Bootstrap পেজিনেশন স্টাইল -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

প্রভাব:
![](../../assets/img/paginator.png)
  
### অধিক তথ্য

https://github.com/jasongrimes/php-paginator

