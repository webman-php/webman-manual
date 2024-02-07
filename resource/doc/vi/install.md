# Yêu cầu môi trường

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Tạo dự án

```php
composer create-project workerman/webman
```

### 2. Chạy

Đi vào thư mục webman   

#### Đối với người dùng windows
Nhấn đôi vào `windows.bat` hoặc chạy `php windows.php` để khởi động

> **Lưu ý**
> Nếu có lỗi xuất hiện, có thể là một số hàm đã bị vô hiệu hóa, tham khảo [Kiểm tra hàm bị vô hiệu hóa](others/disable-function-check.md) để gỡ cấm

#### Đối với người dùng linux
Chạy trong chế độ `debug` (dùng cho debugging phát triển)

```php
php start.php start
```

Chạy trong chế độ `daemon` (dùng cho môi trường chạy thực tế)

```php
php start.php start -d
```

> **Lưu ý**
> Nếu có lỗi xuất hiện, có thể là một số hàm đã bị vô hiệu hóa, tham khảo [Kiểm tra hàm bị vô hiệu hóa](others/disable-function-check.md) để gỡ cấm

### 3. Truy cập

Truy cập trình duyệt vào `http://địa chỉ_ip:8787`
