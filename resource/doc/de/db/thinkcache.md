## ThinkCache

### ThinkCache installieren
`composer require -W webman/think-cache`

Nach der Installation muss neu gestartet werden (reload ist ungültig).

> [webman/think-cache](https://www.workerman.net/plugin/15) ist eigentlich ein Plugin, das automatisch `toptink/think-cache` installiert.

> **Achtung**
> toptink/think-cache unterstützt kein PHP 8.1

### Konfigurationsdatei
Die Konfigurationsdatei befindet sich unter `config/thinkcache.php`.

### Verwendung
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
### Think-Cache Verwendungsanleitung
[ThinkCache Dokumentation](https://github.com/top-think/think-cache)
