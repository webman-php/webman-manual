# Casbin 访问控制库 webman-permission

## 说明

Casbin是一个强大的、高效的开源访问控制框架，其权限管理机制支持多种访问控制模型。
  
## 项目地址

https://github.com/Tinywan/webman-permission
  
## 安装
 
```php
composer require tinywan/webman-permission
```
  
## 配置

新建配置文件 `config/bootstrap.php` 内容类似如下：
  
```php
    // ...
    webman\permission\Permission::class,
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

