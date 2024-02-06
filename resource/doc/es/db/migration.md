# Herramienta de migración de bases de datos Phinx

## Descripción

Phinx permite a los desarrolladores modificar y mantener bases de datos de manera concisa. Evita la escritura manual de instrucciones SQL mediante el uso de una potente API de PHP para gestionar las migraciones de la base de datos. Los desarrolladores pueden utilizar el control de versiones para gestionar sus migraciones de base de datos. Phinx facilita la migración de datos entre diferentes bases de datos. También permite hacer un seguimiento de qué scripts de migración se han ejecutado, lo que permite a los desarrolladores centrarse en cómo escribir un sistema mejor en lugar de preocuparse por el estado de la base de datos.

## Repositorio del proyecto

https://github.com/cakephp/phinx

## Instalación

```php
composer require robmorgan/phinx
```

## Documentación oficial en chino

Para obtener información detallada, consulte la documentación oficial en chino. Aquí solo se explicará cómo configurar y utilizar Phinx en webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Estructura de directorios de archivos de migración

```
.
├── app                           Directorio de la aplicación
│   ├── controller                Directorio de controladores
│   │   └── Index.php             Controlador
│   ├── model                     Directorio de modelos
......
├── database                      Archivos de base de datos
│   ├── migrations                Archivos de migración
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Datos de prueba
│   │   └── UserSeeder.php
......
```

## Configuración de phinx.php

Crear el archivo phinx.php en el directorio raíz del proyecto

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

## Recomendaciones de uso

Una vez que se fusiona el código de los archivos de migración, no se permite modificarlo nuevamente. Si aparece un problema, es necesario crear un nuevo archivo de modificación o eliminación para solucionarlo.

#### Reglas de nombramiento para archivos de creación de tablas

`{hora(creación automática)}_create_{nombre de la tabla en minúsculas en inglés}`

#### Reglas de nombramiento para archivos de modificación de tablas

`{hora(creación automática)}_modify_{nombre de la tabla en minúsculas en inglés + nombre específico de la modificación en minúsculas en inglés}`

#### Reglas de nombramiento para archivos de eliminación de tablas

`{hora(creación automática)}_delete_{nombre de la tabla en minúsculas en inglés + nombre específico de la modificación en minúsculas en inglés}`

#### Reglas de nombramiento para archivos de relleno de datos

`{hora(creación automática)}_fill_{nombre de la tabla en minúsculas en inglés + nombre específico de la modificación en minúsculas en inglés}`
