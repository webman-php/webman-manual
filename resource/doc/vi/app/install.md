# Cài đặt

Có hai cách cài đặt plugin ứng dụng:

## Cài đặt từ Marketplace
Truy cập [trang plugin ứng dụng chính thức của webman-admin](https://www.workerman.net/plugin/82) và nhấn nút cài đặt để cài đặt plugin ứng dụng tương ứng.

## Cài đặt từ mã nguồn
Tải xuống file nén plugin ứng dụng từ Marketplace, giải nén và tải thư mục đã giải nén lên thư mục `/{dự án chính}/plugin/` (nếu thư mục plugin không tồn tại, bạn cần tạo thủ công), sau đó chạy lệnh `php webman app-plugin:install tên_plugin` để hoàn tất quá trình cài đặt.

Ví dụ: Nếu tên file nén là ai.zip, giải nén vào `{dự án chính}/plugin/ai`, sau đó chạy `php webman app-plugin:install ai` để hoàn tất cài đặt.

# Gỡ cài đặt

Tương tự, có hai cách gỡ cài đặt plugin ứng dụng:

## Gỡ cài đặt từ Marketplace
Truy cập [trang plugin ứng dụng chính thức của webman-admin](https://www.workerman.net/plugin/82) và nhấn nút gỡ cài đặt để gỡ cài đặt plugin ứng dụng tương ứng.

## Gỡ cài đặt từ mã nguồn
Chạy lệnh `php webman app-plugin:uninstall tên_plugin` để gỡ cài đặt, sau đó xóa thư mục plugin ứng dụng tương ứng trong thư mục `/{dự án chính}/plugin/` bằng tay.
