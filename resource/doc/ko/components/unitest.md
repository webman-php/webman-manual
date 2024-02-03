# 단위 테스트

## 설치

```php
composer require --dev phpunit/phpunit
```

## 사용
테스트 데이터베이스 구성을 위해 `tests/TestConfig.php` 파일을 새로 만듭니다.
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

다음과 같은 결과가 나옵니다.
```
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 00:00.010, Memory: 6.00 MB

OK (1 test, 5 assertions)
```
