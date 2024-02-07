# キャッシュ

webmanでは、デフォルトで [symfony/cache](https://github.com/symfony/cache) をキャッシュコンポーネントとして使用しています。

> `symfony/cache`を使用する前に、`php-cli`にRedis拡張機能をインストールする必要があります。

## インストール
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

インストール後は、再起動が必要です（reloadでは無効です）。

## Redisの設定
Redisの設定ファイルは `config/redis.php` にあります。
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## 例
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

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

> **注意**
> キーには他のRedisを使用しているビジネスとの衝突を避けるために、できるだけ接頭辞を付けることをお勧めします。

## 他のキャッシュコンポーネントの使用

[ThinkCache](https://github.com/top-think/think-cache) コンポーネントの使用については、[その他のデータベース](others.md#ThinkCache) を参照してください。
