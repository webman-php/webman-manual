# Migration Database Migration Tool Phinx

## Description

Phinx allows developers to easily modify and maintain databases. It avoids manual writing of SQL statements and uses a powerful PHP API to manage database migration. Developers can use version control to manage their database migrations. Phinx makes it easy to migrate data between different databases. It can also track which migration scripts have been executed, allowing developers to focus more on writing better systems without worrying about the state of the database.

## Project URL

https://github.com/cakephp/phinx

## Installation

```php
composer require robmorgan/phinx
```

## Official Chinese Documentation

For detailed usage, please refer to the official Chinese documentation. Here we will only discuss how to configure and use Phinx in webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Migration File Directory Structure

```
.
├── app                           Application directory
│   ├── controller                Controller directory
│   │   └── Index.php             Controller
│   ├── model                     Model directory
......
├── database                      Database files
│   ├── migrations                Migration files
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Test data
│   │   └── UserSeeder.php
......
```

## phinx.php Configuration

Create a phinx.php file in the project root directory

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## Usage Recommendations

Once migration files are merged into the code, they should not be modified again. If there are issues, a new modification or deletion operation file must be created to handle them.

#### Naming Convention for Table Creation Operation Files

`{time(auto create)}_create_{lowercase_table_name_in_english}`

#### Naming Convention for Table Modification Operation Files

`{time(auto create)}_modify_{lowercase_table_name_in_english+specific_lowercase_modification_item}`

#### Naming Convention for Table Deletion Operation Files

`{time(auto create)}_delete_{lowercase_table_name_in_english+specific_lowercase_modification_item}`

#### Naming Convention for Data Filling Files

`{time(auto create)}_fill_{lowercase_table_name_in_english+specific_lowercase_modification_item}`
