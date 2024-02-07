# webman/console 命令列外掛

`webman/console` 基於`symfony/console`

> 外掛需求webman>=1.2.2 webman-framework>=1.2.1

## 安裝

```sh
composer require webman/console
```

## 支援的命令
**使用方法**
`php webman 命令` 或者 `php webman 命令`。
例如 `php webman version` 或者 `php webman version`

## 支援的命令
### version
**列印webman版本號**

### route:list
**列印目前路由配置**

### make:controller
**建立一個控制器檔案**
例如 `php webman make:controller admin` 將建立一個 `app/controller/AdminController.php`
例如 `php webman make:controller api/user` 將建立一個 `app/api/controller/UserController.php`

### make:model
**建立一個model檔案**
例如 `php webman make:model admin` 將建立一個 `app/model/Admin.php`
例如 `php webman make:model api/user` 將建立一個 `app/api/model/User.php`

### make:middleware
**建立一個中介層檔案**
例如 `php webman make:middleware Auth` 將建立一個 `app/middleware/Auth.php`

### make:command
**建立自定義命令檔案**
例如 `php webman make:command db:config` 將建立一個 `app\command\DbConfigCommand.php`

### plugin:create
**建立一個基礎外掛**
例如 `php webman plugin:create --name=foo/admin` 將建立`config/plugin/foo/admin` 和 `vendor/foo/admin` 兩個目錄
參見[建立基礎外掛](/doc/webman/plugin/create.html)

### plugin:export
**匯出基礎外掛**
例如 `php webman plugin:export --name=foo/admin` 
參見[建立基礎外掛](/doc/webman/plugin/create.html)

### plugin:export
**匯出應用外掛**
例如 `php webman plugin:export shop`
參見[應用外掛](/doc/webman/plugin/app.html)

### phar:pack
**將webman專案打包成phar檔案**
參見[phar打包](/doc/webman/others/phar.html)
> 此特性需要webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## 自定義命令
使用者可以定義自己的命令，例如以下是列印資料庫配置的命令

* 執行 `php webman make:command config:mysql`
* 開啟 `app/command/ConfigMySQLCommand.php` 修改成如下

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
    protected static $defaultDescription = '顯示目前MySQL伺服器配置';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL配置資訊如下：');
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

## 測試

命令列執行 `php webman config:mysql`

結果類似如下：
```bash
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## 更多資料參考
http://www.symfonychina.com/doc/current/components/console.html
