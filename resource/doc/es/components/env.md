# webman

## Descripción
`vlucas/phpdotenv` es un componente de carga de variables de entorno que se utiliza para distinguir la configuración de diferentes entornos (como desarrollo, prueba, etc.).

## Repositorio

https://github.com/vlucas/phpdotenv
  
## Instalación
 
```php
composer require vlucas/phpdotenv
 ```
  
## Uso

#### Crear un archivo `.env` en el directorio raíz del proyecto
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modificar el archivo de configuración
**config/database.php**
```php
return [
    // Base de datos por defecto
    'default' => 'mysql',

    // Configuraciones de diversas bases de datos
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

> **Nota**
> Se recomienda agregar el archivo `.env` a la lista de `.gitignore` para evitar enviarlo al repositorio de código. Agregar un archivo de ejemplo de configuración `.env.example` al repositorio, y al implementar el proyecto, copiar `.env.example` a `.env` y modificar la configuración en `.env` según el entorno actual. De esta manera, el proyecto puede cargar diferentes configuraciones en diferentes entornos.

> **Aviso**
> `vlucas/phpdotenv` puede tener errores en la versión PHP TS (Thread Safe). Se recomienda usar la versión NTS (Non-Thread Safe). Para verificar la versión actual de PHP, ejecute `php -v`.

## Más información

Visita https://github.com/vlucas/phpdotenv
