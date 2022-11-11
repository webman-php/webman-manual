# MigrationDatabase Migration Tool Phinx

## Description

Phinx  allows developers to succinctly modify and maintain the database. It avoids the need to write SQL statements by hand, and it uses the powerful PHP API to manage database migrations. Developers can manage their database migrations using version control. Phinx makes it easy to migrate data between different databases. With the ability to track which migration scripts are executed, developers can stop worrying about the state of the database and focus on writing a better system。
  
## Project address

https://github.com/cakephp/phinx

## Install
 
  ```php
  composer require robmorgan/phinx
  ```
  
## Official Chinese Documentation Address

You can read the official Chinese documentation for details, here is only how to configure the use in webman

https://tsy12321.gitbooks.io/phinx-doc/content/

## Migrate file directory structure

```
.
├── app                           Application Catalog
│   ├── controller                Controller Directory
│   │   └── Index.php             controller
│   ├── model                     Model Catalog
......
├── database                      Database file
│   ├── migrations                Migration files
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Test data
│   │   └── UserSeeder.php
......
```

## phinx.php Configure

Create phinx.php file in the project root

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

## Usage suggestions

Migration files are not allowed to be modified again once the code is merged, and problems must be handled by creating new changes or deleting the operation file。

#### Data table creation operation file naming rules

`{time(auto create)}_create_{Table name in English lowercase}`

#### The data table modifies the operation file naming rules

`{time(auto create)}_modify_{Table name in English lowercase + specific modifications in English lowercase}`

### Data table deletion operation file naming rules

`{time(auto create)}_delete_{Table name in English lowercase + specific modifications in English lowercase}`

### Populate data file naming rules

`{time(auto create)}_fill_{Table name in English lowercase + specific modifications in English lowercase}`




