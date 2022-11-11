# vlucas/phpdotenv

## Description
`vlucas/phpdotenv`is an environment variable loading component used to distinguish between different environments (such as development environments, test environments, etc.) configuration。

## Project address

https://github.com/vlucas/phpdotenv
  
## Install
 
```php
composer require vlucas/phpdotenv
 ```
  
## Usage

#### Create a new `.env` file in the project root
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modify configuration file
**config/database.php**
```php
return [
    // Default database
    'default' => 'mysql',

    // Various database configurations
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

> **hint**
> It is recommended that `.env` files be added to the `.gitignore` list to avoid commits to the codebase. Add a `.env.example` sample configuration file to the codebase, copy `.env.example` as `.env` when the project is deployed, and modify the configuration in `.env` according to the current environment, so that the project can load different configurations in different environments。

> **Note**
> `vlucas/phpdotenv`There may be bugs in the TS version of PHP (thread-safe version), please use the NTS version (non-thread-safe version))。
> The current version of php can be checked by executing `php -v` 

## More content

Access https://github.com/vlucas/phpdotenv
  

