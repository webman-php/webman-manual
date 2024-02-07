# Environment Requirements

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Create Project

```php
composer create-project workerman/webman
```

### 2. Run

Enter the webman directory

#### For Windows Users
Double-click `windows.bat` or run `php windows.php` to start

> **Note**
> If there is an error, it is likely that some functions are disabled. Refer to [Function Disable Check](others/disable-function-check.md) to remove the disable setting

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
> If there is an error, it is likely that some functions are disabled. Refer to [Function Disable Check](others/disable-function-check.md) to remove the disable setting

### 3. Access

Access `http://ip address:8787` in a web browser.
