## திங்க்கேஷ்

### திங்க்கேஷ் நிறுவல்
`composer require -W webman/think-cache`

நிறுவிய பிறகு புதுசாப்டு (மீளோட் பெண்) செய்ய வேண்டும்

>[webman/think-cache](https://www.workerman.net/plugin/15) என்ற வெப்மான் / திங்க்கேஷ்  வார்த்தைகள்`toptink/think-cache`-ஐ தானாக்கம் செய்து , நிறைவேற்றுகின்றது.

> **குறிப்பு** toptink/think-cache php8.1 ஐ ஆதரிக்கவில்லை

### கட்டமை கோப்பு

கட்டமை கோப்பு இருக்கும்`config/thinkcache.php`

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

[https://github.com/top-think/think-cache](https://github.com/top-think/think-cache)
