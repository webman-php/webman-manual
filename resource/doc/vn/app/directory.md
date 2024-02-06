# Cấu trúc thư mục

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

Chúng ta thấy rằng một plugin ứng dụng có cấu trúc thư mục và tệp cấu hình tương tự như webman, thực tế, trải nghiệm phát triển ứng dụng plugin không khác biệt nhiều so với phát triển ứng dụng thông thường trên webman.
Tên và cấu trúc thư mục plugin tuân theo quy tắc PSR4, vì tất cả các plugin được đặt trong thư mục plugin, do đó không gian tên bắt đầu bằng plugin, ví dụ `plugin\foo\app\controller\UserController`.

## Về thư mục api
Mỗi plugin đều có một thư mục api, nếu ứng dụng của bạn cung cấp một số giao diện nội bộ cho ứng dụng khác sử dụng, bạn cần đặt các giao diện vào thư mục api.
Lưu ý, giao diện ở đây chỉ đề cập đến giao diện cuộc gọi hàm, không phải là giao diện cuộc gọi mạng.
Ví dụ, `plugin/email/api/Email.php` cung cấp giao diện `Email::send()` để gửi email cho ứng dụng khác sử dụng.
Ngoài ra, `plugin/email/api/Install.php` được tạo tự động, dùng để chứa lời gọi cài đặt hoặc gỡ cài đặt từ webman-admin plugin market.
