# Autoload

## Use composer to load PSR-0 specification files
webmanFollows the `PSR-4` autoloading specification. If your business needs to load the `PSR-0` specification code base, refer to the following actions。

- New `extend` directory user to store the `PSR-0` specification code base
- Edit `composer.json` and add the following under `autoload`

```js
"psr-0" : {
    "": "extend/"
}
```
The end result is similar
![](../../assets/img/psr0.png)

- Execution `composer dumpautoload`
- Run `php start.php restart` to restart webman (note that it must be restarted for this to work)) 

## Load certain files with composer

- Edit `composer.json` and add the files to be loaded under `autoload.files`
```
"files": [
  "./support/helpers.php",
  "./app/helpers.php"
]
```

- Execution `composer dumpautoload`
- Run `php start.php restart` to restart webman (note that it must be restarted for this to work)) 

> **hint**
> composer.json in the `autoload.files` configuration file is loaded before webman starts. And the files loaded using the framework `config/autoload.php` are loaded after webman is started。
> composer.json in the `autoload.files` loaded file changes must be restarted to take effect after the reload does not take effect. The files loaded using the framework `config/autoload.php` support hotloading, and reload will take effect after the change.。


## Load some files using the framework
Some files may not be SPR compliant and cannot be loaded automatically, we can load these files by configuring `config/autoload.php`, for example：
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **hint**
 > We see that `autoload.php` is set to load the `support/Request.php` `support/Response.php` files, this is because there are also two files under `vendor/workerman/webman-framework/src/support/` We load `support/Request.php` `support/Response.php` in the root of the project via `autoload.php`, which allows us to customize the content of these two files without modifying the files in `vendor`. If you don't need to customize them, then you can ignore these two configuration 。
