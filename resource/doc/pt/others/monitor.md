# Process Monitoring
webman comes with a built-in monitor process that supports two functions:

1. Monitor file updates and automatically reload new business code (usually used during development)
2. Monitor all processes occupying memory, and if a process's memory usage is about to exceed the `memory_limit` limit in `php.ini`, it will automatically safely restart the process (without affecting the business).

### Monitoring Configuration
The configuration is located in the file `config/process.php` under the `monitor` setting:

```php
global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([
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
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
```

The `monitorDir` is used to configure which directories to monitor for updates (files in monitored directories should not be too many).
The `monitorExtensions` is used to configure which file suffixes in the `monitorDir` directory should be monitored.
The `options.enable_file_monitor` is set to `true` to enable file update monitoring (by default, file monitoring is enabled when running in debug mode under Linux).
The `options.enable_memory_monitor` is set to `true` to enable memory usage monitoring (memory usage monitoring is not supported on Windows).

> **Note**
> In Windows, file update monitoring can only be enabled when running `windows.bat` or `php windows.php`.
