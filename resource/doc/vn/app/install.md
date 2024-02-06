# Cài đặt

Có hai cách để cài đặt plugin ứng dụng:

## Cài đặt từ Cửa hàng Plugin
Truy cập vào trang plugin ứng dụng tại [trang quản trị chính thức webman-admin](https://www.workerman.net/plugin/82), sau đó nhấn nút cài đặt để cài đặt plugin ứng dụng tương ứng.

## Cài đặt từ mã nguồn
Tải xuống file nén plugin ứng dụng từ cửa hàng ứng dụng, giải nén và tải thư mục giải nén lên thư mục `{thư mục chính}/plugin/` (nếu thư mục plugin không tồn tại thì cần phải tạo mới), chạy lệnh `php webman app-plugin:install tên_plugin` để hoàn tất quá trình cài đặt.

Ví dụ: Tên file nén tải về là ai.zip, giải nén vào `{thư mục chính}/plugin/ai`, thực hiện lệnh `php webman app-plugin:install ai` để hoàn tất quá trình cài đặt.


# Gỡ cài đặt

Tương tự, việc gỡ cài đặt plugin ứng dụng cũng có hai cách:

## Gỡ cài đặt từ Cửa hàng Plugin
Truy cập vào trang plugin ứng dụng tại [trang quản trị chính thức webman-admin](https://www.workerman.net/plugin/82), sau đó nhấn nút gỡ cài đặt để gỡ bỏ plugin ứng dụng tương ứng.

## Gỡ cài đặt từ mã nguồn
Chạy lệnh `php webman app-plugin:uninstall tên_plugin` để gỡ cài đặt, sau đó thủ công xóa thư mục plugin tương ứng trong thư mục `{thư mục chính}/plugin/`.
