# An toàn

## Người dùng chạy
Đề xuất thiết lập người dùng chạy là người dùng có đặc quyền thấp hơn, ví dụ như giống với người dùng chạy nginx. Người dùng chạy được thiết lập trong `config/server.php` với `user` và `group`. Tương tự, người dùng cho các tiến trình tùy chỉnh được chỉ định thông qua `user` và `group` trong `config/process.php`. Chú ý rằng, không nên thiết lập người dùng chạy cho tiến trình giám sát vì nó cần đặc quyền cao để hoạt động bình thường.

## Quy tắc điều khiển
Chỉ có thể đặt tệp điều khiển trong thư mục hoặc thư mục con của `controller`, cấm đặt các tệp lớp khác. Nếu không, khi chưa bật [hậu tố điều khiển](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), tệp lớp có thể bị truy cập bất hợp pháp qua URL, gây ra hậu quả không lường trước. Ví dụ, `app/controller/model/User.php` thực tế là lớp Model, nhưng lại bị đặt sai vào thư mục `controller`, không bật [hậu tố điều khiển](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80), người dùng có thể truy cập bất kỳ phương thức nào trong `User.php` qua `/model/user/xxx`. Để loại bỏ hoàn toàn tình huống này, mạnh mẽ khuyến khích sử dụng [hậu tố điều khiển](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) để xác định rõ ràng các tệp là tệp điều khiển.

## Lọc XSS
Xem xét tính chung, webman không thực hiện việc chuyển đổi XSS cho yêu cầu. webman mạnh mẽ khuyến nghị thực hiện việc chuyển đổi XSS khi kết xuất, thay vì chuyển đổi trước khi lưu vào cơ sở dữ liệu. Ngoài ra, các mẫu twig, blade, think-tmplate và các mẫu khác sẽ tự động thực hiện việc chuyển đổi XSS, không cần phải chuyển đổi thủ công, rất thuận tiện.

> **Gợi ý**
> Nếu bạn chuyển đổi XSS trước khi lưu vào cơ sở dữ liệu, có thể gây ra một số vấn đề không tương thích với các plugin ứng dụng.

## Ngăn chặn SQL Injection
Để ngăn chặn SQL Injection, hãy cố gắng sử dụng ORM như [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html), [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html) và tránh sử dụng tự điều chỉnh SQL.

## Proxy nginx
Khi ứng dụng của bạn cần phải mở ra cho người dùng trên internet, rất đề xuất thêm một proxy nginx trước webman, điều này sẽ có thể lọc một số yêu cầu HTTP bất hợp pháp và cải thiện tính an toàn. Vui lòng xem chi tiết tại [proxy nginx](nginx-proxy.md).
