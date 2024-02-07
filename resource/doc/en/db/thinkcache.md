## ThinkCache

### Installing ThinkCache  
`composer require -W webman/think-cache`

After installation, you need to restart (reload is ineffective)


> [webman/think-cache](https://www.workerman.net/plugin/15) is actually a plugin for automatically installing `toptink/think-cache`.

> **Note**
> toptink/think-cache does not support php8.1
  
### Configuration File

The configuration file is `config/thinkcache.php`

### Usage

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
### Think-Cache Documentation

[ThinkCache Documentation](https://github.com/top-think/think-cache)
