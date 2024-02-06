# Hiệu suất của webman


### Quá trình xử lý yêu cầu trong khung ứng dụng truyền thống

1. Nginx/apache nhận yêu cầu
2. Nginx/apache chuyển yêu cầu sang php-fpm
3. Php-fpm khởi tạo môi trường, như tạo danh sách biến
4. Php-fpm gọi RINIT của các extension/module khác nhau
5. Php-fpm đọc tệp php từ đĩa (sử dụng opcache để tránh)
6. Php-fpm phân tích từ vựng, phân tích cú pháp, biên dịch thành opcode (sử dụng opcache để tránh)
7. Php-fpm thực thi opcode bao gồm 8.9.10.11
8. Khung ứng dụng khởi tạo, như việc khởi tạo các lớp khác nhau, bao gồm cả các thùng chứa, điều khiển, định tuyến, middleware, v.v.
9. Khung ứng dụng kết nối cơ sở dữ liệu và xác thực quyền, kết nối redis
10. Khung ứng dụng thực hiện logic kinh doanh
11. Khung ứng dụng đóng kết nối cơ sở dữ liệu, kết nối redis
12. Php-fpm giải phóng tài nguyên, hủy tất cả các định nghĩa lớp, thể hiện, hủy bảng ký hiệu, v.v.
13. Php-fpm gọi tuần tự các phương thức RSHUTDOWN của các extension/module khác nhau
14. Php-fpm chuyển kết quả qua lại cho nginx/apache
15. Nginx/apache trả kết quả về cho người dùng


### Quá trình xử lý yêu cầu trong webman
1. Khung ứng dụng nhận yêu cầu
2. Khung ứng dụng thực hiện logic kinh doanh
3. Khung ứng dụng trả kết quả về cho người dùng

Đúng vậy, trong trường hợp không có nginx reverse proxy, khung ứng dụng chỉ có 3 bước này. Có thể nói đây là điểm đến tối ưu của khung ứng dụng php, điều này khiến hiệu suất của webman cao hơn một vài lần so với các khung ứng dụng truyền thống.

Xem thêm [Kiểm thử áp lực](benchmarks.md)
