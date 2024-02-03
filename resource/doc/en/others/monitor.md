# Process Monitoring
webman comes with a built-in monitoring process that supports two functions:
1. Monitor file updates and automatically reload new business code (usually used during development).
2. Monitor the memory usage of all processes, and automatically restart a process if its memory usage is about to exceed the `memory_limit` limit in the `php.ini` (without affecting the business).

### Monitoring Configuration
The monitoring configuration is located in the file `config/process.php` under the `monitor` configuration.
```php
global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([    // Directories whose files need to be monitored
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
The `monitorDir` is used to configure which directories to monitor for updates (the number of files being monitored in each directory should not be too large).
The `monitorExtensions` is used to configure which file suffixes within the `monitorDir` directory should be monitored.
When `options.enable_file_monitor` is set to `true`, file update monitoring is enabled (by default, it is enabled on Linux systems running in debug mode).
When `options.enable_memory_monitor` is set to `true`, memory usage monitoring is enabled (memory usage monitoring is not supported on Windows systems).

> **Note**
> On Windows systems, file update monitoring is only enabled when running `windows.bat` or `php windows.php`.