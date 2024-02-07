# Kiểm tra và vô hiệu hóa các hàm

Sử dụng tập lệnh này để kiểm tra xem có bất kỳ hàm nào bị vô hiệu hóa không. Chạy lệnh dưới đây trong cửa sổ dòng lệnh:

```bash
curl -Ss https://www.workerman.net/webman/check | php
```

Nếu có thông báo `Functions tên_hàm đã bị vô hiệu hóa. Vui lòng kiểm tra disable_functions trong tập tin php.ini` có nghĩa là các hàm phụ thuộc vào webman đã bị vô hiệu hóa và cần phải bỏ vô hiệu hóa trong tập tin php.ini để sử dụng webman một cách bình thường.

Để bỏ vô hiệu hóa, bạn có thể tham khảo cách thực hiện từ các phương pháp sau:

## Phương pháp một
Cài đặt `webman/console`
```bash
composer require webman/console ^v1.2.35
```

Chạy lệnh sau
```bash
php webman fix-disable-functions
```

## Phương pháp hai
Chạy tập lệnh sau để bỏ vô hiệu hóa:
```bash
curl -Ss https://www.workerman.net/webman/fix-disable-functions | php
```

## Phương pháp ba
Chạy `php --ini` để tìm vị trí tập tin php.ini mà php cli sử dụng.

Mở tập tin php.ini, tìm `disable_functions` và bỏ vô hiệu hóa các hàm sau:
```apacheconf
stream_socket_server
stream_socket_client
pcntl_signal_dispatch
pcntl_signal
pcntl_alarm
pcntl_fork
posix_getuid
posix_getpwuid
posix_kill
posix_setsid
posix_getpid
posix_getpwnam
posix_getgrnam
posix_getgid
posix_setgid
posix_initgroups
posix_setuid
posix_isatty
proc_open
proc_get_status
proc_close
shell_exec
```
