# Configurazione
illuminate/database supporta i seguenti database e le relative versioni:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

Il file di configurazione del database si trova in `config/database.php`.

```php
return [
    // Database predefinito
    'default' => 'mysql',
    // Configurazioni per vari database
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'webman',
            'username'    => 'webman',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => '',
            'prefix'   => '',
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => '127.0.0.1',
            'port'     => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => 'localhost',
            'port'     => 1433,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
        ],
    ],
];
```

## Utilizzo di più database
Si può selezionare quale database utilizzare utilizzando `Db::connection('nome_configurazione')`, dove `nome_configurazione` è la chiave corrispondente alla configurazione nel file di configurazione `config/database.php`.

Ad esempio, consideriamo la seguente configurazione dei database:

```php
return [
    // Database predefinito
    'default' => 'mysql',
    // Configurazioni per vari database
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'webman',
            'username'    => 'webman',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],

        'mysql2' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'webman2',
            'username'    => 'webman2',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => '127.0.0.1',
            'port'     =>  5432,
            'database' => 'webman',
            'username' =>  'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],
    ],
];
```

Esempio di cambio di database:

```php
// Uso del database predefinito, equivalente a Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();;
// Uso di mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Uso di pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
