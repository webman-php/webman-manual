# Herramienta de migración de base de datos Phinx

## Descripción

Phinx permite a los desarrolladores modificar y mantener la base de datos de forma concisa. Evita la escritura manual de declaraciones SQL, utilizando una potente API de PHP para gestionar la migración de la base de datos. Los desarrolladores pueden utilizar el control de versiones para gestionar sus migraciones de base de datos. Phinx facilita la migración de datos entre bases de datos diferentes. También puede rastrear qué scripts de migración se han ejecutado, permitiendo a los desarrolladores centrarse en cómo escribir sistemas de mejor calidad en lugar de preocuparse por el estado de la base de datos.

## Dirección del Proyecto

https://github.com/cakephp/phinx

## Instalación

```php
composer require robmorgan/phinx
```

## Documentación Oficial en Chino

Para obtener información detallada, consulte la documentación oficial en chino. Aquí solo se explica cómo configurar y usar en webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Estructura de Directorios de Archivos de Migración

``` 
.
├── app                           Directorio de la aplicación
│   ├── controller                Directorio de controladores
│   │   └── Index.php             Controlador
│   ├── model                     Directorio de modelos
......
├── database                      Archivos de la base de datos
│   ├── migrations                Archivos de migración
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Datos de prueba
│   │   └── UserSeeder.php
......
```

## Configuración phinx.php

Cree un archivo phinx.php en el directorio raíz del proyecto.

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

## Sugerencias de Uso

Una vez fusionado el código de los archivos de migración, no se permite modificarlos nuevamente. Si surge algún problema, se debe crear un nuevo archivo de operación o eliminarlo.

#### Reglas de Nomenclatura de Archivos de Operaciones para Crear Tablas

`{tiempo(creado automáticamente)}_create_{nombre de tabla en minúsculas en inglés}`

#### Reglas de Nomenclatura de Archivos de Operaciones para Modificar Tablas

`{tiempo(creado automáticamente)}_modify_{nombre de tabla en minúsculas en inglés + elemento específico a modificar en minúsculas en inglés}`

### Reglas de Nomenclatura de Archivos de Operaciones para Eliminar Tablas

`{tiempo(creado automáticamente)}_delete_{nombre de tabla en minúsculas en inglés + elemento específico a modificar en minúsculas en inglés}`

### Reglas de Nomenclatura de Archivos de Rellenar Datos

`{tiempo(creado automáticamente)}_fill_{nombre de tabla en minúsculas en inglés + elemento específico a modificar en minúsculas en inglés}`
