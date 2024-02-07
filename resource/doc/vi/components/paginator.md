# Thành phần phân trang

## jasongrimes/php-paginator

### Địa chỉ dự án

https://github.com/jasongrimes/php-paginator
  
### Cài đặt

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Sử dụng

Tạo mới `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Danh sách người dùng
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
**Mẫu (nguyên tố PHP)**
Tạo mới mẫu app/view/user/get.html
```html
<html>
<head>
  <!-- Hỗ trợ tích hợp kiểu dáng phân trang Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Mẫu (twig)**
Tạo mới mẫu app/view/user/get.html
```html
<html>
<head>
  <!-- Hỗ trợ tích hợp kiểu dáng phân trang Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Mẫu (blade)**
Tạo mới mẫu app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Hỗ trợ tích hợp kiểu dáng phân trang Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Mẫu (thinkphp)**
Tạo mới mẫu app/view/user/get.blade.php
```html
<html>
<head>
    <!-- Hỗ trợ tích hợp kiểu dáng phân trang Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Hiệu ứng như sau:
![](../../assets/img/paginator.png)
  
### Thêm nội dung

Truy cập https://github.com/jasongrimes/php-paginator
