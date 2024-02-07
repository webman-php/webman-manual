# Tự động tải

## Tải tệp theo chuẩn PSR-0 bằng Composer
webman tuân theo chuẩn tải tự động `PSR-4`. Nếu doanh nghiệp của bạn cần tải thư viện mã nguồn theo chuẩn `PSR-0`, hãy tham khảo các thao tác sau đây.

- Tạo thư mục `extend` để lưu trữ mã nguồn theo chuẩn `PSR-0`
- Chỉnh sửa `composer.json`, thêm nội dung sau đây vào phần `autoload`

```js
"psr-0": {
    "": "extend/"
}
```
Kết quả cuối cùng sẽ tương tự như sau
![](../../assets/img/psr0.png)

- Chạy lệnh `composer dumpautoload`
- Chạy lệnh `php start.php restart` để khởi động lại webman (lưu ý, phải khởi động lại mới có hiệu lực) 

## Tải một số tệp bằng Composer

- Chỉnh sửa `composer.json`, thêm các tệp cần tải vào phần `autoload.files`
```js
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Chạy lệnh `composer dumpautoload`
- Chạy lệnh `php start.php restart` để khởi động lại webman (lưu ý, phải khởi động lại mới có hiệu lực) 

> **Ghi chú**
> Các tệp được cấu hình trong `autoload.files` của tập tin `composer.json` sẽ được tải trước khi webman khởi động. Còn tệp được tải thông qua `config/autoload.php` của framework sẽ được tải sau khi webman khởi động.
> Khi thay đổi tệp được tải thông qua `autoload.files` trong tập tin `composer.json`, cần khởi động lại để có hiệu lực, việc reload không có hiệu lực. Còn việc thay đổi và reload tệp được tải thông qua `config/autoload.php` có hiệu lực ngay lập tức.

## Tải tệp bằng framework
Một số tệp có thể không tuân theo chuẩn SPR, không thể tải tự động. Chúng ta có thể tải các tệp này thông qua cấu hình `config/autoload.php`, ví dụ:

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **Ghi chú**
 > Chúng ta có thể thấy rằng `autoload.php` đã cấu hình để tải hai tệp `support/Request.php` và `support/Response.php`. Điều này là do trong thư mục `vendor/workerman/webman-framework/src/support/` cũng có hai tệp tương tự. Chúng ta sử dụng `autoload.php` để ưu tiên tải hai tệp từ thư mục gốc của dự án là `support/Request.php` và `support/Response.php`, điều này giúp chúng ta có thể tùy chỉnh nội dung của hai tệp này mà không cần sửa đổi tệp trong `vendor`. Nếu bạn không cần tùy chỉnh chúng, bạn có thể bỏ qua cấu hình này.
