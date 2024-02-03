# Automatic Loading

## Loading Files Following the PSR-0 Specification Using Composer
webman follows the `PSR-4` automatic loading specification. If your business needs to load code libraries following the `PSR-0` specification, refer to the following steps.

- Create an `extend` directory to store code libraries that follow the `PSR-0` specification.
- Edit `composer.json` and add the following content under `autoload`:

```js
"psr-0" : {
    "": "extend/"
}
```
The final result should be similar to
![](../../assets/img/psr0.png)

- Run `composer dumpautoload`.
- Run `php start.php restart` to restart webman (Note: restart is required for the changes to take effect).

## Loading Specific Files Using Composer

- Edit `composer.json` and add the files to be loaded under `autoload.files`:

```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Run `composer dumpautoload`.
- Run `php start.php restart` to restart webman (Note: restart is required for the changes to take effect).

> **Tip**
> Files configured in `autoload.files` in composer.json are loaded before webman starts. Files loaded using the framework's `config/autoload.php` are loaded after webman starts. Changes to the files loaded via `autoload.files` in composer.json require a restart to take effect, while changes to the files loaded using `config/autoload.php` support hot reloading and can take effect with a reload.

## Loading Specific Files Using the Framework
Some files may not comply with the PSR specification and cannot be automatically loaded. In such cases, we can load these files through the configuration `config/autoload.php`, for example:

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
> **Tip**  
> In the `autoload.php`, we see that it is set to load two files, `support/Request.php` and `support/Response.php`. This is because there are also two identical files under `vendor/workerman/webman-framework/src/support/`. By using `autoload.php`, we prioritize loading `support/Request.php` and `support/Response.php` from the project root directory, allowing us to customize the content of these two files without modifying the files in the `vendor`. If you do not need to customize them, you can ignore these two configurations.