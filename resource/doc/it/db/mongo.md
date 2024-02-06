# MongoDB

webman usa [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) come componente MongoDB in modo predefinito, che è stato derivato dal progetto laravel e utilizza la stessa modalità di utilizzo di laravel.

Prima di utilizzare `jenssegers/mongodb`, è necessario installare l'estensione mongodb per `php-cli`.

> Ciò che si può fare è utilizzare il comando `php -m | grep mongodb` per verificare se l'estensione mongodb è stata installata per `php-cli`. Si noti che anche se si è installata l'estensione mongodb per `php-fpm`, non implica che si possa utilizzare in `php-cli`, in quanto `php-cli` e `php-fpm` sono applicazioni diverse e potrebbero utilizzare configurazioni `php.ini` diverse. È possibile verificare quale file di configurazione `php.ini` sta utilizzando `php-cli` con il comando `php --ini`.

## Installazione

Per PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
Per PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Dopo l'installazione, è necessario riavviare (restart) e non è sufficiente eseguire il reload.

## Configurazione
Aggiungere la connessione `mongodb` nel file `config/database.php`, simile a quanto segue:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ... altri dettagli di configurazione sono stati omessi ...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // in questo punto è possibile passare ulteriori impostazioni al Mongo Driver Manager.
                // vedere https://www.php.net/manual/en/mongodb-driver-manager.construct.php sotto "Uri Options" per un elenco completo dei parametri che è possibile utilizzare

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

## Per ulteriori informazioni, si prega di visitare

https://github.com/jenssegers/laravel-mongodb
