# Yêu cầu về môi trường

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Tạo dự án

```php
composer create-project workerman/webman
```

### 2. Chạy

Đi vào thư mục webman   

#### Người dùng Windows
Double-click `windows.bat` hoặc chạy `php windows.php` để khởi động

> **Lưu ý**
> Nếu có lỗi, có thể hàm nào đó bị cấm, xem [Kiểm tra hàm bị cấm](others/disable-function-check.md) để bỏ cấm

#### Người dùng Linux
Chạy ở chế độ `debug` (dành cho việc phát triển và gỡ lỗi)

```php
php start.php start
```

Chạy ở chế độ `daemon` (dành cho môi trường chạy thực tế)

```php
php start.php start -d
```

> **Lưu ý**
> Nếu có lỗi, có thể hàm nào đó bị cấm, xem [Kiểm tra hàm bị cấm](others/disable-function-check.md) để bỏ cấm

### 3. Truy cập

Truy cập trình duyệt vào `http://địa_chỉ_ip:8787`
