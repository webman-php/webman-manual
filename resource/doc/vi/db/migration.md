# Công cụ di chuyển cơ sở dữ liệu Phinx

## Giới thiệu

Phinx cho phép các nhà phát triển chỉnh sửa và bảo trì cơ sở dữ liệu một cách dễ dàng. Nó tránh việc viết SQL thủ công bằng cách sử dụng API PHP mạnh mẽ để quản lý di chuyển cơ sở dữ liệu. Nhà phát triển có thể sử dụng quản lý phiên bản để quản lý di chuyển cơ sở dữ liệu của họ. Phinx có thể dễ dàng di chuyển dữ liệu giữa các cơ sở dữ liệu khác nhau. Nó cũng có thể theo dõi xem những tập lệnh di chuyển nào đã được thực thi, giúp nhà phát triển không còn lo lắng về trạng thái của cơ sở dữ liệu mà tập trung hơn vào việc viết ra hệ thống tốt hơn.

## Địa chỉ dự án

https://github.com/cakephp/phinx

## Cách cài đặt

```php
composer require robmorgan/phinx
```

## Địa chỉ tài liệu chính thức tiếng Trung

Bạn có thể xem tài liệu chi tiết tại đây, ở đây chỉ hướng dẫn cách cấu hình và sử dụng trong webman

https://tsy12321.gitbooks.io/phinx-doc/content/

## Cấu trúc thư mục di chuyển file

``` 
.
├── app                           Thư mục ứng dụng
│   ├── controller                Thư mục điều khiển
│   │   └── Index.php             Điều khiển
│   ├── model                     Thư mục mô hình
......
├── database                      Tệp cơ sở dữ liệu
│   ├── migrations                Di chuyển tệp
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Dữ liệu kiểm tra
│   │   └── UserSeeder.php
......
```

## Cấu hình phinx.php

Tạo tệp phinx.php trong thư mục gốc của dự án

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## Gợi ý sử dụng

Một khi tệp di chuyển đã được hợp nhất, không được phép chỉnh sửa lại, nếu gặp sự cố, phải tạo tệp di chuyển mới hoặc xóa để xử lý.

#### Quy tắc đặt tên tệp hoạt động tạo bảng dữ liệu

`{time(tự động tạo)}_create_{tên bảng viết thường tiếng Anh}`

#### Quy tắc đặt tên tệp hoạt động sửa bảng dữ liệu

`{time(tự động tạo)}_modify_{tên bảng viết thường tiếng Anh+cụ thể sửa đổi tiếng Anh viết thường}`

#### Quy tắc đặt tên tệp hoạt động xóa bảng dữ liệu

`{time(tự động tạo)}_delete_{tên bảng viết thường tiếng Anh+cụ thể sửa đổi tiếng Anh viết thường}`

#### Quy tắc đặt tên tệp điền dữ liệu

`{time(tự động tạo)}_fill_{tên bảng viết thường tiếng Anh+cụ thể sửa đổi tiếng Anh viết thường}`
