# การแบ่งหน้า

# 1. การแบ่งหน้าโดยใช้ ORM ของ Laravel
illuminate/database ของ Laravel มีฟังก์ชั่นการแบ่งหน้าที่สะดวก

## การติดตั้ง
`composer require illuminate/pagination`

## การใช้
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## วิธีใช้งานของตัวคัดกรอง
|  วิธี  | คำอธิบาย  |
|  ----  |-----|
|$paginator->count()|รับจำนวนข้อมูลในหน้าปัจจุบัน|
|$paginator->currentPage()|รับหมายเลขหน้าปัจจุบัน|
|$paginator->firstItem()|รับหมายเลขของข้อมูลแรกในชุดข้อมูล|
|$paginator->getOptions()|รับตัวเลือกของตัวคัดกรอง|
|$paginator->getUrlRange($start, $end)|สร้าง URL ของหน้าที่ระบุ|
|$paginator->hasPages()|มีข้อมูลเพียงพอที่จะสร้างหลายหน้าหรือไม่|
|$paginator->hasMorePages()|มีหน้าเพิ่มเติมหรือไม่|
|$paginator->items()|รับรายการข้อมูลในหน้าปัจจุบัน|
|$paginator->lastItem()|รับหมายเลขของข้อมูลสุดท้ายในชุดข้อมูล|
|$paginator->lastPage()|รับหมายเลขหน้าสุดท้าย (ไม่สามารถใช้ได้ใน simplePaginate)|
|$paginator->nextPageUrl()|รับ URL ของหน้าถัดไป|
|$paginator->onFirstPage()|หน้าปัจจุบันเป็นหน้าแรกหรือไม่|
|$paginator->perPage()|รับจำนวนรายการที่แสดงในหน้าละหน้า|
|$paginator->previousPageUrl()|รับ URL ของหน้าก่อนหน้า|
|$paginator->total()|รับจำนวนข้อมูลในชุดข้อมูล (ไม่สามารถใช้ได้ใน simplePaginate)|
|$paginator->url($page)|รับ URL ของหน้าที่ระบุ|
|$paginator->getPageName()|รับชื่อพารามิเตอร์ที่ใช้เก็บหมายเลขหน้า|
|$paginator->setPageName($name)|ตั้งค่าชื่อพารามิเตอร์ที่ใช้เก็บหมายเลขหน้า|

> **หมายเหตุ**
> ไม่รองรับ `$paginator->links()` 方法

## คอมโพเนนต์การแบ่งหน้า
ใน webman ไม่สามารถใช้ `$paginator->links()` เพื่อสร้างปุ่มแบ่งหน้า แต่เราสามารถใช้คอมโพเนนต์อื่นๆ เช่น `jasongrimes/php-paginator` เพื่อสร้าง

**การติดตั้ง**
`composer require "jasongrimes/paginator:~1.0"`


**ทางด้านหลัง**
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

**เทมเพลท(ภาษา PHP)**
สร้างเทมเพลทชื่อ app/view/user/get.html
```html
<html>
<head>
  <!-- รองรับการทำงานกับสไตล์แบ่งหน้า Bootstrap แบบ Built-in -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**เทมเพลท (twig)** 
สร้างเทมเพลทชื่อ app/view/user/get.html
```html
<html>
<head>
  <!-- รองรับการทำงานกับสไตล์แบ่งหน้า Bootstrap แบบ Built-in -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**เทมเพลท (blade)** 
สร้างเทมเพลทชื่อ app/view/user/get.blade.php
```html
<html>
<head>
  <!-- รองรับการทำงานกับสไตล์แบ่งหน้า Bootstrap แบบ Built-in -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**เทมเพลท (thinkphp)**
สร้างเทมเพลทชื่อ app/view/user/get.html
```html
<html>
<head>
    <!-- รองรับการทำงานกับสไตล์แบ่งหน้า Bootstrap แบบ Built-in -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

ผลลัพธ์:
![](../../assets/img/paginator.png)

# 2. การแบ่งหน้าโดยใช้ ORM ของ Thinkphp
ไม่ต้องติดตั้งไลบรารีเพิ่มเติม หากได้ติดตั้ง think-orm ไว้แล้ว
## การใช้
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**เทมเพลท(thinkphp)**
```html
<html>
<head>
    <!-- รองรับการทำงานกับสไตล์แบ่งหน้า Bootstrap แบบ Built-in -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
