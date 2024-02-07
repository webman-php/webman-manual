## ThinkCache

### ThinkCache Kurulumu
`composer require -W webman/think-cache`

Kurulumdan sonra yeniden başlatma gereklidir (reload işlevsiz).

> [webman/think-cache](https://www.workerman.net/plugin/15) aslında `toptink/think-cache`'i otomatik olarak yükleyen bir eklentidir.

> **Dikkat**
> toptink/think-cache PHP8.1'i desteklememektedir.

### Yapılandırma Dosyası

Yapılandırma dosyası `config/thinkcache.php`'dir.

### Kullanım

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

### Think-Cache Kullanım Kılavuzu

[ThinkCache belgeleri adresi](https://github.com/top-think/think-cache)
