# ユニットテスト

## インストール

```php
composer require --dev phpunit/phpunit
```

## 使用法
`tests/TestConfig.php`という新しいファイルを作成して、データベースの設定をテストします。

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

## 実行

プロジェクトのルートディレクトリで次のコマンドを実行します： `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`。

次のような結果が表示されます：

```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
