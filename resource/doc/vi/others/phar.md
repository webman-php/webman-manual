# Gói phar

Phar là một tập tin đóng gói tương tự như JAR trong PHP, bạn có thể sử dụng phar để đóng gói dự án webman của mình thành một tập tin phar duy nhất để dễ dàng triển khai.

**Rất cảm ơn [fuzqing](https://github.com/fuzqing) đã đóng góp.**

> **Lưu ý**
> Cần tắt tùy chọn cấu hình phar trong `php.ini`, cụ thể là đặt `phar.readonly = 0`

## Cài đặt công cụ dòng lệnh
`composer require webman/console`

## Thiết lập cấu hình
Mở tập tin `config/plugin/webman/console/app.php`, thiết lập `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, để loại bỏ một số thư mục và tập tin không cần thiết khi đóng gói, tránh kích thước gói quá lớn.

## Đóng gói
Trong thư mục gốc dự án webman, thực hiện lệnh `php webman phar:pack`
Một tập tin `webman.phar` sẽ được tạo ra trong thư mục build.

> Cấu hình đóng gói có trong tập tin `config/plugin/webman/console/app.php`

## Các lệnh bắt đầu và kết thúc
**Bắt đầu**
`php webman.phar start` hoặc `php webman.phar start -d`

**Kết thúc**
`php webman.phar stop`

**Xem trạng thái**
`php webman.phar status`

**Xem trạng thái kết nối**
`php webman.phar connections`

**Khởi động lại**
`php webman.phar restart` hoặc `php webman.phar restart -d`

## Giải thích
* Sau khi chạy webman.phar, một thư mục runtime sẽ được tạo ra ở cùng nơi với webman.phar, để lưu trữ các tập tin nhật ký và tạm thời khác.

* Nếu dự án của bạn sử dụng tập tin .env, bạn cần đặt tập tin .env trong cùng thư mục với webman.phar.

* Nếu doanh nghiệp của bạn cần tải tập tin lên thư mục public, bạn cũng cần tách riêng thư mục public và đặt trong cùng thư mục với webman.phar, lúc này bạn cần thiết lập trong `config/app.php`.
```'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',```
Doanh nghiệp có thể sử dụng hàm trợ giúp `public_path()` để tìm vị trí thực tế của thư mục public.

* webman.phar không hỗ trợ việc chạy các quy trình tùy chỉnh trên Windows.
