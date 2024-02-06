# Yêu cầu về môi trường

## Hệ điều hành Linux
Hệ điều hành Linux yêu cầu các phần mở rộng `posix` và `pcntl`, hai phần mở rộng này là phần mở rộng tích hợp sẵn trong PHP, thông thường không cần cài đặt để sử dụng.

Nếu bạn sử dụng Bảo Tra, bạn chỉ cần vô hiệu hóa hoặc xóa các chức năng bắt đầu bằng `pnctl_`.

Phần mở rộng `event` không bắt buộc, nhưng để có hiệu suất tốt hơn, khuyên bạn nên cài đặt phần mở rộng này.

## Hệ điều hành Windows
Webman có thể chạy trên hệ điều hành Windows, nhưng do không thể thiết lập nhiều tiến trình, tiến trình bảo vệ và lý do khác, nên khuyến nghị Windows chỉ được sử dụng làm môi trường phát triển, trong khi môi trường sản xuất nên sử dụng hệ điều hành Linux.

Lưu ý: Dưới hệ điều hành Windows không cần phụ thuộc vào phần mở rộng `posix` và `pcntl`.
