# Casbin 訪問控制庫 webman-permission

## 說明

它基於 [PHP-Casbin](https://github.com/php-casbin/php-casbin)，是一個強大且高效的開源訪問控制框架，支援基於 `ACL`、`RBAC`、`ABAC`等訪問控制模型。

## 專案地址

https://github.com/Tinywan/webman-permission

## 安裝

```php
composer require tinywan/webman-permission
```
> 這個擴展需要 PHP 7.1+ 和 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)，官方手冊：https://www.workerman.net/doc/webman#/db/others

## 設定

### 註冊服務
新增設定檔 `config/bootstrap.php` 內容類似如下：

```php
// ...
webman\permission\Permission::class,
```
### Model 設定檔 

新增設定檔 `config/casbin-basic-model.conf` 內容類似如下：
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
### Policy 設定檔

新增設定檔 `config/permission.php` 內容類似如下：
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
            * Model 設定
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // 適配器 .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * 資料庫設定.
            */
            'database' => [
                // 資料庫連線名稱，不填為預設配置.
                'connection' => '',
                // 策略表名稱（不含表前綴）
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
    // 允許 eve 編輯文章
} else {
    // 拒絕請求，顯示錯誤
}
````

## 授權中介軟體

建立檔案 `app/middleware/AuthorizationMiddleware.php`（如果目錄不存在請自行建立），內容如下：
```php
<?php

/**
 * 授權中介軟體
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
				throw new \Exception('對不起，您沒有該接口訪問權限');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('授權異常' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

在 `config/middleware.php` 中添加全域中介軟體如下：

```php
return [
    // 全域中介軟體
    '' => [
        // ... 這裡省略其他中介軟體
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## 感謝

[Casbin](https://github.com/php-casbin/php-casbin)，你可以在其 [官網](https://casbin.org/) 上查看所有文件。

## 授權

本項目根據 [Apache 2.0 許可證](LICENSE) 授權。