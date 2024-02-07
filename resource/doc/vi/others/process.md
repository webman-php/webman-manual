# Quy trình thực thi

## Quy trình khởi động quá trình

Khi thực hiện lệnh `php start.php start`, quy trình thực thi như sau:

1. Tải cấu hình trong thư mục config/
2. Thiết lập cấu hình liên quan đến Worker như `pid_file`, `stdout_file`, `log_file`, `max_package_size`, v.v.
3. Tạo quá trình webman và lắng nghe cổng (mặc định là 8787)
4. Tạo các quá trình tùy chỉnh dựa trên cấu hình
5. Sau khi quá trình webman và quá trình tùy chỉnh được khởi động, thực hiện các logic sau (tất cả đều được thực hiện trong onWorkerStart):
   ① Tải các tệp được cấu hình trong `config/autoload.php`, như `app/functions.php`
   ② Tải middleware được cấu hình trong `config/middleware.php` (bao gồm `config/plugin/*/*/middleware.php`)
   ③ Thực thi class start được cấu hình trong `config/bootstrap.php` (bao gồm `config/plugin/*/*/bootstrap.php`) để khởi tạo một số module, như kết nối cơ sở dữ liệu Laravel
   ④ Tải `config/route.php` (bao gồm `config/plugin/*/*/route.php`) để định nghĩa các route

## Quy trình xử lý yêu cầu
1. Kiểm tra xem URL yêu cầu có tương ứng với tệp tĩnh trong thư mục public không, nếu có thì trả về tệp đó (kết thúc yêu cầu), nếu không thì chuyển sang bước 2
2. Dựa trên URL, kiểm tra xem liệu có trùng với một route nào đó hay không. Nếu không trùng, tiến hành bước 3, nếu trùng thì tiến hành bước 4
3. Kiểm tra xem có bật tắt route mặc định không, nếu bật thì trả về 404 (kết thúc yêu cầu), nếu không bật thì tiếp tục bước 4
4. Tìm middleware tương ứng với controller được yêu cầu, thực hiện các thao tác middleware trước theo thứ tự (giai đoạn yêu cầu mô hình hành động củ chuối), thực hiện logic kinh doanh của controller, thực hiện các thao tác middleware sau (giai đoạn phản hồi mô hình chuối), kết thúc yêu cầu. (Xem thêm về [mô hình đầu cuối middleware](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
