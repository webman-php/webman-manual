# Autoloading

## Loading files based on the PSR-0 specification using Composer
webman follows the `PSR-4` autoloading specification. If your project needs to load libraries based on the `PSR-0` specification, follow these steps:

- Create a `extend` directory to store the libraries that follow the `PSR-0` specification.
- Edit the `composer.json` file and add the following code under `autoload`:

```json
"psr-0" : {
    "": "extend/"
}
```
The final result should look like this:
![](../../assets/img/psr0.png)

- Run `composer dumpautoload`.
- Restart webman by running `php start.php restart` (Note: you must restart for the changes to take effect).

## Loading specific files using Composer

- Edit the `composer.json` file and add the files you want to load under `autoload.files`:
```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Run `composer dumpautoload`.
- Restart webman by running `php start.php restart` (Note: you must restart for the changes to take effect).

> **Note**
> Files specified in the `autoload.files` configuration in composer.json will be loaded before webman starts. On the other hand, files loaded using the `config/autoload.php` provided by the framework are loaded after webman starts.
> Changes made to the files specified in `autoload.files` in composer.json will require a restart to take effect. Reloading will not work. However, files loaded using the `config/autoload.php` provided by the framework support hot reloading, and changes will take effect upon reloading.

## Loading specific files using the framework
Sometimes, certain files may not conform to the PSR specification and thus cannot be loaded automatically. In such cases, we can use the `config/autoload.php` file to load these files. For example:

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **Note**
 > In the `autoload.php` file, we see that `support/Request.php` and `support/Response.php` are loaded. This is because there are also two files with the same names in `vendor/workerman/webman-framework/src/support/`. By loading the files specified in `autoload.php`, we give priority to the files in the project's root directory, allowing us to customize the content of these two files without modifying the files in `vendor`. If you don't need to customize them, you can ignore these two configurations.
