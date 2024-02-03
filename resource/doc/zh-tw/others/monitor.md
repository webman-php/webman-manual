# 監控進程
webman自帶一個 monitor 監控進程，支援兩個功能：
1. 監控檔案更新並自動重新載入新的業務程式碼（一般在開發時使用）。
2. 監控所有進程佔用記憶體，如果某個進程佔用記憶體即將超過 `php.ini` 中的 `memory_limit` 限制，則會自動安全重啟該進程（不影響業務）。

### 監控設定
在配置文件 `config/process.php` 中的 `monitor` 配置如下：
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
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // 是否開啟檔案監控
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // 是否開啟記憶體監控
            ]
        ]
    ]
];
```
`monitorDir` 用來設置監控哪些目錄的更新（監控目錄的檔案不宜過多）。
`monitorExtensions` 用來設置 `monitorDir` 目錄裡哪些檔案後綴應該被監控。
`options.enable_file_monitor` 值為 `true` 時，則開啟檔案更新監控（在 Linux 系統下以 debug 方式執行時默認開啟檔案監控）。
`options.enable_memory_monitor` 值為 `true` 時，則開啟記憶體佔用監控（記憶體佔用監控不支援 Windows 系統）。

> **提示：**
> Windows 系統下，當需要運行 `windows.bat` 或者 `php windows.php` 時才能開啟檔案更新監控。