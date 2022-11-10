# Casbin Access Control Library webman-permission

## Description

It is based on [PHP-Casbin](https://github.com/php-casbin/php-casbin), Functionality provided by、Efficient open source access control framework，and can`ACL`, `RBAC`, `ABAC`and other access control models。
  
## Project address

https://github.com/Tinywan/webman-permission
  
## Install
 
```php
composer require tinywan/webman-permission
```
> String or PHP 7.1+ 和 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)，Official Handbook：https://www.workerman.net/doc/webman#/db/others

## Configure

### Register Service
Create a new configuration file `config/bootstrap.php` with content similar to the following：
  
```php
    // ...
    webman\permission\Permission::class,
```
### Model configuration file 

Create a new configuration file `config/casbin-basic-model.conf` with contents similar to the following：
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
### Policy configuration file

Create a new configuration file `config/permission.php` that looks like this：
```php
<?php

return [
    /*
     *Default  Permission
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model Setup
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // adapter .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Database Settings.
            */
            'database' => [
                // Database connection name, leave as default configuration.
                'connection' => '',
                // Policy table name (without table prefix)）
                'rules_name' => 'rule',
                // Full name of policy table.
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

You can check if the user has such permission

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permit eve to edit articles
} else {
    // deny the request, show an error
}
````

## Authorization Middleware

Create the file `app/middleware/AuthorizationMiddleware.php` (if the directory does not exist, please create it yourself) as follows：
```php
<?php

/**
 * Authorization Middleware
 * @author ShaoBo Wan (Tinywan)
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
				throw new \Exception('Sorry, you do not have access to this interface');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Authorization Exception' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

Add the global middleware to `config/middleware.php` as follows：

```php
return [
    // Global Middleware
    '' => [
        // ... Omit other middleware here
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Thanks

[Casbin](https://github.com/php-casbin/php-casbin)，You can view the full document in its  [official website](https://casbin.org/) 上。

## License

This project is licensed under the [Apache 2.0 license](LICENSE).