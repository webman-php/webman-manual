# Lưu ý lập trình

## Hệ điều hành
webman hỗ trợ cả hệ điều hành linux và windows. Tuy nhiên, do workerman không hỗ trợ cài đặt multi-process và daemon trên windows, nên chỉ nên sử dụng windows cho môi trường phát triển và gỡ lỗi, còn môi trường sản xuất thì nên sử dụng hệ điều hành linux.

## Cách khởi động
**Hệ điều hành linux**: Sử dụng lệnh `php start.php start` (chế độ debug) hoặc `php start.php start -d` (chế độ daemon) để khởi động.
**Hệ điều hành windows**: Chạy `windows.bat` hoặc sử dụng lệnh `php windows.php` để khởi động, sử dụng ctrl c để dừng. Hệ điều hành windows không hỗ trợ các lệnh stop, reload, status, reload connections.

## Chạy trong bộ nhớ không chết
webman là một framework chạy trong bộ nhớ không chết. Thông thường, khi tệp php được tải vào bộ nhớ, nó sẽ được sử dụng lại và không được đọc từ đĩa lưu trữ nữa (ngoại trừ các tệp mẫu). Do đó, sau khi thay đổi mã nguồn hoặc cấu hình trong môi trường sản xuất, bạn cần thực hiện `php start.php reload` để có hiệu lực. Nếu bạn thay đổi cấu hình liên quan đến các tiến trình hoặc cài đặt gói composer mới, bạn cần khởi động lại bằng lệnh `php start.php restart`.

> Để thuận tiện cho việc phát triển, webman đi kèm với một quy trình tuỳ chỉnh để theo dõi cập nhật tệp kinh doanh. Khi có tệp kinh doanh được cập nhật, nó sẽ tự động thực hiện reload. Chức năng này chỉ hoạt động khi workerman chạy ở chế độ debug (không sử dụng `-d` khi khởi động). Người dùng windows cần chạy `windows.bat` hoặc `php windows.php` để sử dụng chức năng này.

## Về câu lệnh xuất
Trong dự án truyền thống sử dụng php-fpm, việc sử dụng `echo`, `var_dump` và các hàm xuất dữ liệu sẽ hiển thị trực tiếp trên trang web, nhưng trong webman, những xuất dữ liệu này thường hiển thị trên terminal và không xuất hiện trên trang web (ngoại trừ các xuất dữ liệu từ tệp mẫu).

## Không sử dụng câu lệnh `exit` hoặc `die`
Việc sử dụng die hoặc exit sẽ khiến tiến trình kết thúc và khởi động lại, dẫn đến việc không thể phản hồi yêu cầu hiện tại đúng cách.

## Không sử dụng hàm `pcntl_fork`
Hàm `pcntl_fork` để tạo một tiến trình, điều này không được phép trong webman.
