# Configuração
O suporte para bancos de dados e suas versões no illuminate/database é o seguinte:

 - MySQL 5.6+
 - PostgreSQL 9.4+
 - SQLite 3.8.8+
 - SQL Server 2017+

O arquivo de configuração do banco de dados está localizado em `config/database.php`.

```php
return [
    // Banco de dados padrão
    'default' => 'mysql',
    // Configurações para diversos tipos de banco de dados
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

## Utilizando Múltiplos Bancos de Dados
Utilize `Db::connection('nome_da_configuração')` para selecionar qual banco de dados utilizar, onde `nome_da_configuração` é a `key` correspondente à configuração no arquivo de configuração `config/database.php`.

Por exemplo, o seguinte é um exemplo de configuração de banco de dados:

```php
return [
    // Banco de dados padrão
    'default' => 'mysql',
    // Configurações para diversos tipos de banco de dados
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

Para alternar entre os bancos de dados, utilize da seguinte forma:
```php
// Utilizando o banco de dados padrão, equivalente a Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// Utilizando mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Utilizando pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
