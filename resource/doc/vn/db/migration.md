# Công cụ di chuyển cơ sở dữ liệu Phinx

## Giới thiệu

Phinx cho phép các nhà phát triển thay đổi và duy trì cơ sở dữ liệu một cách dễ dàng. Nó tránh việc viết SQL thủ công bằng cách sử dụng một API PHP mạnh mẽ để quản lý di chuyển cơ sở dữ liệu. Nhà phát triển có thể sử dụng quản lý phiên bản để quản lý di chuyển cơ sở dữ liệu của họ. Phinx cung cấp sự thuận tiện trong việc di chuyển dữ liệu giữa các cơ sở dữ liệu khác nhau. Nó cũng có thể theo dõi chính xác những tập lệnh di chuyển đã được thực thi, giúp nhà phát triển không còn lo lắng về tình trạng cơ sở dữ liệu mà thay vào đó tập trung hơn vào việc viết hệ thống tốt hơn.

## Địa chỉ dự án

https://github.com/cakephp/phinx

## Cài đặt

  ```php
  composer require robmorgan/phinx
  ```

## Địa chỉ tài liệu chính thức tiếng Trung

Để biết rõ cách sử dụng, bạn có thể xem tài liệu chính thức tiếng Trung, ở đây chỉ nói cách cấu hình và sử dụng trong webman

https://tsy12321.gitbooks.io/phinx-doc/content/

## Cấu trúc thư mục di chuyển

```
.
├── app                           Thư mục ứng dụng
│   ├── controller                Thư mục điều khiển
│   │   └── Index.php             Tệp điều khiển
│   ├── model                     Thư mục mô hình
......
├── database                      Tệp cơ sở dữ liệu
│   ├── migrations                Tệp di chuyển
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Dữ liệu kiểm tra
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
         "seeds" => "database/seeds"
    ],
    "environments" => [
         "default_migration_table" => "phinxlog",
         "default_database" => "dev",
         "default_environment" => "dev",
         "dev" => [
            "adapter" => "DB_CONNECTION",
            "host" => "DB_HOST",
            "name" => "DB_DATABASE",
            "user" => "DB_USERNAME",
            "pass" => "DB_PASSWORD",
            "port" => "DB_PORT",
            "charset" => "utf8"
         ]
    ]
];
```

## Gợi ý sử dụng

Một khi tệp di chuyển đã được ghép mã, không cho phép chỉnh sửa lại, nếu có vấn đề phải tạo hoặc xóa tệp thực hiện thay đổi.

#### Quy tắc đặt tên tệp hoạt động tạo bảng dữ liệu

`{time(tạo tự động)}_create_{tên_bảng_thường_tiếng_anh}`

#### Quy tắc đặt tên tệp hoạt động sửa bảng dữ liệu

`{time(tạo tự động)}_modify_{tên_bảng_thường_tiếng_anh+thuộc_tính_sửa_tiếng_anh}`

#### Quy tắc đặt tên tệp hoạt động xóa bảng dữ liệu

`{time(tạo tự động)}_delete_{tên_bảng_thường_tiếng_anh+thuộc_tính_sửa_tiếng_anh}`

#### Quy tắc đặt tên tệp điền dữ liệu

`{time(tạo tự động)}_fill_{tên_bảng_thường_tiếng_anh+thuộc_tính_sửa_tiếng_anh}`
