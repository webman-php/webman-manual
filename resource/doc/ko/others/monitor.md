# 프로세스 모니터링
webman은 내장된 모니터 프로세스를 가지고 있으며, 두 가지 기능을 지원합니다.
1. 파일 업데이트를 모니터링하고 새로운 비즈니스 코드를 자동으로 다시로드합니다(보통 개발 중에 사용됨).
2. 모든 프로세스의 메모리 사용량을 모니터링하고, 특정 프로세스의 메모리 사용량이 `php.ini`에 정의된 `memory_limit` 제한을 거의 초과하는 경우 자동으로 해당 프로세스를 안전하게 다시 시작합니다(비즈니스에 영향을 주지 않음).

### 모니터 설정
구성 파일 `config/process.php`에 있는 `monitor` 구성
```php

global $argv;

return [
    // 파일 업데이트 검출 및 자동 다시로드
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // 이러한 디렉토리를 모니터링
            'monitorDir' => array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // 이러한 접미사를 가진 파일은 모니터링됨
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
```
`monitorDir`은 어떤 디렉토리의 업데이트를 모니터링할지를 구성합니다(너무 많은 파일이 있는 디렉토리를 모니터링하는 것은 권장되지 않습니다).
`monitorExtensions`은 `monitorDir` 디렉토리에 어떤 파일 확장자가 모니터링되어야 하는지를 구성합니다.
`options.enable_file_monitor`는 `true`로 설정될 경우, 파일 업데이트 모니터링이 활성화됩니다(리눅스 시스템에서 디버그 모드로 실행 중인 경우 기본적으로 파일 모니터링이 활성화됩니다).
`options.enable_memory_monitor`는 `true`로 설정될 경우, 메모리 사용량 모니터링이 활성화됩니다(메모리 사용량 모니터링은 윈도우 시스템에서는 지원되지 않습니다).

> **참고**
> 윈도우 시스템에서는 `windows.bat` 또는 `php windows.php`를 실행할 때 파일 업데이트 모니터링이 활성화됩니다.
