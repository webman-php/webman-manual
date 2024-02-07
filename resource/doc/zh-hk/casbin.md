# Casbin 訪問控制庫 webman-permission

## 說明

它基於 [PHP-Casbin](https://github.com/php-casbin/php-casbin)，一個強大的、高效的開源訪問控制框架，支持基於`ACL`、`RBAC`、`ABAC`等訪問控制模型。

## 專案地址

https://github.com/Tinywan/webman-permission

## 安裝

```php
composer require tinywan/webman-permission
```
> 該擴展需要 PHP 7.1+ 和 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)，官方手冊：https://www.workerman.net/doc/webman#/db/others

## 配置

### 註冊服務
新建配置文件 `config/bootstrap.php` 內容類似如下：

```php
    // ...
    webman\permission\Permission::class,
```

### Model 配置文件 

新建配置文件 `config/casbin-basic-model.conf` 內容類似如下：
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

新建配置文件 `config/permission.php` 內容類似如下：
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
            * Model 設置
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // 适配器 .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

	    /*
            * 數據庫設置.
            */
            'database' => [
                // 數據庫連接名稱，不填為默認配置.
                'connection' => '',
                // 策略表名（不含表前綴）
                'rules_name' => 'rule',
                // 策略表完整名稱.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## 快速開始

```php
use webman\permission\Permission;

// adds permissions to a user
Permission::addPermissionForUser('eve', 'articles', 'read');
// adds a role for a user.
Permission::addRoleForUser('eve', 'writer');
// adds permissions to a rule
Permission::addPolicy('writer', 'articles','edit');
```

您可以檢查用户是否具有這樣的權限

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // permit eve to edit articles
} else {
    // deny the request, show an error
}
````

## 授權中間件

創建文件 `app/middleware/AuthorizationMiddleware.php` (如目錄不存在請自行創建) 如下：
```php
<?php

/**
 * 授權中間件
 * @作者 ShaoBo Wan (Tinywan)
 * @日期時間 2021/09/07 14:15
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
				throw new \Exception('對不起，您沒有該接口訪問權限');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('授權異常' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

在 `config/middleware.php` 中添加全局中間件如下：

```php
return [
    // 全局中間件
    '' => [
        // ... 這裡省略其他中間件
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## 感謝

[Casbin](https://github.com/php-casbin/php-casbin)，你可以查看全部文檔在其 [官網](https://casbin.org/) 上。

## License

This project is licensed under the [Apache 2.0 license](LICENSE).
