# Trang

# 1. Cách phân trang dựa trên ORM Laravel
`illuminate/database` của Laravel cung cấp chức năng phân trang tiện lợi.

## Cài đặt
`composer require illuminate/pagination`

## Sử dụng
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Phương thức thực thể phân trang
|  Phương thức  | Mô tả  |
|  ----  |-----|
|$paginator->count()|Lấy tổng số dữ liệu trên trang hiện tại|
|$paginator->currentPage()|Lấy số trang hiện tại|
|$paginator->firstItem()|Lấy số thứ tự của dữ liệu đầu tiên trong kết quả|
|$paginator->getOptions()|Lấy các tùy chọn phân trang|
|$paginator->getUrlRange($start, $end)|Tạo URL cho một phạm vi trang cụ thể|
|$paginator->hasPages()|Kiểm tra xem có đủ dữ liệu để tạo nhiều trang không|
|$paginator->hasMorePages()|Kiểm tra xem có thêm trang nào để hiển thị không|
|$paginator->items()|Lấy các mục dữ liệu của trang hiện tại|
|$paginator->lastItem()|Lấy số thứ tự của dữ liệu cuối cùng trong kết quả|
|$paginator->lastPage()|Lấy số trang cuối cùng (không sử dụng được trong simplePaginate)|
|$paginator->nextPageUrl()|Lấy URL của trang kế tiếp|
|$paginator->onFirstPage()|Kiểm tra xem trang hiện tại có phải là trang đầu tiên không|
|$paginator->perPage()|Lấy số lượng dữ liệu hiển thị trên mỗi trang|
|$paginator->previousPageUrl()|Lấy URL của trang trước đó|
|$paginator->total()|Lấy tổng số dữ liệu trong kết quả (không sử dụng được trong simplePaginate)|
|$paginator->url($page)|Lấy URL của một trang cụ thể|
|$paginator->getPageName()|Lấy tên của tham số truy vấn được sử dụng để lưu trữ số trang|
|$paginator->setPageName($name)|Đặt tên của tham số truy vấn được sử dụng để lưu trữ số trang|

> **Chú ý**
> Không hỗ trợ phương thức `$paginator->links()`

## Cụm phân trang
Trong webman, không thể sử dụng phương thức `$paginator->links()` để render các nút phân trang, tuy nhiên, chúng ta có thể sử dụng các thành phần khác để render, ví dụ như `jasongrimes/php-paginator`.

**Cài đặt**
`composer require "jasongrimes/paginator:~1.0"`


**Backend**
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
        return view('user/get', ['users' => $users, 'paginator' => $paginator]);
    }
}
```

**Template (nguyên bản PHP)**
Tạo template mới app/view/user/get.html
```html
<html>
<head>
  <!-- Hỗ trợ tùy chỉnh kiểu phân trang Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Template (twig)**
Tạo template mới app/view/user/get.html
```html
<html>
<head>
  <!-- Hỗ trợ tùy chỉnh kiểu phân trang Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Template (blade)**
Tạo template mới app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Hỗ trợ tùy chỉnh kiểu phân trang Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Template (thinkphp)**
Tạo template mới app/view/user/get.html
```html
<html>
<head>
    <!-- Hỗ trợ tùy chỉnh kiểu phân trang Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Hiệu ứng như sau:
![](../../assets/img/paginator.png)

# 2. Cách phân trang dựa trên ORM Thinkphp
Không cần cài đặt thư viện bổ sung, chỉ cần cài think-orm là đủ
## Sử dụng
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Template (thinkphp)**
```html
<html>
<head>
    <!-- Hỗ trợ tùy chỉnh kiểu phân trang Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
