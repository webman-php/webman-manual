# Cache

在webman中使用 [symfony/cache](https://github.com/symfony/cache)

使用`symfony/cache`之前必须先给`php-cli`安装redis扩展。

## 安装
```php
composer require vlucas/phpdotenv ^5.1.0
composer require illuminate/redis ^8.2.0
composer require symfony/cache ^5.2
```

- 新建 support/bootstrap/Cache.php
```php
<?php
namespace support\bootstrap;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Webman\Bootstrap;

/**
 * Class Cache
 * @package support\bootstrap
 *
 * Strings methods
 * @method static mixed get($key, $default = null)
 * @method static bool set($key, $value, $ttl = null)
 * @method static bool delete($key)
 * @method static bool clear()
 * @method static iterable getMultiple($keys, $default = null)
 * @method static bool setMultiple($values, $ttl = null)
 * @method static bool deleteMultiple($keys)
 * @method static bool has($key)
 */
class Cache implements Bootstrap
{
    /**
     * @var Psr16Cache
     */
    public static $_instance = null;

    /**
     * @param \Workerman\Worker $worker
     * @return mixed|void
     */
    public static function start($worker)
    {
        $adapter = new RedisAdapter(Redis::connection()->client());
        self::$_instance = new Psr16Cache($adapter);
    }

    /**
     * @return Psr16Cache|null
     */
    public static function instance()
    {
        return static::$_instance ?? null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}
```

- 进程启动配置
打开config/bootstrap.php，加入如下配置：
```php
return [
    // 这里省略了其它配置 ...
    support\bootstrap\Cache::class,
];
```


## Redis配置
redis配置文件在`config/redis.php`
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

## 示例
```php
<?php
namespace app\controller;

use support\Request;
use support\bootstrap\Cache;

class User
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```
