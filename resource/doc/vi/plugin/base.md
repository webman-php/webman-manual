# Các Plugin Cơ Bản

Các plugin cơ bản thường là những thành phần chung, thường được cài đặt bằng composer và mã nguồn được đặt trong thư mục vendor. Trong quá trình cài đặt, bạn có thể tự động sao chép một số cấu hình tùy chỉnh (middleware, process, route, v.v.) vào thư mục `{thư mục gốc của dự án}config/plugin`, webman sẽ tự động nhận diện cấu hình trong thư mục này và hợp nhất cấu hình vào cấu hình chính, từ đó cho phép plugin có thể can thiệp vào bất kỳ giai đoạn nào trong vòng đời của webman.

Xem thêm tại [Tạo plugin cơ bản](create.md)
