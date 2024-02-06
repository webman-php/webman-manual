# webman

## Introduzione
`vlucas/phpdotenv` è un componente di caricamento delle variabili d'ambiente utilizzato per differenziare le configurazioni in ambienti diversi come lo sviluppo, il test, ecc.

## Indirizzo del progetto
https://github.com/vlucas/phpdotenv

## Installazione
```php
composer require vlucas/phpdotenv
```

## Utilizzo

#### Creare un file `.env` nella directory radice del progetto
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modificare il file di configurazione
**config/database.php**
```php
return [
    // Database predefinito
    'default' => 'mysql',

    // Configurazioni di vari database
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Nota**
> Si consiglia di inserire il file `.env` nella lista di `.gitignore` per evitare il commit nel repository del codice. Aggiungere un file di esempio di configurazione `.env.example` al repository in modo che durante il deploy del progetto si possa copiare `.env.example` come `.env` e modificare le configurazioni all'interno di `.env` in base all'ambiente attuale. In questo modo, il progetto può caricare configurazioni diverse in ambienti diversi.

> **Attenzione**
> `vlucas/phpdotenv` potrebbe avere dei bug nella versione di PHP TS (Thread Safe), si consiglia di utilizzare la versione NTS (Non-Thread Safe).
> È possibile verificare la versione attuale di PHP eseguendo il comando `php -v`

## Ulteriori informazioni

Visita https://github.com/vlucas/phpdotenv
