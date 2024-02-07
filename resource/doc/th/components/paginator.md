# ชุดคอมโพเนนต์

## jasongrimes/php-paginator

### ที่อยู่โปรเจค

https://github.com/jasongrimes/php-paginator
  
### การติดตั้ง

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### การใช้งาน

สร้าง `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * รายการผู้ใช้
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
**เทมเพลต (php ต้นฉบับ)**
สร้างเทมเพลต app/view/user/get.html
```html
<html>
<head>
  <!-- รองรับสไตล์แบบ Bootstrap ในตัวเดียว -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**เทมเพลต (twig)**
สร้างเทมเพลต app/view/user/get.html
```html
<html>
<head>
  <!-- รองรับสไตล์แบบ Bootstrap ในตัวเดียว -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**เทมเพลต (blade)**
สร้างเทมเพลต app/view/user/get.blade.php
```html
<html>
<head>
  <!-- รองรับสไตล์แบบ Bootstrap ในตัวเดียว -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**เทมเพลต (thinkphp)**
สร้างเทมเพลต app/view/user/get.blade.php
```html
<html>
<head>
    <!-- รองรับสไตล์แบบ Bootstrap ในตัวเดียว -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

สามารถดูผลลัพธ์ได้ดังนี้：
![](../../assets/img/paginator.png)
  
### ข้อมูลเพิ่มเติม

เยี่ยมชม https://github.com/jasongrimes/php-paginator
