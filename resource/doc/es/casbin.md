# Librería de control de acceso Casbin webman-permission

## Descripción

Está basado en [PHP-Casbin](https://github.com/php-casbin/php-casbin), un marco de control de acceso de código abierto potente y eficiente que admite modelos de control de acceso como `ACL`, `RBAC`, `ABAC`, entre otros.

## Dirección del proyecto

https://github.com/Tinywan/webman-permission

## Instalación

```php
composer require tinywan/webman-permission
```
> Esta extensión requiere PHP 7.1+ y [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998). Consulte la documentación oficial en: https://www.workerman.net/doc/webman#/db/others

## Configuración

### Registro del servicio
Cree un archivo de configuración `config/bootstrap.php` con un contenido similar a este:

```php
// ...
webman\permission\Permission::class,
```

### Archivo de configuración del modelo

Cree un archivo de configuración `config/casbin-basic-model.conf` con un contenido similar a este:

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

### Archivo de configuración de políticas

Cree un archivo de configuración `config/permission.php` con un contenido similar a este:

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
                // Nombre de la conexión de la base de datos, dejar en blanco para la configuración predeterminada
                'connection' => '',
                // Nombre de tabla de políticas (sin prefijo de tabla)
                'rules_name' => 'rule',
                // Nombre completo de la tabla de políticas
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Comenzar rápidamente

```php
use webman\permission\Permission;

// Agrega permisos a un usuario
Permission::addPermissionForUser('eve', 'articles', 'read');
// Agrega un rol para un usuario
Permission::addRoleForUser('eve', 'writer');
// Agrega permisos a una regla
Permission::addPolicy('writer', 'articles', 'edit');
```

Puede verificar si un usuario tiene ciertos permisos de la siguiente manera:

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permite a eve editar artículos
} else {
    // niega la solicitud, muestra un error
}
````

## Middleware de autorización

Cree el archivo `app/middleware/AuthorizationMiddleware.php` (si el directorio no existe, créelo) de la siguiente manera:

```php
<?php

/**
 * Middleware de autorización
 * Author ShaoBo Wan (Tinywan)
 * Fecha y hora 2021/09/07 14:15
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
				throw new \Exception('Lo siento, no tienes permiso para acceder a esta interfaz');
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
        // ... otros middleware omitidos aquí
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Agradecimientos

[A Casbin](https://github.com/php-casbin/php-casbin), puedes consultar toda la documentación en su [sitio web](https://casbin.org/).

## Licencia

Este proyecto está bajo la [licencia Apache 2.0](LICENSE).
