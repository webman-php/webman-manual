# Phar Packaging

Phar is a kind of packaging file in PHP similar to JAR. You can use Phar to package your webman project into a single Phar file for easy deployment.

**Very grateful to [fuzqing](https://github.com/fuzqing) for the PR.**

> **Note**
> You need to disable the `phar` configuration option in `php.ini`, by setting `phar.readonly = 0`.

## Install Command Line Tool
Run `composer require webman/console` to install the command line tool.

## Configuration Settings
Open the `config/plugin/webman/console/app.php` file and set `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`. This is used to exclude some unnecessary directories and files when packaging, to avoid excessive package size.

## Packaging
Execute the command `php webman phar:pack` in the root directory of the webman project. This will generate a `webman.phar` file in the `build` directory.

> Packaging related configurations are in `config/plugin/webman/console/app.php`.

## Start and Stop Commands
**Start**
`php webman.phar start` or `php webman.phar start -d`

**Stop**
`php webman.phar stop`

**Check Status**
`php webman.phar status`

**Check Connection Status**
`php webman.phar connections`

**Restart**
`php webman.phar restart` or `php webman.phar restart -d`

## Notes
* Running webman.phar will generate a `runtime` directory in the same directory as webman.phar, used for storing temporary files such as logs.

* If your project uses an `.env` file, you need to place the `.env` file in the same directory as webman.phar.

* If your business needs to upload files to the `public` directory, you need to separate the `public` directory and place it in the same directory as webman.phar. In this case, you need to configure `config/app.php`.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
The business can use the helper function `public_path()` to find the actual location of the public directory.

* webman.phar does not support custom processes on Windows.
