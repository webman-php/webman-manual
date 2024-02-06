# Plugin della riga di comando webman/console

`webman/console` basato su `symfony/console`

>Plugin richiesto webman>=1.2.2 webman-framework>=1.2.1

## Installazione

```sh
composer require webman/console
```

## Comandi supportati
**Uso**  
`php webman comando` oppure `php webman comando`.
Ad esempio `php webman version` o `php webman version`

### versione
**Stampa il numero di versione di webman**

### route:list
**Stampa la configurazione attuale del percorso**

### make:controller
**Crea un file controller**
Ad esempio `php webman make:controller admin` creerà un `app/controller/AdminController.php`
Ad esempio `php webman make:controller api/user` creerà un `app/api/controller/UserController.php`

### make:model
**Crea un file model**
Ad esempio `php webman make:model admin` creerà un `app/model/Admin.php`
Ad esempio `php webman make:model api/user` creerà un `app/api/model/User.php`

### make:middleware
**Crea un file middleware**
Ad esempio `php webman make:middleware Auth` creerà un `app/middleware/Auth.php`

### make:command
**Crea un file di comando personalizzato**
Ad esempio `php webman make:command db:config` creerà un `app\command\DbConfigCommand.php`

### plugin:create
**Crea un plugin di base**
Ad esempio `php webman plugin:create --name=foo/admin` creerà due directory `config/plugin/foo/admin` e `vendor/foo/admin`
Vedi [Creazione di un plugin di base](/doc/webman/plugin/create.html)

### plugin:export
**Esporta un plugin di base**
Ad esempio `php webman plugin:export --name=foo/admin` 
Vedi [Creazione di un plugin di base](/doc/webman/plugin/create.html)

### plugin:export
**Esporta un plugin dell'applicazione**
Ad esempio `php webman plugin:export shop`
Vedi [Plugin dell'applicazione](/doc/webman/plugin/app.html)

### phar:pack
**Imballa il progetto webman come file phar**
Vedi [Imballaggio phar](/doc/webman/others/phar.html)
> Qeusta caratteristica richiede webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## Comandi personalizzati
Gli utenti possono definire i propri comandi, ad esempio di seguito è riportato un comando per stampare la configurazione del database.

* Eseguire `php webman make:command config:mysql`
* Aprire `app/command/ConfigMySQLCommand.php` e modificarlo come segue

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
    protected static $defaultDescription = 'Mostra la configurazione corrente del server MySQL';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Ecco le informazioni di configurazione di MySQL:');
        $config = config('database');
        $headers = ['nome', 'predefinito', 'driver', 'host', 'porta', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefisso', 'rigoroso', 'motore', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'nome':
                        $row[] = $name;
                        break;
                    case 'predefinito':
                        $row[] = $config['default'] == $name ? 'vero' : 'falso';
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

Esegui il comando nel terminale `php webman config:mysql`

Il risultato sarà simile a quanto segue:
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| nome  | predefinito | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefisso | rigoroso | motore | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | vero    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## Ulteriori informazioni
http://www.symfonychina.com/doc/current/components/console.html
