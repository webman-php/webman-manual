# Gói nhị phân

webman hỗ trợ việc gói dự án thành một tệp nhị phân, điều này cho phép webman chạy trên hệ thống linux mà không cần môi trường PHP.

> **Lưu ý**
> Tệp được gói chỉ hỗ trợ chạy trên hệ thống linux kiến trúc x86_64, không hỗ trợ hệ điều hành mac.
> Cần tắt tùy chọn cấu hình phar trong `php.ini`, cụ thể là cài đặt `phar.readonly = 0`

## Cài đặt công cụ dòng lệnh
`composer require webman/console ^1.2.24`

## Cấu hình
Mở tệp `config/plugin/webman/console/app.php`, thiết lập:
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
để loại bỏ một số thư mục và tệp không cần thiết khi gói, tránh tạo ra kích thước gói lớn.

## Gói
Chạy lệnh
```
php webman build:bin
```
Cũng có thể chỉ định phiên bản PHP mà bạn muốn gói, ví dụ
```
php webman build:bin 8.1
```

Sau khi gói, một tệp `webman.bin` sẽ được tạo trong thư mục build.

## Khởi động
Tải tệp webman.bin lên máy chủ linux, chạy `./webman.bin start` hoặc `./webman.bin start -d` để khởi động.

## Nguyên lý
* Đầu tiên, dự án webman cục bộ sẽ được gói thành một tệp phar
* Sau đó, tải tệp php8.x.micro.sfx từ xa về cục bộ
* Ghép tệp php8.x.micro.sfx và tệp phar thành một tệp nhị phân

## Lưu ý
* Phiên bản php cục bộ >= 7.2 có thể thực thi lệnh gói
* Nhưng chỉ có thể gói thành tệp nhị phân của php8
* Rất khuyến nghị sử dụng phiên bản php cục bộ và phiên bản gói cùng một phiên, tức là nếu cục bộ là php8.0, hãy dùng php8.0 để gói, tránh tình trạng không tương thích
* Lệnh gói sẽ tải xuống mã nguồn của php8 nhưng không cài đặt nó cục bộ, không ảnh hưởng tới môi trường php cục bộ
* webman.bin hiện chỉ hỗ trợ chạy trên hệ thống linux kiến trúc x86_64, không hỗ trợ trên hệ điều hành mac
* Mặc định không gói tệp env (`config/plugin/webman/console/app.php` điều khiển exclude_files), vì vậy khi khởi động tệp env phải được đặt trong cùng thư mục với webman.bin
* Trong quá trình chạy, thư mục runtime sẽ được tạo ra trong thư mục chứa webman.bin, dùng để lưu trữ tệp nhật ký
* Hiện tại, webman.bin không đọc tệp php.ini bên ngoài, nếu cần tùy chỉnh php.ini, vui lòng thiết lập trong tệp `/config/plugin/webman/console/app.php` custom_ini

## Tải xuống PHP tĩnh riêng lẻ
Đôi khi bạn chỉ cần một tệp thực thi PHP mà không muốn triển khai môi trường PHP, hãy tải tệp thực thi PHP tại [đây](https://www.workerman.net/download)

> **Gợi ý**
> Nếu bạn cần chỉ định tệp php.ini cho PHP tĩnh, hãy sử dụng lệnh sau `php -c /your/path/php.ini start.php start -d`

## Các phần mở rộng được hỗ trợ
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## Nguồn gốc dự án

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
