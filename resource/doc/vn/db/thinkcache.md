## ThinkCache

### Cài đặt ThinkCache  
`composer require -W webman/think-cache`

Sau khi cài đặt, bạn cần restart để khởi động lại (reload không có tác dụng)

> [webman/think-cache](https://www.workerman.net/plugin/15) thực ra là một plugin tự động cài đặt `toptink/think-cache`.

> **Lưu ý**
> toptink/think-cache không hỗ trợ php8.1
  
### Tệp cấu hình

Tệp cấu hình nằm tại `config/thinkcache.php`

### Sử dụng

```php
<?php
namespace app\controller;
  
use support\Request;
use think\facade\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

### Tài liệu sử dụng Think-Cache

[Link đến tài liệu ThinkCache](https://github.com/top-think/think-cache)
