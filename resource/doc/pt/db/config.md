# Configuração
O suporte do banco de dados illuminate/database e suas versões são as seguintes:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

O arquivo de configuração do banco de dados está localizado em `config/database.php`.

```php
return [
    // Banco de dados padrão
    'default' => 'mysql',
    // Conexões de vários bancos de dados
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

## Usando vários bancos de dados
Para selecionar qual banco de dados usar, utilize `Db::connection('nome_da_configuração')`, onde `nome_da_configuração` corresponde à `key` da configuração no arquivo de configuração `config/database.php`.

Por exemplo, com a seguinte configuração de banco de dados:

```php
 return [
    // Banco de dados padrão
    'default' => 'mysql',
    // Conexões de vários bancos de dados
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

Assim é feita a troca de bancos de dados.

```php
// Usando o banco de dados padrão, equivalente a Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first(); 
// Usando mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Usando pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
