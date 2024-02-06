# Плагин командной строки webman/console

`webman/console` основан на `symfony/console`

> Плагин требует webman>=1.2.2 и webman-framework>=1.2.1

## Установка
 
```sh
composer require webman/console
```

## Поддерживаемые команды
**Использование**  
`php webman команда` или `php webman команда`。
Например, `php webman version` или `php webman version`

## Поддерживаемые команды
### version
**Выводит версию webman**

### route:list
**Выводит текущую конфигурацию маршрутов**

### make:controller
**Создает файл контроллера** 
Например, `php webman make:controller admin` создаст `app/controller/AdminController.php`
Например, `php webman make:controller api/user` создаст `app/api/controller/UserController.php`

### make:model
**Создает файл модели**
Например, `php webman make:model admin` создаст `app/model/Admin.php`
Например, `php webman make:model api/user` создаст `app/api/model/User.php`

### make:middleware
**Создает файл промежуточного слоя**
Например, `php webman make:middleware Auth` создаст `app/middleware/Auth.php`

### make:command
**Создает пользовательскую команду**
Например, `php webman make:command db:config` создаст `app\command\DbConfigCommand.php`

### plugin:create
**Создает базовый плагин**
Например, `php webman plugin:create --name=foo/admin` создаст два каталога `config/plugin/foo/admin` и `vendor/foo/admin`
См. [Создание базового плагина](/doc/webman/plugin/create.html)

### plugin:export
**Экспорт базового плагина**
Например, `php webman plugin:export --name=foo/admin` 
См. [Создание базового плагина](/doc/webman/plugin/create.html)

### plugin:export
**Экспорт приложения плагина**
Например, `php webman plugin:export shop`
См. [Приложение плагина](/doc/webman/plugin/app.html)

### phar:pack
**Упаковывает проект webman в файл phar**
См. [Упаковка phar](/doc/webman/others/phar.html)
> Данная функция требует webman>=1.2.4, webman-framework>=1.2.4, webman\console>=1.0.5

## Пользовательские команды
Пользователь может определить собственные команды, например, ниже приведена команда для печати конфигурации базы данных

* Выполнить `php webman make:command config:mysql`
* Откройте `app/command/ConfigMySQLCommand.php` и измените на следующее

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
    protected static $defaultDescription = 'Отображает текущую конфигурацию сервера MySQL';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Информация о конфигурации MySQL:');
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
## Тестирование

Запустите команду в командной строке `php webman config:mysql`

Результат будет примерно следующим образом:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Дополнительная информация
http://www.symfonychina.com/doc/current/components/console.html
