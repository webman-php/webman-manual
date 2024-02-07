# 로그
webman은 [monolog/monolog](https://github.com/Seldaek/monolog)을 사용하여 로그를 처리합니다.

## 사용법
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('로그 테스트');
        return response('안녕하세요 인덱스');
    }
}
```

## 제공되는 메소드
```php
Log::log($level, $message, array $context = [])
Log::debug($message, array $context = [])
Log::info($message, array $context = [])
Log::notice($message, array $context = [])
Log::warning($message, array $context = [])
Log::error($message, array $context = [])
Log::critical($message, array $context = [])
Log::alert($message, array $context = [])
Log::emergency($message, array $context = [])
```
동등한 것은 다음과 같습니다.
```php
$log = Log::channel('default');
$log->log($level, $message, array $context = [])
$log->debug($message, array $context = [])
$log->info($message, array $context = [])
$log->notice($message, array $context = [])
$log->warning($message, array $context = [])
$log->error($message, array $context = [])
$log->critical($message, array $context = [])
$log->alert($message, array $context = [])
$log->emergency($message, array $context = [])
```

## 설정
```php
return [
    // 기본 로그 채널
    'default' => [
        // 기본 채널을 처리하는 핸들러, 여러 개 설정할 수 있음
        'handlers' => [
            [   
                // 핸들러 클래스 이름
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // 핸들러 클래스의 생성자 매개변수
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // 형식 관련
                'formatter' => [
                    // 형식 처리 클래스의 이름
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // 형식 처리 클래스의 생성자 매개변수
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## 다중 채널
monolog은 다중 채널을 지원하며, 기본적으로 `default` 채널을 사용합니다. `log2` 채널을 추가하려면 다음과 유사한 구성을 사용하십시오.
```php
return [
    // 기본 로그 채널
    'default' => [
        // 기본 채널을 처리하는 핸들러, 여러 개 설정할 수 있음
        'handlers' => [
            [   
                // 핸들러 클래스 이름
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // 핸들러 클래스의 생성자 매개변수
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // 형식 관련
                'formatter' => [
                    // 형식 처리 클래스의 이름
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // 형식 처리 클래스의 생성자 매개변수
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 채널
    'log2' => [
        // 기본 채널을 처리하는 핸들러, 여러 개 설정할 수 있음
        'handlers' => [
            [   
                // 핸들러 클래스 이름
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // 핸들러 클래스의 생성자 매개변수
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // 형식 관련
                'formatter' => [
                    // 형식 처리 클래스의 이름
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // 형식 처리 클래스의 생성자 매개변수
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2` 채널을 사용하는 방법은 다음과 같습니다.
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        $log = Log::channel('log2');
        $log->info('log2 테스트');
        return response('안녕하세요 인덱스');
    }
}
```
