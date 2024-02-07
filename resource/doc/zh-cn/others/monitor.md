# 监控进程
webman自带一个monitor监控进程，它支持两个功能
1. 监控文件更新并自动reload载入新的业务代码(一般在开发时使用)
2. 监控所有进程占用内存，如果某个进程占用内存即将超过`php.ini`中`memory_limit`限制则自动安全重启该进程(不影响业务)

## 监控配置
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
            'monitorDir' => array_merge([    // 哪些目录下的文件需要被监控
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
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // 是否开启文件监控
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // 是否开启内存监控
            ]
        ]
    ]
];
```
`monitorDir`用来配置监控哪些目录的更新(监控目录的文件不宜过多)。
`monitorExtensions`用来配置`monitorDir`目录里哪些文件后缀应该被监控。
`options.enable_file_monitor`值为`true`时，则开启文件更新监控(linux系统下以debug方式运行默认开启文件监控)。
`options.enable_memory_monitor`值为`true`时，则开启内存占用监控(内存占用监控不支持windows系统)。

> **提示**
> windows系统下当需要运行`windows.bat` 或者 `php windows.php` 时才能开启文件更新监控。




