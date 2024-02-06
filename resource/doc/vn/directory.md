# Cấu trúc thư mục
```
.
├── app                           Thư mục ứng dụng
│   ├── controller                Thư mục điều khiển
│   ├── model                     Thư mục mô hình
│   ├── view                      Thư mục chế độ xem
│   ├── middleware                Thư mục middleware
│   │   └── StaticFile.php        Middleware tệp tĩnh tích hợp sẵn
│   └── functions.php             Định nghĩa các hàm doanh nghiệp trong tệp này
│
├── config                        Thư mục cấu hình
│   ├── app.php                   Cấu hình ứng dụng
│   ├── autoload.php              Các tệp được cấu hình ở đây sẽ tự động tải
│   ├── bootstrap.php             Cấu hình callback chạy khi Worker được khởi động
│   ├── container.php             Cấu hình container
│   ├── dependence.php            Cấu hình phụ thuộc của container
│   ├── database.php              Cấu hình cơ sở dữ liệu
│   ├── exception.php             Cấu hình ngoại lệ
│   ├── log.php                   Cấu hình nhật ký
│   ├── middleware.php            Cấu hình middleware
│   ├── process.php               Cấu hình tiến trình tùy chỉnh
│   ├── redis.php                 Cấu hình redis
│   ├── route.php                 Cấu hình định tuyến
│   ├── server.php                Cấu hình máy chủ bao gồm cổng, số lượng tiến trình và cài đặt khác
│   ├── view.php                  Cấu hình chế độ xem
│   ├── static.php                Cấu hình tệp tĩnh và middleware tệp tĩnh
│   ├── translation.php           Cấu hình đa ngôn ngữ
│   └── session.php               Cấu hình session
│
├── public                        Thư mục tài nguyên tĩnh
├── process                       Thư mục tiến trình tùy chỉnh
├── runtime                       Thư mục thời gian chạy ứng dụng, cần phải có quyền ghi
├── start.php                     Tệp khởi động dịch vụ
├── vendor                        Thư mục thư viện bên thứ ba được cài đặt bởi composer
└── support                       Thư viện tương thích (bao gồm thư viện bên thứ ba)
    ├── Request.php               Lớp yêu cầu
    ├── Response.php              Lớp phản hồi
    ├── Plugin.php                Tệp cài đặt và gỡ cài đặt plugin
    ├── helpers.php               Hàm trợ giúp (hãy đặt hàm doanh nghiệp tùy chỉnh vào app/functions.php)
    └── bootstrap.php             Tệp khởi động sau khi tiến trình được khởi động
```
