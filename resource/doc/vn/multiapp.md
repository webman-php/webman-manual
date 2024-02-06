# Ứng dụng đa nhiệm

Đôi khi một dự án có thể được chia thành nhiều dự án con, ví dụ một cửa hàng có thể được chia thành các dự án con là cửa hàng chính, giao diện API cửa hàng và trang quản lý cửa hàng, tất cả đều sử dụng cấu hình cơ sở dữ liệu giống nhau.

webman cho phép bạn lên kế hoạch thư mục app như sau:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Khi truy cập vào địa chỉ `http://127.0.0.1:8787/shop/{controller}/{method}` sẽ truy cập vào controller và method trong thư mục `app/shop/controller`.

Khi truy cập vào địa chỉ `http://127.0.0.1:8787/api/{controller}/{method}` sẽ truy cập vào controller và method trong thư mục `app/api/controller`.

Khi truy cập vào địa chỉ `http://127.0.0.1:8787/admin/{controller}/{method}` sẽ truy cập vào controller và method trong thư mục `app/admin/controller`.

Trong webman, bạn thậm chí có thể lên kế hoạch thư mục app như sau:
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Như vậy khi truy cập địa chỉ `http://127.0.0.1:8787/{controller}/{method}` sẽ truy cập vào controller và method trong thư mục `app/controller`. Khi đường dẫn bắt đầu bằng api hoặc admin, sẽ truy cập vào controller và method trong thư mục tương ứng.

Khi có nhiều ứng dụng, không gian tên của lớp cần tuân theo `psr4`, ví dụ file `app/api/controller/FooController.php` có nội dung tương tự như sau:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Cấu hình middleware cho nhiều ứng dụng

Đôi khi bạn muốn cấu hình các middleware khác nhau cho từng ứng dụng, ví dụ ứng dụng `api` có thể cần một middleware cho việc cross-origin, trong khi ứng dụng `admin` cần một middleware để kiểm tra đăng nhập của quản trị viên, việc cấu hình `config/midlleware.php` có thể giống như sau:
```php
return [
    // Middleware toàn cầu
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware ứng dụng api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware ứng dụng admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```

> Các middleware ở trên có thể không tồn tại, đây chỉ là ví dụ về cách cấu hình middleware cho từng ứng dụng

Thứ tự thực hiện middleware là `Middleware toàn cầu`->`middleware ứng dụng`.

Xem thêm về phát triển middleware tại [chương về middleware](middleware.md)

## Cấu hình xử lý ngoại lệ cho nhiều ứng dụng
Tương tự, bạn muốn cấu hình các lớp xử lý ngoại lệ khác nhau cho từng ứng dụng, ví dụ khi xảy ra ngoại lệ trong ứng dụng `shop`, bạn có thể muốn hiển thị một trang cảnh báo thân thiện; trong ứng dụng `api`, bạn có thể muốn trả về không phải là một trang web mà là một chuỗi json. Cấu hình tệp xử lý ngoại lệ khác nhau cho từng ứng dụng `config/exception.php` có thể giống như sau:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Khác với middleware, mỗi ứng dụng chỉ có thể cấu hình một lớp xử lý ngoại lệ.

> Các lớp xử lý ngoại lệ trên có thể không tồn tại, đây chỉ là ví dụ về cách cấu hình xử lý ngoại lệ cho từng ứng dụng

Xem thêm về phát triển xử lý ngoại lệ tại [chương về xử lý ngoại lệ](exception.md)
