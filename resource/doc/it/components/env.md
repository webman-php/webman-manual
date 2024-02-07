# webman

## Introduzione
`webman` è un framework PHP ad alte prestazioni basato su Workerman, utilizzato per la creazione di applicazioni web.

## Indirizzo del progetto
https://github.com/walkor/webman

## Installazione
```php
composer require walkor/webman
```

## Utilizzo
#### Crea un file `.env` nella directory radice del progetto
**.env**
```plaintext
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modifica il file di configurazione
**config/database.php**
```php
return [
    // Database predefinito
    'default' => 'mysql',

    // Configurazioni di diversi database
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
> Si consiglia di aggiungere il file `.env` alla lista di `.gitignore` per evitare il commit nel repository del codice. Aggiungere anche un file di esempio di configurazione `.env.example` nel repository, in modo che durante il rilascio del progetto si possa copiare `.env.example` in `.env` e modificare la configurazione in base all'ambiente corrente, consentendo così al progetto di caricare configurazioni diverse in ambienti diversi.

> **Attenzione**
> `webman` potrebbe avere problemi con le versioni PHP TS (Thread Safe). Si consiglia di utilizzare la versione NTS (Non-Thread Safe). Per verificare la versione corrente di PHP, eseguire il comando `php -v`.

## Ulteriori informazioni
Visita https://github.com/walkor/webman
