# Đóng gói nhị phân

webman hỗ trợ đóng gói dự án thành một tệp nhị phân duy nhất, giúp webman chạy trên Linux mà không cần môi trường PHP.

> **Lưu ý**
> Tệp đã đóng gói hiện chỉ hỗ trợ chạy trên Linux kiến trúc x86_64. Không hỗ trợ Windows và macOS.
> Cần tắt tùy chọn phar trong `php.ini`, tức là đặt `phar.readonly = 0`.

## Cài đặt công cụ dòng lệnh
`composer require webman/console`

## Đóng gói
Chạy lệnh
```
php webman build:bin
```
Có thể chỉ định phiên bản PHP để đóng gói, ví dụ
```
php webman build:bin 8.3
```

Sau khi đóng gói sẽ tạo ra tệp `webman.bin` trong thư mục build.

## Khởi động
Tải webman.bin lên máy chủ Linux, chạy `./webman.bin start` hoặc `./webman.bin start -d` để khởi động.

## Nguyên lý
* Đầu tiên, dự án webman cục bộ được đóng gói thành tệp phar
* Tiếp theo, tải php8.x.micro.sfx từ xa về máy cục bộ
* Ghép php8.x.micro.sfx và tệp phar thành một tệp nhị phân duy nhất

## Lưu ý
* Nên dùng cùng phiên bản PHP cho môi trường cục bộ và khi đóng gói (ví dụ đều dùng PHP 8.3) để tránh xung đột tương thích
* Khi đóng gói sẽ tải mã nguồn PHP 8 nhưng không cài đặt cục bộ, không ảnh hưởng môi trường PHP cục bộ
* webman.bin hiện chỉ chạy trên Linux x86_64 và không hỗ trợ macOS
* Dự án đã đóng gói không hỗ trợ reload; mỗi lần cập nhật mã cần restart
* Mặc định không đóng gói tệp env (do exclude_files trong `config/plugin/webman/console/app.php` điều khiển). Khi khởi động, tệp env phải đặt cùng thư mục với webman.bin
* Trong lúc chạy sẽ tạo thư mục runtime trong thư mục chứa webman.bin để lưu nhật ký
* Hiện webman.bin không đọc tệp php.ini bên ngoài. Để tùy chỉnh php.ini, thiết lập trong custom_ini của `config/plugin/webman/console/app.php`
* Một số tệp không cần đóng gói; có thể cấu hình loại trừ trong `config/plugin/webman/console/app.php` để tránh gói quá lớn
* Đóng gói nhị phân không hỗ trợ coroutine Swoole
* Tuyệt đối không lưu tệp người dùng tải lên trong gói nhị phân. Thao tác tệp tải lên qua giao thức `phar://` rất nguy hiểm (lỗ hổng deserialization phar). Tệp tải lên phải lưu riêng trên đĩa ngoài gói
* Nếu ứng dụng cần tải tệp lên thư mục public, hãy tách thư mục public ra đặt cùng vị trí với webman.bin, cấu hình `config/app.php` như sau rồi đóng gói lại:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Tải PHP tĩnh riêng lẻ
Đôi khi bạn chỉ cần tệp thực thi PHP mà không cần triển khai môi trường PHP. [Tải PHP tĩnh tại đây](https://www.workerman.net/download).

> **Gợi ý**
> Để chỉ định tệp php.ini cho PHP tĩnh: `php -c /your/path/php.ini start.php start -d`

## Tiện ích mở rộng hỗ trợ
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Nguồn dự án

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
