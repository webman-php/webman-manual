## تفكير المخبأ

### تثبيت تفكير المخبأ
`composer require -W webman/think-cache`

بعد التثبيت ، يجب إعادة تشغيل (لا يعمل إعادة التحميل)


>[webman/think-cache](https://www.workerman.net/plugin/15) هو في الواقع إضافة لتثبيت تلقائي `toptink/think-cache`.

> **ملاحظة**
> toptink/think-cache لا يدعم PHP 8.1
  
### ملف التكوين

ملف التكوين هو `config/thinkcache.php`

### الاستخدام

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

### وثائق استخدام Think-Cache

[عنوان وثائق ThinkCache](https://github.com/top-think/think-cache)
