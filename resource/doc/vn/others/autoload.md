# Tự động tải

## Tải các tệp theo quy tắc PSR-0 bằng composer
webman tuân theo quy tắc tải tự động `PSR-4`. Nếu doanh nghiệp của bạn cần tải thư viện mã nguồn theo quy tắc `PSR-0`, hãy tham khảo các bước sau.

- Tạo thư mục `extend` để lưu trữ mã nguồn theo quy tắc `PSR-0`
- Chỉnh sửa `composer.json`, thêm nội dung sau vào `autoload`

```js
"psr-0" : {
    "": "extend/"
}
```
Kết quả cuối cùng sẽ giống như sau
![](../../assets/img/psr0.png)

- Thực thi `composer dumpautoload`
- Thực thi `php start.php restart` để khởi động lại webman (lưu ý rằng phải khởi động lại mới có hiệu lực)

## Tải các tệp nhất định bằng composer

- Chỉnh sửa `composer.json`, thêm các tệp sẽ được tải vào phần `autoload.files`
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Thực thi `composer dumpautoload`
- Thực thi `php start.php restart` để khởi động lại webman (lưu ý rằng phải khởi động lại mới có hiệu lực)

> **Gợi ý**
> Tệp cấu hình trong tệp `composer.json` sẽ được tải trước khi webman khởi động. Tuy nhiên, tệp được tải bằng cách sử dụng `config/autoload.php` của framework sẽ được tải sau khi webman khởi động.
> Việc thay đổi các tệp được tải trong `autoload.files` của `composer.json` yêu cầu phải khởi động lại để có hiệu lực, không đủ với việc tự động tải lại. Trong khi đó, việc thay đổi các tệp được tải bằng cách sử dụng `config/autoload.php` của framework hỗ trợ tải lại ngay lập tức sau khi thay đổi.

## Tải các tệp nhất định bằng framework
Một số tệp có thể không phù hợp với quy tắc SPR, không thể tải tự động. Chúng ta có thể tải các tệp này thông qua cấu hình `config/autoload.php`, ví dụ:

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **Gợi ý**
 > Chúng ta thấy rằng `autoload.php` đã cài đặt việc tải `support/Request.php` và `support/Response.php` vì có hai tệp tương tự trong thư mục `vendor/workerman/webman-framework/src/support/`. Chúng ta ưu tiên tải các tệp từ thư mục gốc của dự án, cho phép tùy chỉnh nội dung của hai tệp mà không cần sửa đổi các tệp trong thư mục `vendor`. Nếu bạn không cần tùy chỉnh chúng, bạn có thể bỏ qua cấu hình này.
