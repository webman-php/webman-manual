# webman/console Command Line Plugin

`webman/console` is based on `symfony/console`

> The plugin requires webman>=1.2.2 webman-framework>=1.2.1

## Installation
 
```sh
composer require webman/console
```

## Supported Commands
**Usage**  
`php webman command` or `php webman command`
For example, `php webman version` or `php webman version`

### version
**Print the webman version number**

### route:list
**Print the current route configuration**

### make:controller
**Create a controller file** 
For example, `php webman make:controller admin` will create `app/controller/AdminController.php`
For example, `php webman make:controller api/user` will create `app/api/controller/UserController.php`

### make:model
**Create a model file**
For example, `php webman make:model admin` will create `app/model/Admin.php`
For example, `php webman make:model api/user` will create `app/api/model/User.php`

### make:middleware
**Create a middleware file**
For example, `php webman make:middleware Auth` will create `app/middleware/Auth.php`

### make:command
**Create a custom command file**
For example, `php webman make:command db:config` will create a `app\command\DbConfigCommand.php`

### plugin:create
**Create a basic plugin**
For example, `php webman plugin:create --name=foo/admin` will create two directories `config/plugin/foo/admin` and `vendor/foo/admin`
See [Create basic plugin](/doc/webman/plugin/create.html)

### plugin:export
**Export a basic plugin**
For example, `php webman plugin:export --name=foo/admin` 
See [Create basic plugin](/doc/webman/plugin/create.html)

### plugin:export
**Export an application plugin**
For example, `php webman plugin:export shop`
See [Application plugin](/doc/webman/plugin/app.html)

### phar:pack
**Pack the webman project into a phar file**
See [Phar packaging](/doc/webman/others/phar.html)
> This feature requires webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Custom Commands
Users can define their own commands, for example, the command below prints the database configuration.

* Run `php webman make:command config:mysql`
* Open `app/command/ConfigMySQLCommand.php` and modify it as follows

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
    protected static $defaultDescription = 'Display current MySQL server configuration';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL configuration information is as follows:');
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
  
## Testing

Run `php webman config:mysql` in the command line.

The result is similar to the following:
```php
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## More References
http://www.symfonychina.com/doc/current/components/console.html
