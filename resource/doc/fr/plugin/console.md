# Le plugin de ligne de commande webman/console

`webman/console` est basé sur `symfony/console`

> Le plugin nécessite webman>=1.2.2 et webman-framework>=1.2.1

## Installation

```sh
composer require webman/console
```

## Commandes Supportées
**Utilisation**  
`php webman commande` ou `php webman commande`
Par exemple `php webman version` ou `php webman version`

## Commandes Supportées
### version
**Affiche le numéro de version de webman**

### route:list
**Affiche la configuration actuelle des routes**

### make:controller
**Crée un fichier de contrôleur** 
Par exemple, `php webman make:controller admin` créera un fichier `app/controller/AdminController.php`
Par exemple, `php webman make:controller api/user` créera un fichier `app/api/controller/UserController.php`

### make:model
**Crée un fichier de modèle**
Par exemple, `php webman make:model admin` créera un fichier `app/model/Admin.php`
Par exemple, `php webman make:model api/user` créera un fichier `app/api/model/User.php`

### make:middleware
**Crée un fichier middleware**
Par exemple, `php webman make:middleware Auth` créera un fichier `app/middleware/Auth.php`

### make:command
**Crée un fichier de commande personnalisée**
Par exemple, `php webman make:command db:config` créera un fichier `app\command\DbConfigCommand.php`

### plugin:create
**Crée un plugin de base**
Par exemple, `php webman plugin:create --name=foo/admin` créera deux répertoires, `config/plugin/foo/admin` et `vendor/foo/admin`
Voir [Créer un plugin de base](/doc/webman/plugin/create.html)

### plugin:export
**Exporte un plugin de base**
Par exemple, `php webman plugin:export --name=foo/admin` 
Voir [Créer un plugin de base](/doc/webman/plugin/create.html)

### plugin:export
**Exporte un plugin d'application**
Par exemple, `php webman plugin:export shop`
Voir [Plugin d'application](/doc/webman/plugin/app.html)

### phar:pack
**Packager le projet webman en fichier phar**
Voir [Emballage phar](/doc/webman/others/phar.html)
> Cette fonctionnalité nécessite webman>=1.2.4, webman-framework>=1.2.4 et webman/console>=1.0.5

## Commandes personnalisées
Les utilisateurs peuvent définir leurs propres commandes, par exemple le suivant est une commande pour afficher la configuration de la base de données.

* Exécuter `php webman make:command config:mysql`
* Ouvrir `app/command/ConfigMySQLCommand.php` et le modifier comme suit

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
    protected static $defaultDescription = 'Affiche la configuration actuelle du serveur MySQL';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Informations de configuration de MySQL :');
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

Exécutez `php webman config:mysql` en ligne de commande

Le résultat sera similaire à :

```shell
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Pour plus d'informations
http://www.symfonychina.com/doc/current/components/console.html
