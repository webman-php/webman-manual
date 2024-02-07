# Hiệu suất của webman


### Quy trình xử lý yêu cầu của khung mạng truyền thống

1. Nginx / apache nhận yêu cầu
2. Nginx / apache chuyển yêu cầu cho php-fpm
3. Php-fpm khởi tạo môi trường, như tạo danh sách biến
4. Php-fpm gọi RINIT của các tiện ích / mô-đun
5. Php-fpm đọc tệp php từ ổ đĩa (có thể tránh được bằng opcache)
6. Php-fpm phân tích cú pháp, phân tích cú pháp, biên dịch thành opcode (có thể tránh được bằng opcache)
7. Php-fpm thực thi opcode bao gồm 8.9.10.11
8. Khung khởi tạo, chẳng hạn như khởi tạo các lớp khác nhau, bao gồm các thành phần như bộ chứa, điều khiển, tuyến đường, middleware, v.v.
9. Khung kết nối cơ sở dữ liệu và xác thực quyền, kết nối redis
10. Khung thực thi logic kinh doanh
11. Khung đóng kết nối cơ sở dữ liệu, kết nối redis
12. Php-fpm giải phóng tài nguyên, hủy tất cả các định nghĩa lớp, thể hiện, hủy bảng ký tự, v.v.
13. Php-fpm gọi tuần tự các phương thức RSHUTDOWN của các tiện ích / mô-đun
14. Php-fpm chuyển kết quả cho nginx / apache
15. Nginx / apache trả kết quả về cho máy khách


### Quy trình xử lý của webman

1. Khung nhận yêu cầu
2. Khung thực thi logic kinh doanh
3. Khung trả kết quả về cho máy khách

Đúng vậy, trong trường hợp không có nginx đảo ngược, khung chỉ có 3 bước này. Có thể nói rằng điều này đã là hết sức cho khung PHP, điều này làm cho hiệu suất của webman là vài lần thậm chí là mấy chục lần so với khung truyền thống.

Xem thêm tại [Thử nghiệm áp lực](benchmarks.md)
