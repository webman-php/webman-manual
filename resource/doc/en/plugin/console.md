# webman/console Command Line Plugin

`webman/console` Based on `symfony/console`

> Plugin Requiredwebman>=1.2.2 webman-framework>=1.2.1

## Install
 
```sh
composer require webman/console
```

## Supported Commands
**Use methods**  
`php webman Command` or `php webman command`。
For example `php webman version` or `php webman version`

## Supported Commands
### version
**print webman version number**

### route:list
**Print current routing configuration**

### make:controller
**Create a controller file** 
For example `php webman make:controller admin` will create a `app/controller/AdminController.php`
For example `php webman make:controller api/user` will create a  `app/api/controller/UserController.php`

### make:model
**Create a model file**
For example `php webman make:model admin` will create a  `app/model/Admin.php`
For example `php webman make:model api/user` will create a  `app/api/model/User.php`

### make:middleware
**Create a middleware file**
For example `php webman make:middleware Auth` will create a  `app/middleware/Auth.php`

### make:command
**Create custom command file**
For example `php webman make:command db:config` will create a  `app\command\DbConfigCommand.php`

### plugin:create
**Create a base plugin**
Example `php webman plugin:create --name=foo/admin` will create`config/plugin/foo/admin` 和 `vendor/foo/admin` 两个目录
See[actually get](/doc/webman/plugin/create.html)

### plugin:export
**Export base plugin**
Example `php webman plugin:export --name=foo/admin` 
See[actually get](/doc/webman/plugin/create.html)

### plugin:create
**Export Application Plugin**
Example `php webman plugin:export shop`
See[support based on](/doc/webman/plugin/app.html)

### phar:pack
**Package the webman project into a phar file**
See[pharpackaged](/doc/webman/others/phar.html)
> Required for this featurewebman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Custom command
Users can define their own commands, for example the following is the command to print the database configuration

* Execution `php webman make:command config:mysql`
* Open `app/command/ConfigMySQLCommand.php` and change it to the following

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = 'Show current MySQL server configuration';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQLThe configuration information is as follows：');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'true' : 'false';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```
  
## test

Command Line Run `php webman config:mysql`

The result is similar to the following：
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## More information reference
http://www.symfonychina.com/doc/current/components/console.html
