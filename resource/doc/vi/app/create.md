# Tạo Plugin Ứng dụng

## Định danh duy nhất

Mỗi plugin đều có một định danh duy nhất, trước khi phát triển, các nhà phát triển cần nghĩ ra một định danh và kiểm tra xem định danh đó có bị sử dụng hay không. 
Kiểm tra tại [Kiểm tra định danh ứng dụng](https://www.workerman.net/app/check)

## Tạo mới

Chạy `composer require webman/console` để cài đặt command-line của webman

Sử dụng lệnh `php webman app-plugin:create {định danh plugin}` để tạo một plugin ứng dụng trên máy cục bộ

Ví dụ: `php webman app-plugin:create foo`

Khởi động lại webman

Truy cập `http://127.0.0.1:8787/app/foo` Nếu có nội dung trả về, điều đó chứng tỏ việc tạo thành công.
