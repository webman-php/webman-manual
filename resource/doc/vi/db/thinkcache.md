## ThinkCache

### Cài đặt ThinkCache  
`composer require -W webman/think-cache`

Sau khi cài đặt cần restart để tải lại (reload không có hiệu lực)

> [webman/think-cache](https://www.workerman.net/plugin/15) thực tế là một plugin tự động cài đặt `toptink/think-cache`.

> **Chú ý**
> toptink/think-cache không hỗ trợ php8.1

### Tệp cấu hình

Tệp cấu hình là `config/thinkcache.php`

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

[Địa chỉ tài liệu ThinkCache](https://github.com/top-think/think-cache)
