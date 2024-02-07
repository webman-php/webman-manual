## ThinkCache

### Installazione di ThinkCache  
`composer require -W webman/think-cache`

Dopo l'installazione è necessario restartare (reload non è valido)

> [webman/think-cache](https://www.workerman.net/plugin/15) è effettivamente un plugin per installare automaticamente `toptink/think-cache`.

> **Nota**
> toptink/think-cache non supporta php8.1
  
### File di configurazione

Il file di configurazione si trova in `config/thinkcache.php`

### Utilizzo

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

### Documentazione sull'uso di Think-Cache

[Documentazione di ThinkCache](https://github.com/top-think/think-cache)
