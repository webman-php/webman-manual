# Process Monitoring
Webman comes with a built-in monitor process that supports two functions:

1. Monitor file updates and automatically reload the new business code (generally used during development).
2. Monitor memory usage of all processes, and automatically restart a process if its memory usage is about to exceed the `memory_limit` limit in the `php.ini` (without affecting the business).

### Monitoring Configuration
The configuration file `config/process.php` has the `monitor` configuration.

```php
global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([    // Which directories files need to be monitored
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
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Whether to enable file monitoring
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Whether to enable memory monitoring
            ]
        ]
    ]
];
```

`monitorDir` is used to configure which directories to monitor for updates (it is not recommended to monitor too many files in the monitored directory).
`monitorExtensions` is used to configure which file suffixes in the `monitorDir` directory should be monitored.
When `options.enable_file_monitor` is set to `true`, file update monitoring is enabled (by default, it is enabled for running in debug mode on Linux systems).
When `options.enable_memory_monitor` is set to `true`, memory usage monitoring is enabled (memory usage monitoring is not supported on Windows systems).

> **Note**
> On a Windows system, file update monitoring can only be enabled when running `windows.bat` or `php windows.php`.
