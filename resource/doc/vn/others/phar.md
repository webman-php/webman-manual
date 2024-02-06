# Gói phar

phar là một loại tập tin gói tương tự như JAR trong PHP, bạn có thể sử dụng phar để đóng gói dự án webman của bạn thành một tập tin phar duy nhất, dễ dàng triển khai.

**Rất cảm ơn [fuzqing](https://github.com/fuzqing) đã đóng góp.**

> **Lưu ý**
> Cần tắt các tùy chọn cấu hình phar trong `php.ini`, cụ thể là đặt `phar.readonly = 0`

## Cài đặt công cụ dòng lệnh
`composer require webman/console`

## Cấu hình
Mở tập tin `config/plugin/webman/console/app.php`, đặt `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, để loại bỏ một số thư mục và tập tin không cần thiết khi đóng gói, tránh kích thước gói quá lớn.

## Đóng gói
Trong thư mục gốc của dự án webman, thực hiện lệnh `php webman phar:pack`
Sẽ tạo ra một tập tin `webman.phar` trong thư mục build.

> Cấu hình đóng gói liên quan đến tập tin  `config/plugin/webman/console/app.php` 

## Lệnh khởi động và dừng
**Khởi động**
`php webman.phar start` hoặc `php webman.phar start -d`

**Dừng**
`php webman.phar stop`

**Xem trạng thái**
`php webman.phar status`

**Xem trạng thái kết nối**
`php webman.phar connections`

**Khởi động lại**
`php webman.phar restart` hoặc `php webman.phar restart -d`

## Giải thích
* Sau khi chạy webman.phar, thư mục runtime sẽ được tạo trong thư mục chứa webman.phar, để lưu trữ các tập tin tạm thời như nhật ký.

* Nếu dự án của bạn sử dụng tập tin .env, bạn cần đặt tập tin .env trong thư mục chứa webman.phar.

* Nếu doanh nghiệp của bạn cần tải tập tin lên thư mục public, bạn cũng cần tách thư mục public ra và đặt trong thư mục chứa webman.phar, lúc này bạn cần cấu hình `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Doanh nghiệp có thể sử dụng hàm trợ giúp `public_path()` để tìm vị trí thực tế của thư mục public.

* webman.phar không hỗ trợ mở tiến trình tùy chỉnh trên Windows.
