# Phar Packing

Phar is a packaging file in PHP similar to JAR. You can use Phar to package your webman project into a single Phar file for easy deployment.

**Many thanks to [fuzqing](https://github.com/fuzqing) for the PR.**

> **Note**
> It is necessary to disable the `phar` configuration option in `php.ini`, which means setting `phar.readonly = 0`.

## Install Command Line Tool
Run `composer require webman/console` to install the command line tool.

## Configuration
Open the file `config/plugin/webman/console/app.php` and set `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` to exclude some unnecessary directories and files during packaging, avoiding excessive package size.

## Packing
Execute the command `php webman phar:pack` in the root directory of the webman project. This will generate a `webman.phar` file in the build directory.

> Packing related configurations are in `config/plugin/webman/console/app.php`.

## Start/Stop Commands
**Start**
Run `php webman.phar start` or `php webman.phar start -d`.

**Stop**
Run `php webman.phar stop`.

**Check Status**
Run `php webman.phar status`.

**Check Connection Status**
Run `php webman.phar connections`.

**Restart**
Run `php webman.phar restart` or `php webman.phar restart -d`.

## Explanation
* Running webman.phar will generate a runtime directory in the same location as webman.phar, used to store temporary files such as logs.

* If your project uses a .env file, it should be placed in the same directory as webman.phar.

* If your business needs to upload files to the public directory, you should separate the public directory and place it in the same location as webman.phar. In this case, you need to configure `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Your business can use the helper function `public_path()` to find the actual location of the public directory.

* webman.phar does not support enabling custom processes on Windows.