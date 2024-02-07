# Cấu trúc thư mục
```plaintext
.
├── app                           Thư mục ứng dụng
│   ├── controller                Thư mục điều khiển
│   ├── model                     Thư mục mô hình
│   ├── view                      Thư mục xem
│   ├── middleware                Thư mục middleware
│   │   └── StaticFile.php        Middleware tập tin tĩnh tích hợp sẵn
|   └── functions.php             Viết các hàm tùy chỉnh của doanh nghiệp vào tệp này
|
├── config                        Thư mục cấu hình
│   ├── app.php                   Cấu hình ứng dụng
│   ├── autoload.php              Các tệp được cấu hình ở đây sẽ tự động tải
│   ├── bootstrap.php             Cấu hình gọi lại chạy khi tiến trình bắt đầu onWorkerStart
│   ├── container.php             Cấu hình container
│   ├── dependence.php            Cấu hình phụ thuộc của container
│   ├── database.php              Cấu hình cơ sở dữ liệu
│   ├── exception.php             Cấu hình ngoại lệ
│   ├── log.php                   Cấu hình nhật ký
│   ├── middleware.php            Cấu hình middleware
│   ├── process.php               Cấu hình tiến trình tùy chỉnh
│   ├── redis.php                 Cấu hình redis
│   ├── route.php                 Cấu hình định tuyến
│   ├── server.php                Cấu hình máy chủ bao gồm cổng, số tiến trình, vv.
│   ├── view.php                  Cấu hình xem
│   ├── static.php                Cấu hình tắt/mở tệp tĩnh và cấu hình middleware tệp tĩnh
│   ├── translation.php           Cấu hình đa ngôn ngữ
│   └── session.php               Cấu hình phiên
├── public                        Thư mục tài nguyên tĩnh
├── process                       Thư mục tiến trình tùy chỉnh
├── runtime                       Thư mục thời gian chạy của ứng dụng, cần có quyền ghi
├── start.php                     Tệp khởi động dịch vụ
├── vendor                        Thư mục thư viện của bên thứ ba được cài đặt bởi composer
└── support                       Thư viện điều chỉnh (bao gồm các thư viện của bên thứ ba)
    ├── Request.php               Lớp yêu cầu
    ├── Response.php              Lớp phản hồi
    ├── Plugin.php                Kịch bản cài đặt và gỡ bỏ plugin
    ├── helpers.php               Hàm trợ giúp (vui lòng viết các hàm tùy chỉnh của doanh nghiệp vào app/functions.php)
    └── bootstrap.php             Kịch bản khởi chạy sau khi tiến trình bắt đầu
```
