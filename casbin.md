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
        // changes whether Lauthz will log messages to the Logger.
        'enabled' => false,
        // Casbin Logger, Supported: \Psr\Log\LoggerInterface|string
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model 设置
            */
            'model' => [
                // 可选值: "file", "text"
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

## 感谢

[Casbin](https://github.com/php-casbin/php-casbin)，你可以查看全部文档在其 [官网](https://casbin.org/) 上。

## License

This project is licensed under the [Apache 2.0 license](LICENSE).