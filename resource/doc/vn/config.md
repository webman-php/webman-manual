# Tệp cấu hình

## Vị trí
Tệp cấu hình của webman nằm trong thư mục `config/`, có thể sử dụng hàm `config()` trong dự án để lấy cấu hình tương ứng.

## Lấy cấu hình

Lấy tất cả cấu hình
```php
config();
```

Lấy tất cả cấu hình trong `config/app.php`
```php
config('app');
```

Lấy cấu hình `debug` trong `config/app.php`
```php
config('app.debug');
```

Nếu cấu hình là mảng, có thể sử dụng dấu `.` để lấy giá trị bên trong mảng, ví dụ
```php
config('file.key1.key2');
```

## Giá trị mặc định
```php
config($key, $default);
```
Hàm config có thể truyền giá trị mặc định thông qua tham số thứ hai, nếu cấu hình không tồn tại thì sẽ trả về giá trị mặc định.
Nếu cấu hình không tồn tại và không có giá trị mặc định thì sẽ trả về null.

## Tùy chỉnh cấu hình
Nhà phát triển có thể thêm tệp cấu hình của riêng mình trong thư mục `config/`, ví dụ

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Sử dụng khi lấy cấu hình**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Thay đổi cấu hình
webman không hỗ trợ thay đổi cấu hình động, tất cả cấu hình phải được thay đổi thủ công trong tệp cấu hình tương ứng và phải tải lại hoặc khởi động lại (reload hoặc restart).

> **Chú ý**
> Cấu hình máy chủ `config/server.php` và cấu hình tiến trình `config/process.php` không hỗ trợ reload, cần phải khởi động lại (restart) để cấu hình có hiệu lực.
