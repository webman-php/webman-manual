## Xử lý tập tin tĩnh
webman hỗ trợ truy cập tập tin tĩnh, tất cả các tập tin tĩnh đều được đặt trong thư mục `public`, ví dụ truy cập `http://127.0.0.8787/upload/avatar.png` thực ra là truy cập `{thư mục dự án chính}/public/upload/avatar.png`.

> **Lưu ý**
> Từ phiên bản 1.4 trở đi, webman hỗ trợ plugin ứng dụng, việc truy cập tập tin tĩnh bắt đầu bằng `/app/xx/tên_tập_tin` thực ra là truy cập vào thư mục `public` của plugin ứng dụng, tức là từ webman >= 1.4.0 không hỗ trợ truy cập các thư mục tại `{thư mục dự án chính}/public/app/`.
> Để biết thêm thông tin, vui lòng tham khảo [Plugin ứng dụng](./plugin/app.md).

### Tắt hỗ trợ tập tin tĩnh
Nếu không cần hỗ trợ tập tin tĩnh, hãy mở tệp `config/static.php` và đổi tùy chọn `enable` thành false. Sau khi tắt, mọi yêu cầu truy cập tập tin tĩnh sẽ trả về lỗi 404.

### Thay đổi thư mục tập tin tĩnh
webman mặc định sử dụng thư mục public làm thư mục tập tin tĩnh. Nếu cần thay đổi, hãy sửa đổi hàm trợ giúp `public_path()` trong `support/helpers.php`.

### Middleware tập tin tĩnh
webman đi kèm với một middleware tập tin tĩnh, vị trí là `app/middleware/StaticFile.php`.
Đôi khi chúng ta cần xử lý một số tập tin tĩnh, ví dụ như thêm tiêu đề HTTP cho tập tin tĩnh, cấm truy cập các tập tin bắt đầu bằng dấu chấm (`.`), có thể sử dụng middleware này.

Nội dung của `app/middleware/StaticFile.php` tương tự như sau:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Cấm truy cập vào các tệp ẩn bắt đầu bằng dấu chấm
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Thêm tiêu đề HTTP vượt quá tên miền
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Nếu cần sử dụng middleware này, bạn cần mở tệp `config/static.php` và bật tùy chọn `middleware`.
