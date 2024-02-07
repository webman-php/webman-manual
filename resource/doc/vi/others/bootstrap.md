# Khởi tạo Kinh doanh

Đôi khi chúng ta cần thực hiện một số bước khởi tạo kinh doanh sau khi quá trình khởi động, hoạt động này chỉ thực hiện một lần trong vòng đời của quá trình, ví dụ sau khi quá trình khởi động thiết lập một bộ hẹn giờ, hoặc khởi tạo kết nối cơ sở dữ liệu. Dưới đây chúng ta sẽ giải thích về điều này.

## Nguyên lý
Theo **[quy trình thực hiện](process.md)**, sau khi quá trình được khởi động, webman sẽ tải lớp được cấu hình trong `config/bootstrap.php` (bao gồm `config/plugin/*/*/bootstrap.php`) và thực hiện phương thức bắt đầu của lớp. Chúng ta có thể thêm mã kinh doanh vào phương thức bắt đầu để hoàn tất việc khởi tạo kinh doanh sau khi quá trình khởi động.

## Quy trình
Giả sử chúng ta muốn tạo một bộ hẹn giờ để báo cáo sử dụng bộ nhớ của quá trình hiện tại định kỳ, lớp này có tên là `MemReport`.

#### Thực hiện lệnh

Chạy lệnh `php webman make:bootstrap MemReport` để tạo tệp khởi tạo `app/bootstrap/MemReport.php`.

> **Gợi ý**
> Nếu webman của bạn chưa cài đặt `webman/console`, hãy chạy lệnh `composer require webman/console` để cài đặt.

#### Sửa tệp khởi tạo
Chỉnh sửa `app/bootstrap/MemReport.php`, nội dung tương tự như sau:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Có phải môi trường dòng lệnh không?
        $is_console = !$worker;
        if ($is_console) {
            // Nếu bạn không muốn thực hiện khởi tạo trong môi trường dòng lệnh, hãy trả về trực tiếp tại đây
            return;
        }
        
        // Thực hiện mỗi 10 giây một lần
        \Workerman\Timer::add(10, function () {
            // Để thuận tiện cho việc giới thiệu, chúng tôi sử dụng đầu ra thay vì quá trình báo cáo
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Gợi ý**
> Trong quá trình sử dụng dòng lệnh, framework cũng sẽ thực hiện phương thức khởi đầu được cấu hình trong `config/bootstrap.php`. Chúng ta có thể sử dụng biến `$worker` để xác định xem có phải môi trường dòng lệnh không, từ đó quyết định xem liệu mã khởi tạo kinh doanh có nên được thực hiện hay không.

#### Cấu hình cùng với quá trình khởi động
Mở tệp `config/bootstrap.php` và thêm lớp `MemReport` vào danh sách khởi động.
```php
return [
    // ...Phần cấu hình khác ở đây...
    
    app\bootstrap\MemReport::class,
];
```

Như vậy chúng ta đã hoàn tất một quy trình khởi tạo kinh doanh.

## Thông tin bổ sung
Sau khi **[tùy chỉnh quá trình thực hiện](../process.md)** được khởi động, cũng sẽ thực hiện phương thức khởi đầu được cấu hình trong `config/bootstrap.php`. Chúng ta có thể sử dụng `$worker->name` để xác định quá trình hiện tại là quá trình gì, rồi quyết định xem có nên thực hiện mã khởi tạo kinh doanh trong quá trình đó hay không, ví dụ nếu chúng ta không cần theo dõi quá trình monitor, thì nội dung của `MemReport.php` sẽ tương tự như sau:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Có phải môi trường dòng lệnh không?
        $is_console = !$worker;
        if ($is_console) {
            // Nếu bạn không muốn thực hiện khởi tạo trong môi trường dòng lệnh, hãy trả về trực tiếp tại đây
            return;
        }
        
        // Quá trình monitor không thực hiện bộ hẹn giờ
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Thực hiện mỗi 10 giây một lần
        \Workerman\Timer::add(10, function () {
            // Để thuận tiện cho việc giới thiệu, chúng tôi sử dụng đầu ra thay vì quá trình báo cáo
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
