# Tập tin cấu hình

## Vị trí
Tập tin cấu hình của webman nằm trong thư mục `config/`, trong dự án có thể sử dụng hàm `config()` để lấy cấu hình tương ứng.

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

Nếu cấu hình là một mảng, có thể sử dụng dấu `.` để lấy giá trị phần tử bên trong mảng, ví dụ
```php
config('file.key1.key2');
```

## Giá trị mặc định
```php
config($key, $default);
```
Hàm config sử dụng tham số thứ hai để truyền giá trị mặc định, nếu cấu hình không tồn tại sẽ trả về giá trị mặc định.
Nếu cấu hình không tồn tại và không có giá trị mặc định được thiết lập thì sẽ trả về null.

## Cấu hình tùy chỉnh
Nhà phát triển có thể thêm tập tin cấu hình của riêng mình trong thư mục `config/`, ví dụ

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Sử dụng cấu hình khi lấy**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Thay đổi cấu hình
webman không hỗ trợ thay đổi cấu hình động, tất cả cấu hình phải được thay đổi thủ công trong tập tin cấu hình tương ứng và sau đó reload hoặc restart.

> **Chú ý**
> Cấu hình máy chủ `config/server.php` và cấu hình tiến trình `config/process.php` không hỗ trợ reload, cần phải restart để có hiệu lực.
