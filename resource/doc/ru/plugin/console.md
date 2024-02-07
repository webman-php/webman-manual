# Консольный плагин webman/console

`webman/console` основан на `symfony/console`

> Плагин требует webman>=1.2.2 и webman-framework>=1.2.1

## Установка
 
```sh
composer require webman/console
```

## Поддерживаемые команды
**Использование**  
`php webman команда` или `php webman команда`.
Например, `php webman version` или `php webman version`

## Поддерживаемые команды
### version
**Печать версии webman**

### route:list
**Печать текущей конфигурации маршрутов**

### make:controller
**Создание файла контроллера** 
Например, `php webman make:controller admin` создаст `app/controller/AdminController.php`
Например, `php webman make:controller api/user` создаст `app/api/controller/UserController.php`

### make:model
**Создание файла модели**
Например, `php webman make:model admin` создаст `app/model/Admin.php`
Например, `php webman make:model api/user` создаст `app/api/model/User.php`

### make:middleware
**Создание файла промежуточного слоя**
Например, `php webman make:middleware Auth` создаст `app/middleware/Auth.php`

### make:command
**Создание файла пользовательской команды**
Например, `php webman make:command db:config` создаст `app\command\DbConfigCommand.php`

### plugin:create
**Создание базового плагина**
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
**Упаковка проекта webman в phar-файл**
См. [Сборка phar](/doc/webman/others/phar.html)
> Для этой функции требуется webman>=1.2.4, webman-framework>=1.2.4, webman\console>=1.0.5

## Пользовательские команды
Пользователи могут создавать собственные команды, например, укажем команду для печати конфигурации базы данных

* Выполните `php webman make:command config:mysql`
* Откройте `app/command/ConfigMySQLCommand.php` и измените на следующий код

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
    protected static $defaultDescription = 'Отображение текущей конфигурации сервера MySQL';

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

Запустите команду `php webman config:mysql`

Результат будет примерно следующим:
```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Дополнительные материалы
http://www.symfonychina.com/doc/current/components/console.html
