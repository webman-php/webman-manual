# Đóng gói

Ví dụ đóng gói ứng dụng plugin foo

* Đặt số phiên bản trong tệp `plugin/foo/config/app.php` (**Quan trọng**)
* Xóa các tệp không cần thiết trong thư mục `plugin/foo`, đặc biệt là các tệp tạm thời trong `plugin/foo/public` được sử dụng cho chức năng tải lên thử nghiệm
* Xóa cấu hình cơ sở dữ liệu, Redis. Nếu dự án của bạn có cấu hình cơ sở dữ liệu, Redis độc lập, các cấu hình này sẽ được kích hoạt khi truy cập ứng dụng lần đầu tiên (cần tự thực hiện), khiến người quản trị phải điền thông tin và tạo ra.
* Khôi phục các tệp khác cần phải được khôi phục về trạng thái ban đầu
* Sau khi hoàn tất các thao tác trên, đi vào thư mục `{thư mục chính}/plugin/`, sử dụng lệnh `zip -r foo.zip foo` để tạo file foo.zip
