# Yêu cầu về môi trường

## Hệ điều hành Linux
Hệ điều hành Linux yêu cầu sử dụng extension `posix` và `pcntl`, hai extension này là mặc định có sẵn trong PHP, thường không cần cài đặt lại.

Nếu bạn là người dùng của Bảo tàng, bạn chỉ cần tắt hoặc xóa các hàm bắt đầu bằng `pnctl_` trong Bảo tàng.

Extension `event` không bắt buộc, nhưng để tối ưu hóa hiệu suất, khuyến nghị cài đặt extension này.

## Hệ điều hành Windows
Webman có thể hoạt động trên hệ điều hành Windows, nhưng do không thể thiết lập nhiều tiến trình và tiến trình bảo vệ, do đó, Windows chỉ được khuyến nghị sử dụng làm môi trường phát triển, trong khi môi trường sản xuất nên sử dụng hệ điều hành Linux.

Lưu ý: Không cần extension `posix` và `pcntl` dưới hệ điều hành Windows.
