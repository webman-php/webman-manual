# Ứng dụng đa nhiệm

Đôi khi một dự án có thể chia thành nhiều dự án con, ví dụ, một cửa hàng có thể chia thành ba dự án con bao gồm: dự án chính của cửa hàng, giao diện lập trình ứng dụng (API) của cửa hàng và trang quản trị của cửa hàng. Tất cả họ đều sử dụng cấu hình cơ sở dữ liệu giống nhau.

webman cho phép bạn lên kế hoạch thư mục ứng dụng như sau:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Khi truy cập vào địa chỉ `http://127.0.0.1:8787/shop/{controller}/{method}` thì truy cập vào các điều khiển và phương thức trong `app/shop/controller`.

Khi truy cập vào địa chỉ `http://127.0.0.1:8787/api/{controller}/{method}` thì truy cập vào các điều khiển và phương thức trong `app/api/controller`.

Khi truy cập vào địa chỉ `http://127.0.0.1:8787/admin/{controller}/{method}` thì truy cập vào các điều khiển và phương thức trong `app/admin/controller`.

Trong webman, thậm chí bạn có thể lên kế hoạch thư mục ứng dụng như sau:
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Khi truy cập vào địa chỉ `http://127.0.0.1:8787/{controller}/{method}` thì truy cập vào các điều khiển và phương thức trong `app/controller`. Khi đường dẫn bắt đầu với api hoặc admin thì truy cập vào các điều khiển và phương thức trong thư mục tương ứng.

Trong trường hợp nhiều ứng dụng, không gian tên lớp của các lớp cần phù hợp với `psr4`, ví dụ như tệp `app/api/controller/FooController.php` sẽ có nội dung tương tự như sau:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Cấu hình tiền xử lý đa ứng dụng
Đôi khi bạn muốn cấu hình các tiền xử lý khác nhau cho các ứng dụng khác nhau, ví dụ như ứng dụng `api` có thể cần một tiền xử lý chuyển tiếp (middleware) còn `admin` cần một tiền xử lý kiểm tra đăng nhập quản trị viên. Trong trường hợp này, tệp cấu hình `config/midlleware.php` có thể giống như sau:
```php
return [
    // Tiền xử lý toàn cầu
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Tiền xử lý cho ứng dụng api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Tiền xử lý cho ứng dụng admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Các tiền xử lý ở trên có thể không tồn tại, đây chỉ là ví dụ để mô tả cách cấu hình tiền xử lý theo từng ứng dụng

Thứ tự thực hiện của các tiền xử lý là `Tiền xử lý toàn cầu` -> `Tiền xử lý ứng dụng`.

Tài liệu phát triển tiền xử lý xem tại [Chương tiền xử lý](middleware.md)

## Cấu hình xử lý ngoại lệ đa ứng dụng
Tương tự, bạn muốn cấu hình các lớp xử lý ngoại lệ khác nhau cho từng ứng dụng, ví dụ như khi có ngoại lệ xảy ra trong ứng dụng `shop`, bạn có thể muốn cung cấp một trang thông báo thân thiện; trong ứng dụng `api`, bạn có thể muốn trả về một chuỗi json thay vì một trang. Tệp cấu hình xử lý ngoại lệ cho từng ứng dụng `config/exception.php` có thể giống như sau:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Khác với tiền xử lý, mỗi ứng dụng chỉ có thể cấu hình một lớp xử lý ngoại lệ.

> Các lớp xử lý ngoại lệ ở trên có thể không tồn tại, đây chỉ là ví dụ để mô tả cách cấu hình xử lý ngoại lệ theo từng ứng dụng
Tài liệu phát triển xử lý ngoại lệ xem tại [Chương xử lý ngoại lệ](exception.md)
