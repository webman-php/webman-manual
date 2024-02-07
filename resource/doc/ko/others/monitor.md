# 프로세스 모니터링
webman에는 내장된 monitor 프로세스 모니터링이 있으며, 두 가지 기능을 지원합니다.
1. 파일 업데이트 모니터링 및 자동으로 새 비즈니스 코드를 리로드합니다(일반적으로 개발 중에 사용됨).
2. 모든 프로세스의 메모리 사용량을 모니터링하며, 특정 프로세스의 메모리 사용량이 `php.ini`의 `memory_limit` 제한을 거의 초과하는 경우 해당 프로세스를 안전하게 다시 시작합니다(비즈니스에는 영향을 주지 않음).

## 모니터링 구성
구성 파일 `config/process.php`에 `monitor` 구성이 있습니다.
```php

global $argv;

return [
    // 파일 업데이트 감지 및 자동 리로드
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // 이 디렉터리를 모니터링합니다
            'monitorDir' => array_merge([    // 감시해야 할 디렉터리
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // 다음 접미사가 있는 파일을 모니터링합니다
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // 파일 모니터링 활성화 여부
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // 메모리 모니터링 활성화 여부
            ]
        ]
    ]
];
```
`monitorDir`은 어떤 디렉터리의 업데이트를 모니터링할지 구성하는 데 사용됩니다(너무 많은 파일을 모니터링하는 것은 좋지 않음).
`monitorExtensions`은 `monitorDir` 디렉터리에서 어떤 파일 접미사를 모니터링할지 구성하는 데 사용됩니다.
`options.enable_file_monitor`는 `true`인 경우 파일 업데이트 모니터링을 활성화하고(linux 시스템에서 디버그 모드로 실행하는 경우 기본적으로 파일 모니터링이 활성화됩니다).
`options.enable_memory_monitor`는 `true`인 경우 메모리 사용량 모니터링을 활성화하며(메모리 사용량 모니터링은 Windows 시스템에서 지원되지 않음).

> **팁**
> Windows 시스템에서는 `windows.bat` 또는 `php windows.php`를 실행해야 파일 업데이트 모니터링을 활성화할 수 있습니다.
