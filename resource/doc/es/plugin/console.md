# El complemento de la línea de comandos webman/console

`webman/console` se basa en `symfony/console`

> El complemento requiere webman>=1.2.2 webman-framework>=1.2.1

## Instalación

```sh
composer require webman/console
```

## Comandos admitidos
**Uso**
`php webman comando` o `php webman comando`.
Por ejemplo, `php webman version` o `php webman version`

## Comandos admitidos
### version
**Imprime el número de versión de webman**

### route:list
**Imprime la configuración de ruta actual**

### make:controller
**Crea un archivo de controlador**
Por ejemplo, `php webman make:controller admin` creará un `app/controller/AdminController.php`
Por ejemplo, `php webman make:controller api/user` creará un `app/api/controller/UserController.php`

### make:model
**Crea un archivo de modelo**
Por ejemplo, `php webman make:model admin` creará un `app/model/Admin.php`
Por ejemplo, `php webman make:model api/user` creará un `app/api/model/User.php`

### make:middleware
**Crea un archivo de middleware**
Por ejemplo, `php webman make:middleware Auth` creará un `app/middleware/Auth.php`

### make:command
**Crea un archivo de comando personalizado**
Por ejemplo, `php webman make:command db:config` creará un `app\command\DbConfigCommand.php`

### plugin:create
**Crea un complemento básico**
Por ejemplo, `php webman plugin:create --name=foo/admin` creará dos directorios `config/plugin/foo/admin` y `vendor/foo/admin`
Ver [Crear complemento básico](/doc/webman/plugin/create.html)

### plugin:export
**Exporta un complemento básico**
Por ejemplo, `php webman plugin:export --name=foo/admin`
Ver [Crear complemento básico](/doc/webman/plugin/create.html)

### plugin:export
**Exporta un complemento de la aplicación**
Por ejemplo, `php webman plugin:export shop`
Ver [Complemento de la aplicación](/doc/webman/plugin/app.html)

### phar:pack
**Empaqueta el proyecto webman en un archivo phar**
Ver [Empaquetar phar](/doc/webman/others/phar.html)
> Esta característica requiere webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Comandos personalizados
Los usuarios pueden definir sus propios comandos, por ejemplo, el siguiente comando imprime la configuración de la base de datos

* Ejecute `php webman make:command config:mysql`
* Abra `app/command/ConfigMySQLCommand.php` y modifíquelo de la siguiente manera

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
    protected static $defaultDescription = 'Mostrar la configuración actual del servidor MySQL';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('La información de configuración de MySQL es la siguiente:');
        $config = config('database');
        $headers = ['nombre', 'predeterminado', 'controlador', 'host', 'puerto', 'base de datos', 'nombre de usuario', 'contraseña', 'socket UNIX', 'juego de caracteres', 'collation', 'prefijo', 'estricto', 'motor', 'esquema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'nombre':
                        $row[] = $name;
                        break;
                    case 'predeterminado':
                        $row[] = $config['default'] == $name ? 'verdadero' : 'falso';
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

## Pruebas

Ejecute `php webman config:mysql` en la línea de comandos

El resultado se verá similar a lo siguiente:

```plaintext
+--------+-----------+---------+-----------+------+----------+------------+-----------+-------------+----------+-----------------+---------+---------+--------+---------+---------+
| nombre | predeterminado | controlador | host      | puerto | base de datos | nombre de usuario | contraseña | socket UNIX | juego de caracteres | collation | prefijo | estricto | motor | esquema | sslmode |
+--------+-----------+---------+-----------+------+----------+------------+-----------+-------------+----------+-----------------+---------+---------+--------+---------+---------+
| mysql  | verdadero | mysql   | 127.0.0.1 | 3306 | mysql    | root       | ******    |             | utf8     | utf8_unicode_ci   |         | 1       |        |         |         |
+--------+-----------+---------+-----------+------+----------+------------+-----------+-------------+----------+-----------------+---------+---------+--------+---------+---------+
```

## Para más información
http://www.symfonychina.com/doc/current/components/console.html
