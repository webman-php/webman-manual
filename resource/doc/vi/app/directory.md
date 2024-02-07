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

Chúng ta có thể thấy rằng một ứng dụng plugin có cấu trúc thư mục và tệp cấu hình tương tự như webman, thực tế, trải nghiệm phát triển và phát triển ứng dụng thông thường của webman gần như không có sự khác biệt. Thư mục plugin và tên tuân theo chuẩn PSR4, vì tất cả các plugin đều nằm trong thư mục plugin, vì vậy không gian tên bắt đầu bằng plugin, ví dụ `plugin\foo\app\controller\UserController`.

## Về thư mục api
Mỗi plugin đều có một thư mục api, nếu ứng dụng của bạn cung cấp một số giao diện nội bộ cho ứng dụng khác gọi, bạn cần đặt giao diện đó vào thư mục api.
Lưu ý, giao diện ở đây là giao diện cuộc gọi hàm, không phải giao diện mạng.
Ví dụ, `plugin/email/api/Email.php` cung cấp một giao diện `Email::send()` để ứng dụng khác gọi để gửi email.
Ngoài ra, `plugin/email/api/Install.php` được tạo tự động, được sử dụng để cho thị trường plugin webman-admin gọi để thực hiện cài đặt hoặc gỡ cài đặt.
