# 監控進程
webman自帶一個monitor監控進程，它支援兩個功能
1. 監控文件更新並自動reload載入新的業務代碼(一般在開發時使用)
2. 監控所有進程佔用內存，如果某個進程佔用內存即將超過`php.ini`中`memory_limit`限制則自動安全重啟該進程(不影響業務)

## 監控配置
配置文件 `config/process.php` 中`monitor`配置
```php

global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([    // 哪些目錄下的檔案需要被監控
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // 是否開啟文件監控
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // 是否開啟內存監控
            ]
        ]
    ]
];
```
`monitorDir`用來配置監控哪些目錄的更新(監控目錄的檔案不宜過多)。
`monitorExtensions`用來配置`monitorDir`目錄裡哪些檔案後綴應該被監控。
`options.enable_file_monitor`值為`true`時，則開啟文件更新監控(linux系統下以debug方式運行默認開啟文件監控)。
`options.enable_memory_monitor`值為`true`時，則開啟內存佔用監控(內存佔用監控不支援windows系統)。

> **提示**
> windows系統下當需要運行`windows.bat` 或者 `php windows.php` 時才能開啟文件更新監控。
