## திங்க்கேஷ்

### திங்க்கேஷ் நிறுவல்

`composer require -W webman/think-cache`

நிறுவிய பின் மீள்பாதிகை(reload) காத்திருக்கிறது (மீள்பாதிகை உபயோகமில்லை)

> [வெப்மான்/திங்க்கேஷ்](https://www.workerman.net/plugin/15) என்பது உண்மையில்  `toptink/think-cache` ஐ தானாக நிறுவுகின்றது.

> **குறிப்பு**
> toptink/think-cache PHP 8.1 ஐ ஆதரிக்கவில்லை

### கட்டமை கோப்பு

கட்டமை கோப்பு `config/thinkcache.php` ஆகும்

### பயன்பாடு

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

### திங்க்-கேஷ் பயன்பாடு ஆவணம்

[ThinkCache ஆவணம் இடம்](https://github.com/top-think/think-cache)
