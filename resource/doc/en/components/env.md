# vlucas/phpdotenv

## Description
`vlucas/phpdotenv` is an environment variable loading component used to differentiate configurations for different environments (e.g., development environment, testing environment, etc.).

## Project Repository
https://github.com/vlucas/phpdotenv
  
## Installation
```php
composer require vlucas/phpdotenv
 ```
  
## Usage

#### Create a `.env` file in the project root directory
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modify the configuration file
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

> **Note**
> It is recommended to add the `.env` file to the `.gitignore` list to avoid committing it to the code repository. Add a `.env.example` configuration sample file to the code repository, and when deploying the project, copy `.env.example` to `.env` and modify the configurations in `.env` according to the current environment. This allows the project to load different configurations in different environments.

> **Caution**
> `vlucas/phpdotenv` may have bugs in the PHP TS version (thread-safe version). Please use the NTS version (non-thread-safe version).
> You can check the current PHP version by running `php -v`.

## More Information
Visit https://github.com/vlucas/phpdotenv  
