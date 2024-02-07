# 단위 테스트

## 설치
```php
composer require --dev phpunit/phpunit
```

## 사용
새 파일 `tests/TestConfig.php`을 만들어 데이터베이스 구성을 테스트하는 데 사용합니다.
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

## 실행
프로젝트 루트 디렉토리에서 `./vendor/bin/phpunit --bootstrap support/bootstrap.php tests/TestConfig.php`을 실행합니다.

다음은 결과입니다:
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
