# 单元测试

  
## 安装
 
```php
composer require --dev phpunit/phpunit
```
  
## 创建bootstrap

创建tests/bootstrap.php

```php
<?php
use Dotenv\Dotenv;
use Webman\Bootstrap;
use Webman\Config;

require_once __DIR__ . '/../vendor/autoload.php';
if (class_exists('Dotenv\Dotenv')) {
    if (method_exists('Dotenv\Dotenv', 'createUnsafeImmutable')) {
        Dotenv::createUnsafeImmutable(base_path())->load();
    } else {
        Dotenv::createMutable(base_path())->load();
    }
}
Config::load(config_path(), ['route', 'container']);
if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}
foreach (config('autoload.files', []) as $file) {
    include_once $file;
}
foreach (config('bootstrap', []) as $class_name) {
    /** @var Bootstrap $class_name */
    $class_name::start(null);
}
```
  
## 使用
新建文件 `tests/TestConfig.php`，用于测试数据库配置
```php
<?php
use PHPUnit\Framework\TestCase;

class TestConfig extends TestCase
{
    public function testAppConfig()
    {
        $config = config('app');
        self::assertIsArray($config);
        self::assertArrayHasKey('debug', $config);
        self::assertIsBool($config['debug']);
        self::assertArrayHasKey('default_timezone', $config);
        self::assertIsString($config['default_timezone']);
    }
}
```
  
## 运行

项目更目录里运行 `./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/TestConfig.php`

结果类似如下：
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
