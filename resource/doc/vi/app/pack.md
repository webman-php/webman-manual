# Gói lên

Ví dụ: gói ứng dụng plugin foo

- Đặt số phiên bản trong `plugin/foo/config/app.php` (**Quan trọng**)
- Xóa các tệp không cần thiết trong thư mục `plugin/foo`, đặc biệt là các tệp tạm thời trong `plugin/foo/public` được sử dụng cho chức năng tải lên thử nghiệm
- Xóa cài đặt cơ sở dữ liệu và Redis, nếu dự án của bạn có cài đặt cơ sở dữ liệu và Redis độc lập, cấu hình này nên được kích hoạt thông qua chương trình hướng dẫn cài đặt khi truy cập ứng dụng lần đầu (cần tự thực hiện), để quản trị viên điền thông tin và tạo thành công.
- Khôi phục các tệp khác cần phục hồi
- Sau khi hoàn tất các thao tác trên, vào thư mục `{dự án chính}/plugin/`, sử dụng lệnh `zip -r foo.zip foo` để tạo ra foo.zip
