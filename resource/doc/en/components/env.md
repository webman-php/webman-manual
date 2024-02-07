# vlucas/phpdotenv

## Description
`vlucas/phpdotenv` is an environment variable loading component used to differentiate configurations for different environments such as development, testing, etc.

## Project Repository

https://github.com/vlucas/phpdotenv

## Installation

```php
composer require vlucas/phpdotenv
```

## Usage

#### Create `.env` file in the project root directory
**.env**
```ini
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Update the configuration file
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
> It's recommended to add the `.env` file to your `.gitignore` list to avoid committing it to your code repository. Instead, include a `.env.example` configuration sample file in your repository and when deploying the project, copy `.env.example` as `.env` and modify the configuration in `.env` based on the specific environment. This way, different configurations can be loaded for different environments.

> **Note**
> `vlucas/phpdotenv` may have bugs in the PHP TS (Thread Safe) version. Please use the NTS (Non-Thread Safe) version. You can check the current PHP version by executing `php -v`.

## More Information

Visit https://github.com/vlucas/phpdotenv.
