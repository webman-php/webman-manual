# vlucas/phpdotenv

## Descripción
`vlucas/phpdotenv` es un componente de carga de variables de entorno que se utiliza para diferenciar la configuración en diferentes entornos (como desarrollo, pruebas, etc.).

## Repositorio del proyecto
https://github.com/vlucas/phpdotenv

## Instalación
```php
composer require vlucas/phpdotenv
```

## Uso

#### Crear un archivo `.env` en la raíz del proyecto
**.env**
```plaintext
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

    // Configuraciones de diferentes bases de datos
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
> Se recomienda agregar el archivo `.env` a la lista de `.gitignore` para evitar su inclusión en el repositorio de código. Es recomendable añadir un archivo de ejemplo de configuración, por ejemplo `.env.example`, y al momento de implementar el proyecto, copiar el archivo `.env.example` como `.env` y modificar la configuración en el archivo `.env` según el entorno actual. De esta manera, el proyecto puede cargar diferentes configuraciones en entornos distintos.

> **Atención**
> `vlucas/phpdotenv` puede presentar errores en la versión de PHP TS (Thread Safe). Se recomienda usar la versión NTS (Non Thread Safe). Puede verificar la versión actual de PHP ejecutando `php -v`.

## Más información
Visita https://github.com/vlucas/phpdotenv
