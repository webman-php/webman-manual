## ThinkCache

### ThinkCacheのインストール  
`composer require -W webman/think-cache`

インストール後は、再起動が必要です（reloadでは無効）


> [webman/think-cache](https://www.workerman.net/plugin/15) は実際には、`toptink/think-cache`を自動的にインストールするプラグインです。

> **注意**
> toptink/think-cache はPHP8.1をサポートしていません。

### 設定ファイル

設定ファイルは `config/thinkcache.php` です。

### 使用方法

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
### Think-Cacheの使用方法

[ThinkCacheのドキュメント](https://github.com/top-think/think-cache)
