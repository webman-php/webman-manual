# Bộ lập lịch crontab

## workerman/crontab

### Giới thiệu

`workerman/crontab` tương tự như crontab của Linux, khác biệt là `workerman/crontab` hỗ trợ lập lịch theo giây.

Mô tả thời gian:

``` 
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ ngày trong tuần (0 - 6) (Chủ nhật=0)
|   |   |   |   +------ tháng (1 - 12)
|   |   |   +-------- ngày trong tháng (1 - 31)
|   |   +---------- giờ (0 - 23)
|   +------------ phút (0 - 59)
+-------------- giây (0-59)[Có thể bỏ qua, nếu không có số 0, đơn vị thời gian nhỏ nhất sẽ là phút]
```

### Địa chỉ dự án

https://github.com/walkor/crontab

### Cài đặt

```php
composer require workerman/crontab
```

### Sử dụng

**Bước 1: Tạo tệp tiến trình mới `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Thực hiện mỗi giây
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi 5 giây
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi phút
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi 5 phút
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi giây đầu tiên của mỗi phút
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Thực hiện vào 7 giờ 50 phút hàng ngày, lưu ý ở đây bỏ qua đơn vị giây
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Bước 2: Cấu hình để tiến trình chạy cùng với webman**

Mở tệp cấu hình `config/process.php`, thêm cấu hình sau:

```php
return [
    ....Cấu hình khác, ở đây lược bỏ....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Bước 3: Khởi động lại webman**

> Chú ý: Công việc được lập lịch không sẽ thực hiện ngay lập tức, tất cả các công việc được lập lịch sẽ bắt đầu đếm thời gian từ phút tiếp theo.

### Giải thích

Crontab không phải là không đồng bộ. Ví dụ, trong một tiến trình task, đặt hai viên đồng hồ A và B, cả hai đều thực hiện mỗi giây, nhưng nhiệm vụ A mất 10 giây, thì nhiệm vụ B cần phải đợi cho đến khi nhiệm vụ A hoàn thành mới được thực hiện, dẫn đến việc trì hoãn thực hiện của nhiệm vụ B.
Nếu doanh nghiệp cảm thấy nhạy cảm với khoảng thời gian, cần đặt các công việc lập lịch nhạy cảm thời gian vào một tiến trình riêng biệt để chạy, ngăn không cho các công việc lập lịch khác ảnh hưởng. Ví dụ `config/process.php` thiết lập như sau:

```php
return [
    ....Cấu hình khác, ở đây lược bỏ....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```

Đặt các công việc lập lịch nhạy cảm thời gian vào tệp `process/Task1.php`, các công việc lập lịch khác vào `process/Task2.php`.

### Thêm thông tin

Để biết thêm thông tin cấu hình `config/process.php`, vui lòng tham khảo [Tiến trình tùy chỉnh](../process.md)
