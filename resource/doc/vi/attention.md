# Lưu ý lập trình

## Hệ điều hành
webman hỗ trợ chạy trên cả hệ điều hành Linux và Windows. Tuy nhiên, do workerman không hỗ trợ cài đặt multi-process và daemon trên Windows, nên chỉ nên sử dụng Windows để phát triển và debug trong môi trường phát triển. Đối với môi trường sản phẩm, vui lòng sử dụng hệ điều hành Linux.

## Cách khởi động
**Trên hệ điều hành Linux**, sử dụng lệnh `php start.php start` (chế độ debug) hoặc `php start.php start -d` (chế độ daemon) để khởi động.
**Trên hệ điều hành Windows**, chạy `windows.bat` hoặc sử dụng lệnh `php windows.php` để khởi động và nhấn ctrl c để dừng. Hệ điều hành Windows không hỗ trợ các lệnh stop, reload, status, reload connections.

## Chạy trong bộ nhớ
webman là một framework chạy trong bộ nhớ vĩnh viễn. Thông thường, sau khi file PHP được tải vào bộ nhớ, nó sẽ được tái sử dụng và không cần đọc từ đĩa nữa (ngoại trừ các file template). Vì vậy, sau khi thay đổi mã nguồn hoặc cấu hình trong môi trường sản phẩm, cần thực hiện `php start.php reload` để ứng dụng được cập nhật. Nếu thay đổi cấu hình liên quan đến process hoặc cài đặt thư viện mới từ composer, cần khởi động lại bằng `php start.php restart`.

> Để tiện cho việc phát triển, webman được tích hợp với một process monitor để giám sát file mã nguồn. Khi có sự thay đổi trong file mã nguồn, nó sẽ tự động thực hiện reload. Tính năng này chỉ hoạt động khi workerman chạy ở chế độ debug (không sử dụng `-d` khi khởi động). Người dùng Windows cần chạy `windows.bat` hoặc `php windows.php` để sử dụng tính năng này.

## Về việc xuất dữ liệu
Trong các dự án truyền thống PHP-FPM, sử dụng các hàm như `echo` hoặc `var_dump` để xuất dữ liệu sẽ hiển thị trực tiếp trên trang web. Tuy nhiên, trong webman, những dữ liệu này thường hiển thị trên cửa sổ terminal và không xuất hiện trên trang web (ngoại trừ việc xuất dữ liệu từ file template).

## Không sử dụng lệnh `exit` `die`
Sử dụng lệnh `exit` hoặc `die` sẽ dẫn đến việc tiến trình kết thúc và khởi động lại, làm cho yêu cầu hiện tại không thể được xử lý đúng cách.

## Không sử dụng hàm `pcntl_fork`
Sử dụng `pcntl_fork` để tạo một tiến trình là không được phép trong webman.
