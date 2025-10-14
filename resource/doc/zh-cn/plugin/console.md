# webman/console 命令行插件

`webman/console` 基于 `symfony/console`

## 安装
 
```sh
composer require webman/console
```

## 支持的命令
**使用方法**  
`php webman 命令`
例如 `php webman version`  

> ** 提示 **
> linux系统下可简化为 `./webman 命令`

## 支持的命令
### version
**打印webman版本号**

### route:list
**打印当前路由配置**

### make:controller
**创建一个控制器文件** 
例如 `php webman make:controller admin` 将创建一个 `app/controller/AdminController.php`
例如 `php webman make:controller api/user` 将创建一个 `app/api/controller/UserController.php`

### make:model
**创建一个model文件**
例如 `php webman make:model admin` 将创建一个 `app/model/Admin.php`
例如 `php webman make:model api/user` 将创建一个 `app/api/model/User.php`

### make:middleware
**创建一个中间件文件**
例如 `php webman make:middleware Auth` 将创建一个 `app/middleware/Auth.php`

### make:command
**创建自定义命令文件**
例如 `php webman make:command db:config` 将创建一个 `app\command\DbConfigCommand.php`

### plugin:create
**创建一个基础插件**
例如 `php webman plugin:create --name=foo/admin` 将创建`config/plugin/foo/admin` 和 `vendor/foo/admin` 两个目录
参见[创建基础插件](/doc/webman/plugin/create.html)

### plugin:export
**导出基础插件**
例如 `php webman plugin:export --name=foo/admin` 
参见[创建基础插件](/doc/webman/plugin/create.html)

### plugin:export
**导出应用插件**
例如 `php webman plugin:export shop`
参见[应用插件](/doc/webman/plugin/app.html)

### build:phar
**将webman项目打包成phar文件**
参见[phar打包](/doc/webman/others/phar.html)

### build:bin
**将webman项目打包成可直接执行的二进制文件**
参见[phar打包](/doc/webman/others/bin.html)

## 自定义命令
用户可以定义自己的命令，例如以下是打印数据库配置的命令

* 执行 `php webman make:command config:mysql`
* 新建 `app/command/ConfigMySQLCommand.php` 代码如下

```php
<?php

namespace app\command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('config:mysql', '显示当前MySQL服务器配置')]
class ConfigMySQLCommand extends Command
{
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('MySQL配置信息如下：');
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
以上为`Symfony/console>=7.3`写法，低于此版本可以使用以下代码
```php
<?php

namespace app\command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('config:mysql', '显示当前MySQL服务器配置')]
class ConfigMySQLCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL配置信息如下：');
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

## 测试

命令行运行 `php webman config:mysql`

结果类似如下：
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## 注解(Symfony/console>=7.3)
以下三个注解用于代替旧版的`configure`方法中的内容，使用注解后代码简洁易读。
### AsCommand
此注解作用于类，用于声明命令行名称等。需要注意的是框架并未提供注解扫描，所以仍然需要将类名添加到配置文件中。
```php
#[AsCommand(
    name: 'config:mysql', // 命令名称
    description: '显示当前MySQL服务器配置', // 命令介绍
    aliases: ['config:database'], //命令别名
    hidden: false, // 是否隐藏
    'help page content' // help内容
)]
class Command {}
```
```php
//旧版写法
protected static string $defaultName = 'config:mysql';
protected static string $defaultDescription = '显示当前MySQL服务器配置';
```
### Argument
此注解作用于属性，用于声明命令行参数，声明后将自动注入到属性中。
```php
    public function __invoke(OutputInterface $output, #[Argument('The username of the user.')] string $username): int
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        $output->writeln('Username: '.$username);

        return Command::SUCCESS;
    }
```
```php
//旧版写法
$this->addArgument('username', InputArgument::REQUIRED, 'The username of the user.');
```
命令行运行：`php webman test:command zhangsan`

### Option
此注解作用于属性，用于声明命令行选项，声明后将自动注入到属性中。
```php
public function __invoke(OutputInterface $output, #[Option('The username of the user.', shortcut: 'u')] string $username = 'zhangsan'): int
{
    $output->writeln([
        'User Creator',
        '============',
        '',
    ]);

    $output->writeln('Username: '.$username);

    return Command::SUCCESS;
}
```
```php
//旧版写法
$this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'The username of the user.', 'zhangsan');
```
命令行运行：`php webman test:command --username=zhangsan`


## 更多资料参考
https://symfony.com/doc/current/console.html
