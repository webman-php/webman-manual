# Ngôn ngữ đa

Ngôn ngữ đa sử dụng thành phần [symfony/translation](https://github.com/symfony/translation).

## Cài đặt
```
composer require symfony/translation
```

## Tạo gói ngôn ngữ
webman mặc định đặt gói ngôn ngữ trong thư mục `resource/translations` (nếu không có, vui lòng tạo mới), nếu bạn muốn thay đổi thư mục, vui lòng thiết lập trong `config/translation.php`.
Mỗi ngôn ngữ tương ứng với một thư mục con, các định nghĩa ngôn ngữ mặc định được đặt trong `messages.php`. Ví dụ:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Tất cả các tệp ngôn ngữ đều trả về một mảng như sau:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Cấu hình

`config/translation.php`

```php
return [
    // Ngôn ngữ mặc định
    'locale' => 'zh_CN',
    // Ngôn ngữ dự phòng, nếu không tìm thấy bản dịch trong ngôn ngữ hiện tại, hệ thống sẽ thử tìm trong ngôn ngữ dự phòng
    'fallback_locale' => ['zh_CN', 'en'],
    // Thư mục lưu trữ tệp ngôn ngữ
    'path' => base_path() . '/resource/translations',
];
```

## Dịch

Sử dụng phương thức `trans()` để dịch.

Tạo tệp ngôn ngữ `resource/translations/zh_CN/messages.php` như sau:
```php
return [
    'hello' => 'Xin chào thế giới!',
];
```

Tạo tệp `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Xin chào thế giới!
        return response($hello);
    }
}
```

Truy cập `http://127.0.0.1:8787/user/get` sẽ trả về "Xin chào thế giới!"

## Thay đổi ngôn ngữ mặc định

Để chuyển đổi ngôn ngữ, sử dụng phương thức `locale()`.

Tạo tệp ngôn ngữ mới `resource/translations/en/messages.php` như sau:
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Chuyển đổi ngôn ngữ
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
Truy cập `http://127.0.0.1:8787/user/get` sẽ trả về "hello world!"

Bạn cũng có thể sử dụng tham số thứ 4 của hàm `trans()` để chuyển đổi ngôn ngữ tạm thời, ví dụ các ví dụ trên và dưới đây tương đương:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Tham số thứ 4 chuyển đổi ngôn ngữ
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Thiết lập ngôn ngữ cụ thể cho mỗi yêu cầu
Translation là một đối tượng đơn lẻ, điều này có nghĩa là tất cả các yêu cầu chia sẻ cùng một phiên bản, nếu một yêu cầu nào đó sử dụng `locale()` để thiết lập ngôn ngữ mặc định, nó sẽ ảnh hưởng đến tất cả các yêu cầu tiếp theo của quá trình đó. Vì vậy, chúng ta nên thiết lập ngôn ngữ cụ thể cho mỗi yêu cầu. Ví dụ sử dụng middleware sau:

Tạo tệp `app/middleware/Lang.php` (nếu không có, vui lòng tạo mới) như sau:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

Thêm middleware toàn cầu trong tệp `config/middleware.php` như sau:
```php
return [
    // Middleware toàn cầu
    '' => [
        // ... Các middleware khác ở đây
        app\middleware\Lang::class,
    ]
];
```

## Sử dụng các placeholder
Đôi khi, một thông báo chứa biến cần được dịch, ví dụ
```php
trans('hello ' . $name);
```
Trong trường hợp này, chúng ta sử dụng placeholder để xử lý.

Sửa đổi `resource/translations/zh_CN/messages.php` như sau:
```php
return [
    'hello' => 'Xin chào %name%!',
```
Khi dịch, chúng ta truyền dữ liệu tương ứng với placeholder qua tham số thứ hai
```php
trans('hello', ['%name%' => 'webman']); // Xin chào webman!
```

## Xử lý số nhiều
Một số ngôn ngữ hiển thị cấu trúc câu khác nhau dựa trên số lượng, ví dụ `There is %count% apple` sẽ sai nếu `%count%` là 1, nhưng đúng nếu lớn hơn 1.

Trong trường hợp này, chúng ta sử dụng **dấu phẩy nối** (`|`) để liệt kê các hình thức số nhiều.

Thêm mục `apple_count` vào tệp ngôn ngữ `resource/translations/en/messages.php` như sau
```php
return [
    // ...
    'apple_count' => 'Có một quả táo|Mỗi quả táo có %count% quả',
];
```

```php
trans('apple_count', ['%count%' => 10]); // Mỗi quả táo có 10 quả
```

Chúng ta có thể chỉ định cả khoảng số, tạo ra các quy tắc số nhiều phức tạp hơn:
```php
return [
    // ...
    'apple_count' => '{0} Không có quả táo|Một quả táo|Mỗi quả táo có %count% quả|[20,Inf[ Rất nhiều quả táo',
];
```

```php
trans('apple_count', ['%count%' => 20]); // Rất nhiều quả táo
```

## Chỉ định tệp ngôn ngữ

Tệp ngôn ngữ mặc định có tên là `messages.php`, nhưng thực tế bạn có thể tạo các tệp ngôn ngữ khác.

Tạo tệp ngôn ngữ `resource/translations/zh_CN/admin.php` như sau:
```php
return [
    'hello_admin' => 'Xin chào quản trị viên!',
];
```
Sử dụng tham số thứ ba của `trans()` để chỉ định tệp ngôn ngữ (bỏ qua phần đuôi `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // Xin chào quản trị viên!
```

## Thêm thông tin chi tiết
Xem thêm tại [hướng dẫn về symfony/translation](https://symfony.com/doc/current/translation.html)
