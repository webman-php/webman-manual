# Migration Database Migration Tool Phinx

## Introduction

Phinx allows developers to easily modify and maintain databases. It eliminates the need for manually writing SQL statements, using a powerful PHP API to manage database migrations. Developers can use version control to manage their database migrations. Phinx can easily perform data migrations between different databases. It can also track which migration scripts have been executed, allowing developers to focus more on writing better systems without worrying about the state of the database.

## Project Address

https://github.com/cakephp/phinx

## Installation
 
  ```php
  composer require robmorgan/phinx
  ```
  
## Official Chinese Documentation Address

For detailed usage, you can refer to the official Chinese documentation. Here, we only discuss how to configure and use Phinx in webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Migration File Directory Structure

```
.
├── app                           Application directory
│   ├── controller                Controller directory
│   │   └── Index.php             Controller
│   ├── model                     Model directory
......
├── database                      Database files
│   ├── migrations                Migration files
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Test data
│   │   └── UserSeeder.php
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

## Suggestions for Use

Once the migration file is merged, it should not be modified again. If any issues arise, a new modification or deletion operation file should be created to handle the issue.

#### Naming Convention for Data Table Creation Operation Files

`{time(auto create)}_create_{table name in lowercase}`

#### Naming Convention for Data Table Modification Operation Files

`{time(auto create)}_modify_{table name in lowercase + specific modification in lowercase}`

### Naming Convention for Data Table Deletion Operation Files

`{time(auto create)}_delete_{table name in lowercase + specific modification in lowercase}`

### Naming Convention for Data Filling Files

`{time(auto create)}_fill_{table name in lowercase + specific modification in lowercase}`
