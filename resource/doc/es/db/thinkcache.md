## ThinkCache

### Instalación de ThinkCache
`composer require -W webman/think-cache`

Después de la instalación, es necesario reiniciar (reload no es efectivo).

> [webman/think-cache](https://www.workerman.net/plugin/15) es en realidad un complemento para instalar automáticamente `toptink/think-cache`.

> **Atención**:
> toptink/think-cache no es compatible con php8.1

### Archivo de configuración

El archivo de configuración se encuentra en `config/thinkcache.php`

### Uso

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

### Documentación de uso de Think-Cache

[ThinkCache documentación](https://github.com/top-think/think-cache)
