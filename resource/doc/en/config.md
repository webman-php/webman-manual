# Configuration File

## Location
The configuration file of webman is located in the `config/` directory. You can use the `config()` function to access the corresponding configurations in your project.

## Accessing Configurations

Get all configurations:
```php
config();
```

Get all configurations in `config/app.php`:
```php
config('app');
```

Get the `debug` configuration in `config/app.php`:
```php
config('app.debug');
```

If the configuration is an array, you can use `.` to access the values of the elements inside the array. For example:
```php
config('file.key1.key2');
```

## Default Value
```php
config($key, $default);
```
The `config` function takes a second parameter to pass in the default value. If the configuration doesn't exist, it will return the default value. If no default value is set and the configuration doesn't exist, it will return `null`.

## Custom Configuration
Developers can add their own configuration files in the `config/` directory. For example:

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Usage when accessing configurations**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Modifying Configurations
Webman does not support dynamically modifying configurations. All configurations must be manually modified in the corresponding configuration files and then reload or restart.

> **Note**
> The server configuration `config/server.php` and process configuration `config/process.php` do not support reload. You need to restart for the changes to take effect.
