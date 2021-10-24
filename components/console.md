# console命令行组件 symfony/console

  
## 安装
 
```php
composer require symfony/console
```
  
## 创建执行文件

项目根目录创建webman文件

```php
#!/usr/bin/env php
<?php
use Dotenv\Dotenv;
use Symfony\Component\Console\Application as Cli;
use Webman\Bootstrap;
use Webman\Config;

require_once __DIR__ . '/vendor/autoload.php';

if (class_exists('Dotenv\Dotenv')) {
  if (method_exists('Dotenv\Dotenv', 'createUnsafeImmutable')) {
      Dotenv::createUnsafeImmutable(base_path())->load();
  } else {
      Dotenv::createMutable(base_path())->load();
  }
}
Config::load(config_path(), ['route', 'container']);
if ($timezone = config('app.default_timezone')) {
  date_default_timezone_set($timezone);
}
foreach (config('autoload.files', []) as $file) {
  include_once $file;
}
foreach (config('bootstrap', []) as $class_name) {
  /** @var Bootstrap $class_name */
  $class_name::start(null);
}

$cli = new Cli();
$cli->setName('webman命令行工具');

$dir_iterator = new \RecursiveDirectoryIterator(app_path() . DIRECTORY_SEPARATOR . 'command');
$iterator = new \RecursiveIteratorIterator($dir_iterator);
foreach ($iterator as $file) {
  if (is_dir($file)) {
      continue;
  }
  $class_name = str_replace(DIRECTORY_SEPARATOR, '\\',substr(substr($file, strlen(base_path())), 0, -4));
  if (!is_a($class_name, \Symfony\Component\Console\Command\Command::class, true)) {
      continue;
  }
  $cli->add(new $class_name);
}
$cli->run();

```
  
## 使用
新建文件 `app/command/MySQLCommand.php`，用于展示数据库配置
```php
<?php

namespace app\command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MySQLCommand extends \Symfony\Component\Console\Command\Command
{
   protected static $defaultName = 'config:mysql';
   protected static $defaultDescription = '显示当前MySQL服务器配置';

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

