# Configuración
Illuminate/database tiene las siguientes compatibilidades con bases de datos y versiones:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

El archivo de configuración de la base de datos se encuentra en `config/database.php`.

```php
return [
    // Base de datos por defecto
    'default' => 'mysql',
    
    // Configuraciones de varias bases de datos
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

## Uso de múltiples bases de datos
Utiliza `Db::connection('nombre_de_configuración')` para seleccionar qué base de datos usar, donde `nombre_de_configuración` es la clave correspondiente en el archivo de configuración `config/database.php`.

Por ejemplo, con la siguiente configuración de base de datos:

```php
return [
    // Base de datos por defecto
    'default' => 'mysql',
    
    // Configuraciones de varias bases de datos
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        =>   '127.0.0.1',
            'port'        => 3306,
            'database'    => 'webman',
            'username'    => 'webman',
            'password'    => '',
            'unix_socket' =>  '',
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
];
```

Como en el siguiente ejemplo para cambiar la base de datos:

```php
// Utiliza la base de datos por defecto, equivalente a Db::connection('mysql')->table('users')->where('name', 'John')->first();
$usuarios = Db::table('users')->where('name', 'John')->first();
// Usa mysql2
$usuarios = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Usa pgsql
$usuarios = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
