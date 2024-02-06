## Xử lý tập tin tĩnh
webman hỗ trợ truy cập tập tin tĩnh, tất cả các tập tin tĩnh được đặt trong thư mục `public`. Ví dụ, truy cập `http://127.0.0.8787/upload/avatar.png` thực tế là truy cập `{thư mục gốc dự án}/public/upload/avatar.png`.

> **Chú ý**
> Từ phiên bản 1.4, webman hỗ trợ plugin ứng dụng, việc truy cập tập tin tĩnh bắt đầu bằng `/app/xx/tên_tập_tin` thực tế là truy cập vào thư mục `public` của plugin ứng dụng, nghĩa là webman >=1.4.0 không hỗ trợ việc truy cập thư mục `{thư mục gốc dự án}/public/app/`.
> Xem thêm tại [Plugin ứng dụng](./plugin/app.md)

### Tắt hỗ trợ tập tin tĩnh
Nếu không cần hỗ trợ tập tin tĩnh, hãy mở `config/static.php` và thay đổi tùy chọn `enable` thành false. Sau khi tắt, tất cả các truy cập tập tin tĩnh sẽ trả về lỗi 404.

### Thay đổi thư mục tập tin tĩnh
Mặc định, webman sử dụng thư mục public làm thư mục tập tin tĩnh. Nếu cần thay đổi, hãy sửa đổi hàm trợ giúp `public_path()` trong `support/helpers.php`.

### Middleware tập tin tĩnh
Webman có một middleware xử lý tập tin tĩnh mặc định, vị trí `app/middleware/StaticFile.php`.
Đôi khi chúng ta cần xử lý tập tin tĩnh một cách nào đó, ví dụ như thêm tiêu đề HTTP CORS cho tập tin tĩnh hoặc cấm truy cập vào tập tin bắt đầu bằng dấu chấm (`.`).

Nội dung `app/middleware/StaticFile.php` tương tự như sau:
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
        // Cấm truy cập vào các tập tin ẩn bắt đầu bằng dấu chấm
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Thêm tiêu đề HTTP CORS
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Nếu cần middleware này, hãy kích hoạt nó trong tùy chọn `middleware` trong `config/static.php`.
