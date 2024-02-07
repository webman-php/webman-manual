# Bộ điều khiển

Theo quy tắc PSR4, không gian tên lớp bộ điều khiển bắt đầu bằng `plugin\{plugin_identifier}`, ví dụ

Tạo tệp điều khiển mới `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('xin chào index');
    }
    
    public function hello(Request $request)
    {
        return response('xin chào webman');
    }
}
```

Khi truy cập `http://127.0.0.1:8787/app/foo/foo` thì trang web sẽ trả về `xin chào index`

Khi truy cập `http://127.0.0.1:8787/app/foo/foo/hello` thì trang web sẽ trả về `xin chào webman`
  
## Truy cập URL
Đường dẫn URL của ứng dụng plugin bắt đầu bằng `/app`, tiếp theo là plugin_identifier, sau đó là điều khiển cụ thể và phương pháp.
Ví dụ, đường dẫn URL của `plugin\foo\app\controller\UserController` là `http://127.0.0.1:8787/app/foo/user`
