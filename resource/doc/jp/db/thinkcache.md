## ThinkCache

### ThinkCacheのインストール
`composer require -W webman/think-cache`

インストール後、再起動（reloadは無効）

>[webman/think-cache](https://www.workerman.net/plugin/15) は実際には自動的に`toptink/think-cache`をインストールするプラグインです。

> **注意**
> toptink/think-cacheはPHP8.1をサポートしていません

### 設定ファイル

設定ファイルは`config/thinkcache.php`です

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

### Think-Cache使用ドキュメント

[ThinkCacheのドキュメント](https://github.com/top-think/think-cache)
