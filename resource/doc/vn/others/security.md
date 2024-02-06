# An toàn

## Người dùng chạy
Đề xuất đặt người dùng chạy ứng dụng là người dùng có đặc quyền thấp hơn, ví dụ giống như người dùng chạy nginx. Người dùng chạy được thiết lập trong `config/server.php` với `user` và `group`.
Người dùng cho các tiến trình tùy chỉnh được chỉ định thông qua `user` và `group` trong `config/process.php`.
Lưu ý không nên thiết lập người dùng chạy cho tiến trình giám sát vì nó cần có đặc quyền cao để hoạt động đúng.

## Quy tắc điều khiển
Chỉ được đặt tệp điều khiển trong thư mục `controller` hoặc các thư mục con khác, cấm đặt các tệp lớp khác, nếu không có thể bị truy cập URL trái phép khi không bật [Hậu tố Điều khiển](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), gây ra hậu quả không thể dự đoán.
Ví dụ `app/controller/model/User.php` thực tế là một lớp Model, nhưng lại đặt sai thư mục `controller`, nếu không bật [Hậu tố Điều khiển](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), người dùng có thể truy cập vào bất kỳ phương thức nào trong `User.php` bằng URL như `/model/user/xxx`.
Để loại bỏ hoàn toàn tình huống này, rất khuyến khích sử dụng [Hậu tố Điều khiển](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) để đánh dấu rõ ràng những tệp điều khiển.

## Lọc XSS
Với tính linh hoạt, webman không thực hiện việc chuyển đổi XSS trên yêu cầu.
webman mạnh mẽ khuyến nghị thực hiện việc chuyển đổi XSS khi kết xuất, thay vì chuyển đổi trước khi nhập cơ sở dữ liệu.
Ngoài ra, các mẫu như twig, blade, think-tmplate tự động thực hiện chuyển đổi XSS, không cần phải chuyển đổi thủ công, rất tiện lợi.

> **Gợi ý**
> Nếu bạn thực hiện chuyển đổi XSS trước khi nhập cơ sở dữ liệu, có thể gây ra một số vấn đề không tương thích với các plugin ứng dụng

## Ngăn chặn SQL Injection
Để ngăn chặn SQL Injection, hãy cố gắng sử dụng ORM, như [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html), [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), hạn chế việc tự lắp ráp SQL.

## Proxy nginx
Khi ứng dụng của bạn cần phải công khai cho người dùng trên mạng, rất đề xuất thêm một proxy nginx trước webman, điều này giúp lọc một số yêu cầu HTTP không hợp lệ, nâng cao tính an toàn. Vui lòng tham khảo [nginx proxy](nginx-proxy.md) để biết thêm chi tiết.
