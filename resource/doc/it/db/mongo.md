# MongoDB

webman utilizza [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) come componente MongoDB predefinito, che è stato estratto dal progetto Laravel e funziona allo stesso modo.

Prima di utilizzare `jenssegers/mongodb`, è necessario installare l'estensione MongoDB per `php-cli`.

> Utilizza il comando `php -m | grep mongodb` per verificare se l'estensione MongoDB è installata per `php-cli`. Nota: anche se hai installato l'estensione MongoDB per `php-fpm`, non significa che sia disponibile per `php-cli`, poiché sono due applicazioni diverse con configurazioni `php.ini` diverse. Utilizza il comando `php --ini` per verificare quale file di configurazione `php.ini` è utilizzato da `php-cli`.

## Installazione

PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Dopo l'installazione, è necessario riavviare (non ricaricare) il sistema.

## Configurazione
Aggiungi la connessione `mongodb` nel file `config/database.php`, simile a quanto segue:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...Altre configurazioni qui...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // Qui puoi passare altre impostazioni al Mongo Driver Manager
                // consulta https://www.php.net/manual/en/mongodb-driver-manager.construct.php nel paragrafo "Uri Options" per un elenco completo dei parametri che puoi utilizzare

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Esempio
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## Per ulteriori informazioni, visitare

https://github.com/jenssegers/laravel-mongodb
