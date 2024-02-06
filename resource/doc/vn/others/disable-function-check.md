# Vô hiệu hóa kiểm tra hàm

Sử dụng tập lệnh này để kiểm tra xem có bất kỳ hàm nào bị vô hiệu hóa hay không. Chạy tập lệnh dưới đây trong dòng lệnh: ```curl -Ss https://www.workerman.net/webman/check | php```

Nếu hiển thị thông báo ```Functions tên_hàm đã bị vô hiệu hóa. Vui lòng kiểm tra disable_functions trong php.ini```, điều này cho biết các hàm mà webman phụ thuộc vào đã bị vô hiệu hóa và cần phải bỏ vô hiệu hóa trong tệp php.ini để sử dụng webman một cách bình thường.
Để bỏ vô hiệu hóa, hãy tham khảo một trong các phương pháp dưới đây.

## Phương pháp 1
Cài đặt `webman/console` 
```
composer require webman/console ^v1.2.35
```

Chạy tập lệnh
```
php webman fix-disable-functions
```

## Phương pháp 2

Chạy tập lệnh `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` để bỏ vô hiệu hóa

## Phương pháp 3

Chạy tập lệnh `php --ini` để xác định vị trí tệp php.ini được sử dụng bởi php cli

Mở tệp php.ini, tìm `disable_functions`, và bỏ vô hiệu hóa việc gọi các hàm sau:
```
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
