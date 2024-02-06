# Các plugin cơ bản

Các plugin cơ bản thường là những thành phần chung, thường được cài đặt bằng composer và mã nguồn được đặt trong thư mục vendor. Khi cài đặt, có thể tự động sao chép một số cấu hình tùy chỉnh (middleware, process, route, v.v.) vào thư mục `{project gốc}/config/plugin`, webman sẽ tự động nhận diện cấu hình trong thư mục này và hợp nhất cấu hình này vào cấu hình chính, từ đó cho phép plugin có thể can thiệp vào bất kỳ vòng đời nào của webman.

Xem thêm tại [Tạo plugin cơ bản](create.md)
