# webman là gì

webman là một framework dịch vụ HTTP hiệu suất cao được phát triển dựa trên [workerman](https://www.workerman.net). webman được sử dụng để thay thế kiến trúc php-fpm truyền thống, cung cấp dịch vụ HTTP có hiệu suất cực cao và có thể mở rộng. Bạn có thể sử dụng webman để phát triển trang web, cũng có thể phát triển giao diện HTTP hoặc dịch vụ micro.

Ngoài ra, webman còn hỗ trợ tiến trình tùy chỉnh, có thể thực hiện bất kỳ điều gì mà workerman có thể thực hiện, chẳng hạn như dịch vụ websocket, IoT, trò chơi, dịch vụ TCP, dịch vụ UDP, dịch vụ unix socket và nhiều hơn nữa.

# Triết lý của webman
**Cung cấp tính mở rộng tối đa và hiệu suất mạnh mẽ với hạt nhân nhỏ nhất.**

webman chỉ cung cấp những tính năng cốt lõi nhất (định tuyến, middleware, phiên, giao diện tiến trình tùy chỉnh). Các tính năng khác đều tái sử dụng từ hệ sinh thái của composer, điều này có nghĩa là bạn có thể sử dụng các thành phần chức năng quen thuộc nhất trong webman, ví dụ, trong phần cơ sở dữ liệu, người phát triển có thể chọn sử dụng `illuminate/database` của Laravel, hoặc có thể sử dụng `ThinkORM` của ThinkPHP, hoặc các thành phần khác như `Medoo`. Việc tích hợp chúng vào webman là điều rất dễ dàng.

# webman có những đặc điểm sau đây

1. Ổn định cao. webman được phát triển dựa trên workerman, workerman luôn được biết đến là framework socket ổn định với rất ít lỗi trong ngành.
2. Hiệu suất cực cao. Hiệu suất của webman cao hơn 10-100 lần so với framework php-fpm truyền thống, gấp đôi hiệu suất so với các framework như gin echo của go.
3. Tính tái sử dụng cao. Không cần sửa đổi, có thể tái sử dụng hầu hết các thành phần và thư viện của composer.
4. Tính mở rộng cao. Hỗ trợ tiến trình tùy chỉnh, có thể thực hiện bất kỳ điều gì mà workerman có thể thực hiện.
5. Cực kỳ đơn giản và dễ sử dụng, chi phí học tập cực thấp, viết code không khác biệt so với framework truyền thống.
6. Sử dụng giấy phép mã nguồn mở MIT linh hoạt và thân thiện.

# Địa chỉ dự án
GitHub: https://github.com/walkor/webman **Đừng tiếc sao nhỏ của bạn nhé**

Gitee: https://gitee.com/walkor/webman **Đừng tiếc sao nhỏ của bạn nhé**

# Dữ liệu kiểm tra áp lực từ bên thứ ba

![](../assets/img/benchmark1.png)

Với dịch vụ truy vấn cơ sở dữ liệu, webman đạt được 390.000 QPS trên máy đơn, cao gấp khoảng 80 lần so với kiến trúc laravel framework của php-fpm truyền thống.

![](../assets/img/benchmarks-go.png)

Với dịch vụ truy vấn cơ sở dữ liệu, webman có hiệu suất gấp đôi so với framework web tương tự sử dụng ngôn ngữ go.

Dữ liệu trên đến từ [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
