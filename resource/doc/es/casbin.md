# Biblioteca de control de acceso Casbin webman-permission

## Descripción

Esto está basado en [PHP-Casbin](https://github.com/php-casbin/php-casbin), un marco de control de acceso de código abierto poderoso y eficiente que soporta modelos de control de acceso como `ACL`, `RBAC`, `ABAC`, entre otros.

## Dirección del proyecto

https://github.com/Tinywan/webman-permission

## Instalación

```php
composer require tinywan/webman-permission
```
> Esta extensión requiere PHP 7.1+ y [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998). Consulte el manual oficial en https://www.workerman.net/doc/webman#/db/others

## Configuración

### Registración del servicio
Cree el archivo de configuración `config/bootstrap.php` con el siguiente contenido:

```php
    // ...
    webman\permission\Permission::class,
```
### Archivo de configuración del modelo

Cree el archivo de configuración `config/casbin-basic-model.conf` con el siguiente contenido:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```
### Archivo de configuración de la política

Cree el archivo de configuración `config/permission.php` con el siguiente contenido:

```php
<?php

return [
    /*
     * Permiso predeterminado
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Configuración del modelo
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adaptador
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Configuración de la base de datos
            */
            'database' => [
                // Nombre de la conexión de la base de datos; dejar en blanco para usar la configuración predeterminada
                'connection' => '',
                // Nombre de la tabla de políticas (sin prefijo de tabla)
                'rules_name' => 'rule',
                // Nombre completo de la tabla de políticas
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## Inicio rápido

```php
use webman\permission\Permission;

// Agregar permisos a un usuario
Permission::addPermissionForUser('eve', 'articles', 'read');
// Agregar un rol a un usuario
Permission::addRoleForUser('eve', 'writer');
// Agregar permisos a una regla
Permission::addPolicy('writer', 'articles','edit');
```

Puede verificar si un usuario tiene dichos permisos

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permitir a eve editar artículos
} else {
    // denegar la solicitud, mostrar un error
}
````

## Middleware de autorización

Cree el archivo `app/middleware/AuthorizationMiddleware.php` (si el directorio no existe, créelo) de la siguiente manera:

```php
<?php

/**
 * Middleware de autorización
 * por ShaoBo Wan (Tinywan)
 * en 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $uri = $request->path();
        try {
            $userId = 10086;
            $action = $request->method();
            if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
                throw new \Exception('Lo siento, no tienes permiso para acceder a esta API');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('Excepción de autorización' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

Agregue el middleware global en `config/middleware.php` de la siguiente manera:

```php
return [
    // Middleware global
    '' => [
        // ... otros middlewares omitidos
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Agradecimientos

[Casbin](https://github.com/php-casbin/php-casbin), puedes ver toda la documentación en su [web oficial](https://casbin.org/).

## Licencia

Este proyecto está licenciado bajo la [licencia Apache 2.0](LICENSE).
