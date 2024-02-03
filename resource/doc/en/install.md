# Environment Requirements

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Project Creation

```php
composer create-project workerman/webman
```

### 2. Running

Navigate to the webman directory

#### For Windows Users
Double click on `windows.bat` or run `php windows.php` to start.

> **Note**
> If there is an error, it is likely that some functions are disabled. Refer to [function disable check](others/disable-function-check.md) to enable them.

#### For Linux Users
Run in `debug` mode (for development and debugging)

```php
php start.php start
```

Run in `daemon` mode (for production environment)

```php
php start.php start -d
```

> **Note**
> If there is an error, it is likely that some functions are disabled. Refer to [function disable check](others/disable-function-check.md) to enable them.

### 3. Access

Access via a web browser at `http://ip address:8787`.