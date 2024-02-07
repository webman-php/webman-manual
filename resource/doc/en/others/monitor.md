# Process Monitoring

webman comes with a monitor process to support two functions:

1. Detects file updates and automatically reloads new business code (usually used during development).
2. Monitors memory consumption of all processes. If the memory consumption of a process is about to exceed the `memory_limit` limit set in `php.ini`, it will automatically safely restart the process without affecting the business.

### Monitoring Configuration

The configuration can be found in the file `config/process.php` under the `monitor` setting:
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

The `monitorDir` is used to configure which directories to monitor for updates (it is not advisable to monitor too many files in the monitored directories).  
The `monitorExtensions` is used to configure which file suffixes in the `monitorDir` directories should be monitored.  
The value of `options.enable_file_monitor` as `true` enables file update monitoring (by default, in Linux systems, file monitoring is enabled when running in debug mode without specifying the `-d` flag).  
The value of `options.enable_memory_monitor` as `true` enables memory usage monitoring (memory usage monitoring is not supported in Windows systems).

> **Note**
> In Windows systems, file update monitoring is only enabled when running `windows.bat` or `php windows.php`.
