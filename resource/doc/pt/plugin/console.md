# Plugin webman/console de linha de comando

`webman/console` baseia-se em `symfony/console`

> O plug-in requer webman>=1.2.2 webman-framework>=1.2.1

## Instalação

```sh
composer require webman/console
```

## Comandos suportados
**Método de uso**  
`php webman comando` ou `php webman comando`.  
Por exemplo `php webman versão` ou `php webman versão`

## Comandos suportados
### versão
**Imprime a versão do webman**

### route:list
**Imprime a configuração de rota atual**

### make:controller
**Cria um arquivo de controlador**  
Por exemplo, `php webman make:controller admin` irá criar um `app/controller/AdminController.php`  
Por exemplo, `php webman make:controller api/user` irá criar um `app/api/controller/UserController.php`

### make:model
**Cria um arquivo de modelo**  
Por exemplo, `php webman make:model admin` irá criar um `app/model/Admin.php`  
Por exemplo, `php webman make:model api/user` irá criar um `app/api/model/User.php`

### make:middleware
**Cria um arquivo de middleware**  
Por exemplo, `php webman make:middleware Auth` irá criar um `app/middleware/Auth.php`

### make:command
**Cria arquivo de comando personalizado**  
Por exemplo, `php webman make:command db:config` irá criar um `app\command\DbConfigCommand.php`

### plugin:create
**Cria um plug-in básico**  
Por exemplo, `php webman plugin:create --name=foo/admin` irá criar dois diretórios `config/plugin/foo/admin` e `vendor/foo/admin`  
Veja [Criar um plug-in básico](/doc/webman/plugin/create.html)

### plugin:export
**Exportar plug-in básico**  
Por exemplo, `php webman plugin:export --name=foo/admin`  
Veja [Criar um plug-in básico](/doc/webman/plugin/create.html)

### plugin:export
**Exportar plug-in de aplicativo**  
Por exemplo, `php webman plugin:export shop`  
Veja [Plug-in de aplicativo](/doc/webman/plugin/app.html)

### phar:pack
**Empacotar o projeto webman em um arquivo phar**  
Veja [Empacotar phar](/doc/webman/others/phar.html)  
> Esta funcionalidade requer webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Comandos personalizados
Os usuários podem definir seus próprios comandos, por exemplo, o seguinte comando imprime a configuração do banco de dados:

* Execute `php webman make:command config:mysql`
* Abra `app/command/ConfigMySQLCommand.php` e modifique para o seguinte:

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
    protected static $defaultDescription = 'Mostra a configuração atual do servidor MySQL';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Informações de configuração do MySQL:');
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
                        $row[] = $config['default'] == $name ? 'verdadeiro' : 'falso';
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

## Teste

Execute `php webman config:mysql` no terminal

O resultado será semelhante ao seguinte:

```php
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | verdadeiro | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Mais informações
http://www.symfonychina.com/doc/current/components/console.html
