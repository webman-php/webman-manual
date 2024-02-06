# vlucas/phpdotenv

## Giới thiệu
`vlucas/phpdotenv` là một thành phần tải biến môi trường, được sử dụng để phân biệt cấu hình cho các môi trường khác nhau (ví dụ: môi trường phát triển, môi trường thử nghiệm, v.v.).

## Địa chỉ dự án

https://github.com/vlucas/phpdotenv
  
## Cài đặt
 
```php
composer require vlucas/phpdotenv
 ```
  
## Sử dụng

#### Tạo file `.env` trong thư mục gốc dự án
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Sửa file cấu hình
**config/database.php**
```php
return [
    // Database mặc định
    'default' => 'mysql',

    // Các cấu hình cho từng loại cơ sở dữ liệu
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
> Đề nghị thêm file `.env` vào danh sách `.gitignore` để tránh commit lên repository. Thêm file cấu hình mẫu `.env.example` vào repository, khi triển khai dự án, sao chép file `.env.example` thành file `.env` và sửa cấu hình trong file `.env` tùy theo môi trường hiện tại, từ đó cho phép dự án tải cấu hình khác nhau trong các môi trường khác nhau.

> **Chú ý**
> `vlucas/phpdotenv` có thể có lỗi trên phiên bản PHP TS (Phiên bản An toàn cho Luồng), vui lòng sử dụng phiên bản NTS (Phiên bản Không an toàn cho Luồng).
> Để kiểm tra phiên bản PHP hiện tại, bạn có thể chạy `php -v` để kiểm tra.

## Thêm thông tin

Truy cập vào https://github.com/vlucas/phpdotenv
