# webman là gì

webman là một framework dịch vụ HTTP hiệu suất cao dựa trên [workerman](https://www.workerman.net). webman được sử dụng để thay thế kiến trúc php-fpm truyền thống, cung cấp dịch vụ HTTP có khả năng mở rộng cao và hiệu suất cực cao. Bạn có thể sử dụng webman để phát triển trang web, cũng có thể phát triển các giao diện HTTP hoặc dịch vụ micro.

Ngoài ra, webman cũng hỗ trợ quá trình tùy chỉnh, có thể thực hiện bất kỳ công việc nào mà workerman có thể thực hiện, chẳng hạn như dịch vụ websocket, IoT, trò chơi, dịch vụ TCP, dịch vụ UDP, dịch vụ unix socket và nhiều hơn nữa.

# Triết lý của webman
**Cung cấp khả năng mở rộng tối đa và hiệu suất mạnh mẽ với lõi tối thiểu.**

webman chỉ cung cấp các chức năng cốt lõi nhất định (định tuyến, middleware, phiên, giao diện quá trình tùy chỉnh). Tất cả các chức năng khác đều được tái sử dụng từ cộng đồng composer, điều này có nghĩa là bạn có thể sử dụng các thành phần chức năng quen thuộc nhất trong webman, ví dụ, trong phần cơ sở dữ liệu, người phát triển có thể chọn sử dụng `illuminate/database` của Laravel, hoặc `ThinkORM` của ThinkPHP, hoặc các thành phần khác như `Medoo`. Việc tích hợp chúng trong webman là điều rất dễ dàng.

# Điểm đặc biệt của webman

1. Ổn định cao. webman được phát triển dựa trên workerman, workerman luôn là một framework socket ổn định với ít lỗi nhất trong ngành.

2. Hiệu suất siêu cao. Hiệu suất của webman cao hơn 10-100 lần so với framework php-fpm truyền thống, và cao hơn khoảng gấp đôi so với các framework như gin và echo của go.

3. Sử dụng lại cao. Không cần sửa đổi, có thể tái sử dụng hầu hết các thành phần và thư viện composer.

4. Mở rộng cao. Hỗ trợ quá trình tùy chỉnh, có thể thực hiện bất kỳ công việc nào mà workerman có thể thực hiện.

5. Đơn giản và dễ sử dụng vô cùng, chi phí học tập rất thấp, việc viết mã không khác biệt so với framework truyền thống.

6. Sử dụng giấy phép mã nguồn mở MIT rất linh hoạt và thân thiện.

# Địa chỉ dự án
GitHub: https://github.com/walkor/webman **Đừng tiếc sao nhỏ của bạn nha**

Mã nguồn: https://gitee.com/walkor/webman **Đừng tiếc sao nhỏ của bạn nha**

# Dữ liệu chính thức từ bên thứ ba

![](../assets/img/benchmark1.png)

Khi có thao tác truy vấn cơ sở dữ liệu, webman có thể xử lý tới 39 nghìn QPS trên một máy duy nhất, cao gấp 80 lần so với kiến trúc php-fpm truyền thống của framework Laravel.

![](../assets/img/benchmarks-go.png)

Khi có thao tác truy vấn cơ sở dữ liệu, hiệu suất của webman cao gấp đôi so với framework web của ngôn ngữ go của cùng loại.

Các dữ liệu trên đến từ [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
