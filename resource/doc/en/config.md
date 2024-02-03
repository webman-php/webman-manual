# Configuration File

## Location
The configuration file of webman is located in the `config/` directory. In the project, you can use the `config()` function to retrieve the corresponding configuration.

## Retrieving Configuration

Retrieve all configurations
```php
config();
```

Retrieve all configurations in `config/app.php`
```php
config('app');
```

Retrieve the `debug` configuration in `config/app.php`
```php
config('app.debug');
```

If the configuration is an array, you can use `.` to retrieve the value of elements inside the array, for example
```php
config('file.key1.key2');
```

## Default Value
```php
config($key, $default);
```
The `config()` function can be used to pass a default value as the second parameter. If the configuration does not exist, it will return the default value. If the configuration does not exist and no default value is set, it will return `null`.

## Custom Configuration
Developers can add their own configuration files in the `config/` directory, for example

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Usage when retrieving the configuration**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Modifying Configuration
webman does not support dynamically modifying the configuration. All configurations must be manually changed in the corresponding configuration files, and then reloaded or restarted.

> **Note**
> The server configuration in `config/server.php` and the process configuration in `config/process.php` do not support reloading. A restart is required for the changes to take effect.