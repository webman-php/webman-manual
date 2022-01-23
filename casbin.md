# Casbin 访问控制库 webman-permission

## 说明

它基于 [PHP-Casbin](https://github.com/php-casbin/php-casbin), 一个强大的、高效的开源访问控制框架，支持基于`ACL`, `RBAC`, `ABAC`等访问控制模型。
  
## 项目地址

https://github.com/Tinywan/webman-permission
  
## 安装
 
```php
composer require tinywan/webman-permission
```
> 该扩展需要 PHP 7.1+ 和 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)，官方手册：https://www.workerman.net/doc/webman#/db/others

## 配置

### 注册服务
新建配置文件 `config/bootstrap.php` 内容类似如下：
  
```php
    // ...
    webman\permission\Permission::class,
```
### Model 配置文件 

新建配置文件 `config/casbin-basic-model.conf` 内容类似如下：
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
### Policy 配置文件

新建配置文件 `config/permission.php` 内容类似如下：
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
            * Model 设置
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // 适配器 .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * 数据库设置.
            */
            'database' => [
                // 数据库连接名称，不填为默认配置.
                'connection' => '',
                // 策略表名（不含表前缀）
                'rules_name' => 'rule',
                // 策略表完整名称.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## 快速开始

```php
use webman\permission\Permission;

// adds permissions to a user
Permission::addPermissionForUser('eve', 'articles', 'read');
// adds a role for a user.
Permission::addRoleForUser('eve', 'writer');
// adds permissions to a rule
Permission::addPolicy('writer', 'articles','edit');
```

您可以检查用户是否具有这样的权限

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permit eve to edit articles
} else {
    // deny the request, show an error
}
````

## 授权中间件

创建文件 `app/middleware/AuthorizationMiddleware.php` (如目录不存在请自行创建) 如下：
```php
<?php

/**
 * 授权中间件
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
				throw new \Exception('对不起，您没有该接口访问权限');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('授权异常' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

在 `config/middleware.php` 中添加全局中间件如下：

```php
return [
    // 全局中间件
    '' => [
        // ... 这里省略其它中间件
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## 感谢

[Casbin](https://github.com/php-casbin/php-casbin)，你可以查看全部文档在其 [官网](https://casbin.org/) 上。

## License

This project is licensed under the [Apache 2.0 license](LICENSE).