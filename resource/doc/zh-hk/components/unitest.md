# 單元測試

## 安裝

```php
composer require --dev phpunit/phpunit
```

## 使用
新建檔案 `tests/TestConfig.php`，用於測試數據庫配置
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

## 執行
在項目根目錄執行 `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`

結果類似如下：
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
