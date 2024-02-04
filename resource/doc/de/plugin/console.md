# webman/console Befehlszeilen-Plugin

`webman/console` basiert auf `symfony/console`

> Das Plugin benötigt webman>=1.2.2 webman-framework>=1.2.1

## Installation
 
```sh
composer require webman/console
```

## Unterstützte Befehle
**Verwendung**  
`php webman Befehl` oder `php webman Befehl`.
Beispielsweise `php webman version` oder `php webman version`

## Unterstützte Befehle
### version
**Gibt die Webman-Version aus**

### route:list
**Gibt die aktuelle Routenkonfiguration aus**

### make:controller
**Erstellt eine Controller-Datei** 
Beispiel: `php webman make:controller admin` erstellt eine `app/controller/AdminController.php`
Beispiel: `php webman make:controller api/user` erstellt eine `app/api/controller/UserController.php`

### make:model
**Erstellt eine Model-Datei**
Beispiel: `php webman make:model admin` erstellt eine `app/model/Admin.php`
Beispiel: `php webman make:model api/user` erstellt eine `app/api/model/User.php`

### make:middleware
**Erstellt eine Middleware-Datei**
Beispiel: `php webman make:middleware Auth` erstellt eine `app/middleware/Auth.php`

### make:command
**Erstellt eine benutzerdefinierte Befehlsdatei**
Beispiel: `php webman make:command db:config` erstellt eine `app\command\DbConfigCommand.php`

### plugin:create
**Erstellt ein Basis-Plugin**
Beispiel: `php webman plugin:create --name=foo/admin` erstellt die Verzeichnisse `config/plugin/foo/admin` und `vendor/foo/admin`
Siehe [Erstellen eines Basis-Plugins](/doc/webman/plugin/create.html)

### plugin:export
**Exportiert ein Basis-Plugin**
Beispiel: `php webman plugin:export --name=foo/admin` 
Siehe [Erstellen eines Basis-Plugins](/doc/webman/plugin/create.html)

### plugin:export
**Exportiert ein Anwendungs-Plugin**
Beispiel: `php webman plugin:export shop`
Siehe [Anwendungs-Plugin](/doc/webman/plugin/app.html)

### phar:pack
**Packt das Webman-Projekt in eine Phar-Datei**
Siehe [Phar-Packen](/doc/webman/others/phar.html)
> Dieses Feature erfordert webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Benutzerdefinierte Befehle
Benutzer können eigene Befehle definieren, zum Beispiel ein Befehl, um die Datenbankkonfiguration auszugeben

* Führe `php webman make:command config:mysql` aus
* Öffne `app/command/ConfigMySQLCommand.php` und ändere es wie folgt

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
    protected static $defaultDescription = 'Zeigt die aktuelle MySQL Serverkonfiguration an';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL-Konfigurationsinformationen:');
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
  
## Test

Führe den Befehl `php webman config:mysql` aus

Das Ergebnis ähnelt dem folgenden:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Weitere Informationen
http://www.symfonychina.com/doc/current/components/console.html
