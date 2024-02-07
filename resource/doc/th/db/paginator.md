# หน้า

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

## วิธีการใช้งานของตัวกำหนดหน้า
|  วิธีการ   |  คำอธิบาย  |
|  ----  |-----|
|$paginator->count()|รับจำนวนข้อมูลในหน้าปัจจุบัน|
|$paginator->currentPage()|รับหมายเลขหน้าปัจจุบัน|
|$paginator->firstItem()|รับหมายเลขของข้อมูลตัวแรกในชุดข้อมูล|
|$paginator->getOptions()|รับตัวเลือกของตัวกำหนดหน้า|
|$paginator->getUrlRange($start, $end)|สร้าง URL ของระยะหน้าที่กำหนด|
|$paginator->hasPages()|มีข้อมูลเพียงพอที่จะสร้างหลายหน้าหรือไม่|
|$paginator->hasMorePages()|มีหน้าเพิ่มเติมที่สามารถแสดงได้หรือไม่|
|$paginator->items()|รับรายการข้อมูลในหน้าปัจจุบัน|
|$paginator->lastItem()|รับหมายเลขของข้อมูลสุดท้ายในชุดข้อมูล|
|$paginator->lastPage()|รับหมายเลขหน้าสุดท้าย (ไม่สามารถใช้ใน simplePaginate)|
|$paginator->nextPageUrl()|รับ URL ของหน้าถัดไป|
|$paginator->onFirstPage()|หน้าปัจจุบันเป็นหน้าแรกหรือไม่|
|$paginator->perPage()|รับจำนวนทั้งหมดของรายการในแต่ละหน้า|
|$paginator->previousPageUrl()|รับ URL ของหน้าก่อนหน้า|
|$paginator->total()|รับจำนวนข้อมูลทั้งหมดในชุดข้อมูล (ไม่สามารถใช้ใน simplePaginate)|
|$paginator->url($page)|รับ URL ของหน้าที่กำหนด|
|$paginator->getPageName()|รับชื่อพารามิเตอร์คำขอหน้าที่ใช้เก็บข้อมูลหน้า|
|$paginator->setPageName($name)|ตั้งค่าชื่อพารามิเตอร์คำขอหน้าที่ใช้เก็บข้อมูลหน้า|

> **หมายเหตุ**
> ไม่รองรับวิธีใช้ `$paginator->links()`

## คอมโพเนนต์การแบ่งหน้า
ใน webman เราไม่สามารถใช้ `$paginator->links()` เพื่อเรียกใช้ปุ่มแบ่งหน้า แต่เราสามารถใช้คอมโพเนนต์อื่นเพื่อเรียกใช้ ตัวอย่างเช่น `jasongrimes/php-paginator` 

**การติดตั้ง**
`composer require "jasongrimes/paginator:~1.0"`

**ฝั่งเซิร์ฟเวอร์**
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

**เทมเพลต(php ธรรมดา)**
สร้างเทมเพลตใหม่ app/view/user/get.html
```html
<html>
<head>
  <!-- รองรับสไตล์แบ่งหน้า Bootstrap ที่ซ้อนอยู่ภายใน -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**เทมเพลต(twig)** 
สร้างเทมเพลตใหม่ app/view/user/get.html
```html
<html>
<head>
  <!-- รองรับสไตล์แบ่งหน้า Bootstrap ที่ซ้อนอยู่ภายใน -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**เทมเพลต(blade)** 
สร้างเทมเพลตใหม่ app/view/user/get.blade.php
```html
<html>
<head>
  <!-- รองรับสไตล์แบ่งหน้า Bootstrap ที่ซ้อนอยู่ภายใน -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**เทมเพลต(thinkphp)**
สร้างเทมเพลตใหม่ app/view/user/get.html
```html
<html>
<head>
    <!-- รองรับสไตล์แบ่งหน้า Bootstrap ที่ซ้อนอยู่ภายใน -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

ผลลัพธ์ดังต่อไปนี้:
![](../../assets/img/paginator.png)

# 2. การแบ่งหน้าโดยใช้ ORM ของ Thinkphp
ไม่จำเป็นต้องติดตั้งไลบรารีเพิ่มเติม เพียงแค่ติดตั้ง think-orm เท่านั้น
## การใช้
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**เทมเพลต(thinkphp)**
```html
<html>
<head>
    <!-- รองรับสไตล์แบ่งหน้า Bootstrap ที่ซ้อนอยู่ภายใน -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
