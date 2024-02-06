# Khởi tạo Kinh doanh

Đôi khi chúng ta cần thực hiện một số công việc khởi tạo kinh doanh sau khi quá trình khởi động, việc khởi tạo này chỉ thực hiện một lần trong vòng đời của quá trình, ví dụ như sau khi quá trình khởi động đã thiết lập một bộ định thời hoặc khởi tạo kết nối cơ sở dữ liệu. Dưới đây chúng ta sẽ hướng dẫn về việc này.

## Nguyên lý
Dựa trên **[Luồng thực hiện](process.md)** được mô tả, webman sẽ tải các lớp được thiết lập trong `config/bootstrap.php` (bao gồm `config/plugin/*/*/bootstrap.php`) sau khi quá trình khởi động và thực hiện phương thức start của lớp. Chúng ta có thể thêm mã kinh doanh vào phương thức start để hoàn thành quá trình khởi động kinh doanh sau khi quá trình khởi động.

## Quá trình
Giả sử chúng ta muốn tạo một bộ định thời để báo cáo việc sử dụng bộ nhớ hiện tại của quá trình, lớp này được đặt tên là `MemReport`.

#### Thực hiện lệnh

Thực hiện lệnh `php webman make:bootstrap MemReport` để tạo tệp khởi tạo `app/bootstrap/MemReport.php`

> **Lưu ý**
> Nếu webman của bạn chưa cài đặt `webman/console`, hãy thực hiện lệnh  `composer require webman/console` để cài đặt nó.

#### Chỉnh sửa tệp khởi tạo
Chỉnh sửa tệp `app/bootstrap/MemReport.php`, nội dung tương tự như sau:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Đây có phải môi trường dòng lệnh không?
        $is_console = !$worker;
        if ($is_console) {
            // Nếu bạn không muốn thực hiện khởi tạo trong môi trường dòng lệnh, hãy trả về ngay tại đây
            return;
        }
        
        // Thực hiện mỗi 10 giây một lần
        \Workerman\Timer::add(10, function () {
            // Để thuận tiện cho việc trình bày, ở đây sử dụng đầu ra thay vì quá trình báo cáo
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Lưu ý**
> Khi sử dụng môi trường dòng lệnh, framework cũng sẽ thực hiện phương thức start được thiết lập trong `config/bootstrap.php`, chúng ta có thể xác định xem môi trường là dòng lệnh hay không thông qua biến `$worker` để quyết định xem mã khởi tạo kinh doanh có thể được thực hiện hay không.

#### Cấu hình khi khởi động quá trình
Mở tệp `config/bootstrap.php` và thêm lớp `MemReport` vào danh sách khởi động.
```php
return [
    // ... Các cấu hình khác ở đây ...

    app\bootstrap\MemReport::class,
];
```

Như vậy, chúng ta đã hoàn tất một quy trình khởi tạo kinh doanh.

## Thêm thông tin
Sau khi khởi động, [quá trình tự định nghĩa](../process.md) cũng sẽ thực hiện phương thức start được thiết lập trong `config/bootstrap.php`. Chúng ta có thể sử dụng biến `$worker->name` để xác định loại quá trình hiện tại và quyết định xem mã khởi tạo kinh doanh của bạn có thể được thực hiện trong quá trình đó hay không, ví dụ: nếu chúng ta không cần giám sát quá trình monitor, thì nội dung của `MemReport.php` sẽ tương tự như sau:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Đây có phải môi trường dòng lệnh không?
        $is_console = !$worker;
        if ($is_console) {
            // Nếu bạn không muốn thực hiện khởi tạo trong môi trường dòng lệnh, hãy trả về ngay tại đây
            return;
        }
        
        // Không thực hiện bộ định thời trong quá trình giám sát
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Thực hiện mỗi 10 giây một lần
        \Workerman\Timer::add(10, function () {
            // Để thuận tiện cho việc trình bày, ở đây sử dụng đầu ra thay vì quá trình báo cáo
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
