# Đóng gói nhị phân

webman hỗ trợ việc đóng gói dự án thành một tệp nhị phân, cho phép webman chạy trên hệ thống linux mà không cần môi trường php.

> **Chú ý**
> Tệp đã được đóng gói hiện chỉ hỗ trợ chạy trên hệ thống linux kiến trúc x86_64, không hỗ trợ hệ thống mac
> Cần tắt tinh chỉnh phar trong `php.ini`, cụ thể là đặt `phar.readonly = 0`

## Cài đặt công cụ dòng lệnh
`composer require webman/console ^1.2.24`

## Cấu hình
Mở tệp `config/plugin/webman/console/app.php`, thiết lập 
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
để loại bỏ một số thư mục và tệp không cần thiết khi đóng gói, tránh tình trạng tệp đóng gói quá lớn

## Đóng gói
Chạy lệnh
```shell
php webman build:bin
```
Cũng có thể chỉ định phiên bản php để đóng gói, ví dụ
```shell
php webman build:bin 8.1
```

Sau khi đóng gói sẽ tạo ra tệp `webman.bin` trong thư mục build

## Khởi động
Tải webman.bin lên máy chủ linux, chạy `./webman.bin start` hoặc `./webman.bin start -d` để khởi động.

## Nguyên lý
* Đầu tiên, dự án webman cục bộ sẽ được đóng gói thành một tệp phar
* Tiếp theo, tải php8.x.micro.sfx từ xa về máy cục bộ
* Ghép php8.x.micro.sfx và tệp phar lại với nhau thành một tệp nhị phân

## Lưu ý
* Phiên bản php cục bộ >=7.2 đều có thể thực hiện lệnh đóng gói
* Tuy nhiên, chỉ có thể đóng gói thành tệp nhị phân của php8
* Đề xuất sử dụng cùng phiên bản php cho lệnh đóng gói và môi trường cục bộ, tránh sự không tương thích
* Lệnh đóng gói sẽ tải về mã nguồn của php8, nhưng không cài đặt trên máy cục bộ, không ảnh hưởng đến môi trường php cục bộ
* Hiện tại tệp webman.bin chỉ hỗ trợ chạy trên hệ thống linux kiến trúc x86_64, không hỗ trợ trên hệ thống mac
* Mặc định không đóng gói tệp env (`config/plugin/webman/console/app.php` thiết lập exclude_files), do đó tệp env cần được đặt trong cùng thư mục với webman.bin
* Trong quá trình chạy sẽ tạo ra thư mục runtime trong thư mục chứa webman.bin, dùng để lưu trữ tệp nhật ký
* Hiện tại webman.bin không đọc tệp php.ini ngoài, nếu cần tùy chỉnh php.ini, vui lòng thiết lập trong tệp `/config/plugin/webman/console/app.php` custom_ini

## Tải về Tĩnh PHP riêng lẻ
Đôi khi bạn chỉ cần tải xuống một tệp thực thi PHP mà không cần triển khai môi trường PHP, vui lòng nhấp vào đây để tải về [Tải về PHP tĩnh](https://www.workerman.net/download)

> **Gợi ý**
> Nếu bạn cần chỉ định tệp php.ini cho PHP tĩnh, xin vui lòng sử dụng lệnh sau `php -c /your/path/php.ini start.php start -d`

## Các tiện ích hỗ trợ
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

## Nguồn dự án
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
