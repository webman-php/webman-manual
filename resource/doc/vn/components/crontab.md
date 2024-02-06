# Thư viện CronTab

## workerman/crontab

### Giới thiệu

`workerman/crontab` tương tự như crontab của linux, điều khác biệt là `workerman/crontab` hỗ trợ định kỳ theo giây.

Giải thích về thời gian:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ ngày trong tuần (0 - 6) (Chủ nhật=0)
|   |   |   |   +------ tháng (1 - 12)
|   |   |   +-------- ngày trong tháng (1 - 31)
|   |   +---------- giờ (0 - 23)
|   +------------ phút (0 - 59)
+-------------- giây (0-59)[có thể bỏ qua, nếu không có giây, đơn vị thời gian tối thiểu là phút]
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
    
        // Thực hiện mỗi giây một lần
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi 5 giây một lần
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi phút một lần
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện mỗi 5 phút một lần
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Thực hiện vào giây đầu tiên của mỗi phút
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Thực hiện vào 7 giờ 50 phút hàng ngày, lưu ý rằng giây có thể bị bỏ qua
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Bước 2: Cấu hình tệp tiến trình để khởi động cùng với webman**

Mở tệp cấu hình `config/process.php`, thêm cấu hình như sau

```php
return [
    ....các cấu hình khác, bỏ qua ở đây....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
**Bước 3: Khởi động lại webman**

> Lưu ý: Công việc định kỳ không bắt đầu thực thi ngay lập tức, tất cả công việc định kỳ sẽ bắt đầu tính giờ thực thi ở phút tiếp theo.

### Giải thích
Crontab không phải là bất đồng bộ, ví dụ một tiến trình task có thiết lập cho A và B là hai bộ hẹn giờ, cả hai đều thực hiện công việc mỗi giây một lần, nhưng công việc A mất 10 giây, vì vậy công việc B phải chờ cho đến khi A thực hiện xong mới được thực hiện, dẫn đến B bị trì hoãn trong công việc thực hiện.
Nếu doanh nghiệp nhạy về khoảng thời gian, cần di chuyển công việc định kỳ nhạy cảm về thời gian sang một tiến trình riêng để thực hiện, để tránh bị ảnh hưởng bởi các công việc định kỳ khác. Ví dụ cấu hình trong `config/process.php` như sau

```php
return [
    ....các cấu hình khác, bỏ qua ở đây....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Di chuyển công việc định kỳ nhạy về thời gian vào tệp `process/Task1.php`, các công việc định kỳ khác di chuyển vào `process/Task2.php`.

### Thêm nữa
Để biết thêm về cấu hình `config/process.php`, vui lòng tham khảo [Tùy chỉnh tiến trình](../process.md)
