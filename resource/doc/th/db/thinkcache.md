## ThinkCache

### ติดตั้ง ThinkCache
`composer require -W webman/think-cache`

หลังจากติดตั้งจะต้อง restart ใหม่ (reload ไม่ทำงาน)

> [webman/think-cache](https://www.workerman.net/plugin/15) จริง ๆ คือปลััดอัตโนมัติ `toptink/think-cache`.

> **โปรดทราบ**
> toptink/think-cache ไม่รองรับ PHP 8.1

### ไฟล์การกำหนดค่า

ไฟล์การกำหนดค่าอยู่ที่ `config/thinkcache.php`

### การใช้งาน

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
### เอกสารการใช้งาน Think-Cache

[ที่อยู่เอกสาร ThinkCache](https://github.com/top-think/think-cache)
