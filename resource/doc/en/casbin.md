# Casbin Access Control Library webman-permission

## Description

It is based on [PHP-Casbin](https://github.com/php-casbin/php-casbin), a powerful and efficient open-source access control framework that supports access control models such as `ACL`, `RBAC`, `ABAC`, etc.

## Project Address

https://github.com/Tinywan/webman-permission

## Installation

```php
composer require tinywan/webman-permission
```
> This extension requires PHP 7.1+ and [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998). Official documentation: https://www.workerman.net/doc/webman#/db/others

## Configuration

### Register Service
Create a configuration file `config/bootstrap.php` with similar content:

```php
    // ...
    webman\permission\Permission::class,
```

### Model Configuration File

Create a configuration file `config/casbin-basic-model.conf` with similar content:

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

### Policy Configuration File

Create a configuration file `config/permission.php` with similar content:

```php
<?php

return [
    /*
     * Default Permission
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model Setting
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adapter
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Database Setting
            */
            'database' => [
                // Database connection name, leave blank for default configuration.
                'connection' => '',
                // Policy table name (without table prefix)
                'rules_name' => 'rule',
                // Full name of the policy table.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Quick Start

```php
use webman\permission\Permission;

// adds permissions to a user
Permission::addPermissionForUser('eve', 'articles', 'read');
// adds a role for a user.
Permission::addRoleForUser('eve', 'writer');
// adds permissions to a rule
Permission::addPolicy('writer', 'articles','edit');
```

You can check if a user has such permission

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permit eve to edit articles
} else {
    // deny the request, show an error
}
````

## Authorization Middleware

Create file `app/middleware/AuthorizationMiddleware.php` (create the directory if it does not exist) as follows:

```php
<?php

/**
 * Authorization Middleware
 * @datetime 2021/09/07 14:15
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
				throw new \Exception('Sorry, you do not have permission to access this interface');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Authorization exception' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

Add the global middleware in `config/middleware.php` as follows:

```php
return [
    // Global Middleware
    '' => [
        // ... Other middleware omitted here
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Credits

[Casbin](https://github.com/php-casbin/php-casbin), you can view the complete documentation on its [official website](https://casbin.org/).

## License

This project is licensed under the [Apache 2.0 license](LICENSE).
