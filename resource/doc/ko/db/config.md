Illuminate/database 데이터베이스 및 지원 버전은 다음과 같습니다:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

데이터베이스 구성 파일의 위치는 `config/database.php`입니다.

```php
return [
    // 기본 데이터베이스
    'default' => 'mysql',
    // 다양한 데이터베이스 구성
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

## 여러 데이터베이스 사용
`Db::connection('구성 이름')`을 사용하여 데이터베이스를 선택하고, 여기서 `구성 이름`은 `config/database.php` 파일의 해당 설정의 `key`입니다.

다음과 같은 데이터베이스 구성 예시:

```php
return [
    // 기본 데이터베이스
    'default' => 'mysql',
    // 다양한 데이터베이스 구성
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
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

다음과 같이 데이터베이스를 전환합니다.
```php
// 기본 데이터베이스 사용, Db::connection('mysql')->table('users')->where('name', 'John')->first();와 동등합니다.
$users = Db::table('users')->where('name', 'John')->first();; 
// mysql2 사용
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql 사용
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
