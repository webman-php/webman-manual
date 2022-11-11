# pharpack

phar is a kind of packaging file in PHP similar to JAR, you can use phar to package your webman project into a single phar file for easy deployment。

**can also can be [fuzqing](https://github.com/fuzqing) 的PR.**

> **Note**
> This feature is only supported on linux systems
> Required for this featurewebman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5
> Need to turn off the `php.ini` Phar configuration option `phar.readonly = 0`

## Install command line tool
`composer require webman/console`

## pack
Execute commands at the root of a webman project `php webman phar:pack`
Generates a `webman.phar` file in the bulid directory。

> Packaging related configuration in `config/plugin/webman/console/app.php`

## Start/Stop related commands
**Start**
`php webman.phar start` 或 `php webman.phar start -d`

**Stop**
`php webman.phar stop`

**View Status**
`php webman.phar status`

**View connection status**
`php webman.phar connections`

**Reboot**
`php webman.phar restart` 或 `php webman.phar restart -d`

