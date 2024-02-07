# vlucas/phpdotenv

## Giới thiệu
`vlucas/phpdotenv` là một thành phần tải biến môi trường, được sử dụng để phân biệt cấu hình giữa các môi trường khác nhau (như môi trường phát triển, môi trường thử nghiệm, vv.).

## Địa chỉ dự án
https://github.com/vlucas/phpdotenv

## Cài đặt
```php
composer require vlucas/phpdotenv
```

## Sử dụng

#### Tạo tệp `.env` trong thư mục gốc của dự án
**.env**
```plaintext
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Sửa tệp cấu hình
**config/database.php**
```php
return [
    // Cơ sở dữ liệu mặc định
    'default' => 'mysql',

    // Các cấu hình cơ sở dữ liệu khác nhau
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Lưu ý**  
> Đề xuất thêm tệp `.env` vào danh sách `.gitignore` để tránh commit vào kho lưu trữ mã nguồn. Thêm một tệp cấu hình mẫu `.env.example` vào kho lưu trữ mã nguồn, khi triển khai dự án, sao chép `.env.example` thành `.env`, và sửa cấu hình trong `.env` theo môi trường hiện tại. Điều này giúp dự án có thể tải cấu hình khác nhau trong các môi trường khác nhau.

> **Chú ý**  
> `vlucas/phpdotenv` có thể gặp lỗi trên phiên bản PHP TS (phiên bản có sự an toàn đa luồng). Đề nghị sử dụng phiên bản NTS (phiên bản không an toàn đa luồng).  
> Bạn có thể kiểm tra phiên bản PHP hiện tại bằng cách thực thi `php -v`.

## Thêm thông tin
Truy cập https://github.com/vlucas/phpdotenv
