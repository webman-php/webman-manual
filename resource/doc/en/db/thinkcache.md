## ThinkCache

### Installing ThinkCache
Run the following command to install ThinkCache using Composer:
```sh
composer require -W webman/think-cache
```
After installation, a restart is required (reload is ineffective).

> [webman/think-cache](https://www.workerman.net/plugin/15) is actually a plugin for automating the installation of `toptink/think-cache`.

> **Note**
> toptink/think-cache does not support PHP 8.1

### Configuration File
The configuration file is located at `config/thinkcache.php`.

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
For more information on using ThinkCache, please refer to the [ThinkCache documentation](https://github.com/top-think/think-cache).