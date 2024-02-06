## ThinkCache

### Установка ThinkCache
`composer require -W webman/think-cache`

После установки необходимо перезагрузить (reload is not valid)


> [webman/think-cache](https://www.workerman.net/plugin/15) фактически является плагином, автоматически устанавливающим `toptink/think-cache`.

> **Примечание**
> toptink/think-cache не поддерживает php8.1

### Файл конфигурации

Файл конфигурации находится в `config/thinkcache.php`

### Использование

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

### Документация по использованию Think-Cache

[Ссылка на документацию ThinkCache](https://github.com/top-think/think-cache)
