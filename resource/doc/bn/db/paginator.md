# পৃষ্ঠাবিভাজন

# 1. Laravel ORM-এর ভিত্তিতে পৃষ্ঠাবিভাজন পদ্ধতি
Laravel-এর `illuminate/database` প্যাকেজ সহজেই পৃষ্ঠাবিভাজন সুবিধা প্রদান করে।

## ইনস্টলেশন
`composer require illuminate/pagination`

## ব্যবহার
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## পৃষ্ঠাবিভাজনকারী ইনস্ট্যান্স মেথড
|  মেথড   | বর্ণনা  |
|  ----  |-----|
|$paginator->count()|বর্তমান পৃষ্ঠার মোট ডেটা সংখ্যা পেতে|
|$paginator->currentPage()|বর্তমান পৃষ্ঠার পাতার নম্বর পেতে|
|$paginator->firstItem()|ফলাফল সেটে প্রথম ডেটার নম্বর পেতে|
|$paginator->getOptions()|পৃষ্ঠাবিভাজকারীর অপশন পেতে|
|$paginator->getUrlRange($start, $end)|দেয়া পৃষ্ঠা পরিসীমা এর URL ইতোয়া|
|$paginator->hasPages()|পর্যাপ্ত ডেটা আছে তার জন্য একাধিক পৃষ্ঠা তৈরি করা উচিত না|
|$paginator->hasMorePages()|আরো পৃষ্ঠা আছে তা দেখানোর জন্য|
|$paginator->items()|বর্তমান পৃষ্ঠার ডেটা আইটেমের জন্য|
|$paginator->lastItem()|ফলাফল সেটে শেষ ডেটার নম্বর পেতে|
|$paginator->lastPage()|শেষ পৃষ্ঠার পাতার নম্বর পেতে (simplePaginate এ পাওয়া যায় না)|
|$paginator->nextPageUrl()|পরবর্তী পৃষ্ঠার URL পেতে|
|$paginator->onFirstPage()|বর্তমান পৃষ্ঠা কি প্রথম পৃষ্ঠা তা পরীক্ষা করা|
|$paginator->perPage()|প্রতি পৃষ্ঠায় প্রদর্শন করা মোট সংখ্যা পেতে|
|$paginator->previousPageUrl()|আগের পৃষ্ঠার URL পেতে|
|$paginator->total()|ফলাফল সেটে মোট ডেটা সংখ্যা পেতে (simplePaginate এ পাওয়া যায় না)|
|$paginator->url($page)|নির্দিষ্ট পৃষ্ঠার URL পেতে|
|$paginator->getPageName()|পাতার নম্বর সংরক্ষণের জন্য ব্যবহৃত প্রশ্নের প্রতিনিধি নাম পেতে|
|$paginator->setPageName($name)|পাতার নম্বর সংরক্ষণের জন্য ব্যবহৃত প্রশ্নের প্রতিনিধি নাম সেট করতে|

> **দ্য দ্য]

## পৃষ্ঠাবিভাজক
webman-এ `$paginator->links()` মেথড ব্যবহার করা যায় না পৃষ্ঠাবিভাজক বোতিসিত্তিক, তবে আমরা অন্যান্য কম্পোনেন্ট ব্যবহার করতে পারি, যেমন `jasongrimes/php-paginator`।

**ইনস্টলেশন**
`composer require "jasongrimes/paginator:~1.0"`

**ব্যবহারকারী-অনুচ্ছেদ**
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

**টেম্পলেট (মৌলিক PHP)**
app/view/user/get.html নতুন টেম্পলেট তৈরি করুন
```html
<html>
<head>
  <!-- ইনবিল্ট সাপোর্ট বুটস্ট্রাপ পৃষ্ঠাবিভাজন স্টাইল -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator; ?>

</body>
</html>
```

**টেম্পলেট (twig)** 
app/view/user/get.html নতুন টেম্পলেট তৈরি করুন
```html
<html>
<head>
  <!-- ইনবিল্ট সাপোর্ট বুটস্ট্রাপ পৃষ্ঠাবিভাজন স্টাইল -->
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
app/view/user/get.blade.php নতুন টেম্পলেট তৈরি করুন
```html
<html>
<head>
  <!-- ইনবিল্ট সাপোর্ট বুটস্ট্রাপ পৃষ্ঠাবিভাজন স্টাইল -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**টেম্পলেট (thinkphp)**
app/view/user/get.html নতুন টেম্পলেট তৈরি করুন
```html
<html>
<head>
    <!-- ইনবিল্ট সাপোর্ট বুটস্ট্রাপ পৃষ্ঠাবিভাজন স্টাইল -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

প্রভাব:
![](../../assets/img/paginator.png)

# 2. Thinkphp ORM-এর ভিত্তিতে পৃষ্ঠাবিভাজন পদ্ধতি
অতিরিক্ত লাইব্রেরি ইনস্টলেশন প্রয়োজন নেই, শুধুমাত্র think-orm ইনস্টল হলেই চলবে
## ব্যবহার
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**টেম্পলেট (thinkphp)**
```html
<html>
<head>
    <!-- ইনবিল্ট সাপোর্ট বুটস্ট্রাপ পৃষ্ঠাবিভাজন স্টাইল -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
