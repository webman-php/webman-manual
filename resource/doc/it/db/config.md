# Configurazione
Illuminate/database supporta i seguenti database e le relative versioni:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

Il file di configurazione del database si trova in `config/database.php`.

```php
return [
    // Database predefinito
    'default' => 'mysql',
    // Configurazioni per i vari database
    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => '',
            'prefix' => '',
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => 'localhost',
            'port' => 1433,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
        ],
    ],
];
```

## Utilizzo di più database
Utilizza `Db::connection('nome_configurazione')` per selezionare quale database utilizzare, dove `nome_configurazione` corrisponde alla chiave della configurazione nel file `config/database.php`.

Ad esempio, considera la seguente configurazione del database:

```php
return [
    // Database predefinito
    'default' => 'mysql',
    // Configurazioni per i vari database
    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'mysql2' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'webman2',
            'username' => 'webman2',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ];
```

Puoi quindi passare da un database all'altro in questo modo:

```php
// Usa il database predefinito, equivalente a Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// Usa mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Usa pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
