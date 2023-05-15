## 环境需求

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

## composer 安装

#### 1. 去掉composer代理

```php
composer config -g --unset repos.packagist
```

> **说明**：有些composer代理镜像不全（如阿里云），使用以上命令可删除composer代理

**2、创建项目**

```php
composer create-project workerman/webman
```

**3、运行**  

> 进入webman目录   

`debug`方式运行（用于开发调试）
 
```php
php start.php start
```

`daemon`方式运行（用于正式环境）

```php
php start.php start -d
```

**windows用户用 双击windows.bat 或者运行 `php windows.php` 启动**

> **提示**
> 如果有报错，很可能是有函数被禁用，参考[函数禁用检查](others/disable-function-check.md)解除禁用

**4、访问**

浏览器访问 `http://ip地址:8787`


