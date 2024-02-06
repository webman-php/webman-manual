# Bộ điều khiển

Theo quy tắc PSR4, không gian tên lớp bộ điều khiển bắt đầu bằng `plugin\{định danh plugin}`, ví dụ

Tạo tệp điều khiển mới `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('Xin chào index');
    }
    
    public function hello(Request $request)
    {
        return response('Xin chào webman');
    }
}
```

Khi truy cập `http://127.0.0.1:8787/app/foo/foo`, trang web trả về `Xin chào index`

Khi truy cập `http://127.0.0.1:8787/app/foo/foo/hello`, trang web trả về `Xin chào webman`

## Truy cập url
Đường dẫn url của ứng dụng plugin bắt đầu bằng `/app`, sau đó là định danh plugin, tiếp theo là điều khiển cụ thể và phương thức.
Ví dụ định danh url của `plugin\foo\app\controller\UserController` là `http://127.0.0.1:8787/app/foo/user`
