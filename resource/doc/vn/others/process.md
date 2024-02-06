# Quy trình thực hiện

## Quy trình khởi động quá trình

Sau khi thực hiện lệnh `php start.php start`, quy trình thực hiện như sau:

1. Tải cấu hình trong thư mục config/
2. Thiết lập các cấu hình liên quan đến Worker như `pid_file`, `stdout_file`, `log_file`, `max_package_size`, v.v.
3. Tạo quá trình webman và lắng nghe cổng (mặc định là 8787)
4. Dựa trên cấu hình, tạo các quá trình tùy chỉnh
5. Sau khi quá trình webman và quá trình tùy chỉnh được khởi động, thực hiện logic sau (tất cả đều được thực hiện trong hàm onWorkerStart):
    ① Tải các tệp được thiết lập trong `config/autoload.php`, như `app/functions.php`
    ② Tải các middleware được thiết lập trong `config/middleware.php` (bao gồm `config/plugin/*/*/middleware.php`)
    ③ Thực thi phương thức start của các lớp được thiết lập trong `config/bootstrap.php` (bao gồm `config/plugin/*/*/bootstrap.php`), dùng để khởi tạo một số mô-đun, chẳng hạn như kết nối cơ sở dữ liệu Laravel
    ④ Tải `config/route.php` (bao gồm `config/plugin/*/*/route.php`) để định nghĩa các tuyến đường

## Quy trình xử lý yêu cầu
1. Kiểm tra xem URL yêu cầu có tương ứng với tệp tĩnh trong thư mục public không, nếu có thì trả về tệp (kết thúc yêu cầu), nếu không thì tiếp tục ở bước 2
2. Dựa trên URL, kiểm tra xem có tuyến đường nào được chọn không, nếu không thì tiếp tục ở bước 3, nếu có thì tiếp tục ở bước 4
3. Kiểm tra xem có đóng tuyến đường mặc định không, nếu có thì trả về mã lỗi 404 (kết thúc yêu cầu), nếu không thì tiếp tục ở bước 4
4. Tìm middleware của bộ điều khiển tương ứng với yêu cầu, thực hiện các thao tác tiền xử lý của middleware theo thứ tự (giai đoạn yêu cầu mô hình hành động của củ cải), thực thi logic kinh doanh của bộ điều khiển, thực hiện các thao tác hậu xử lý của middleware (giai đoạn phản hồi mô hình hành động của củ cải), kết thúc yêu cầu. (Xem chi tiết về [mô hình hành động của middleware](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
