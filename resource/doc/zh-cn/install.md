# 如何安装webman

* PHP >= 8.1
* [Composer](https://getcomposer.org/) >= 2.0


## Linux安装PHP+Composer环境(已有环境请跳过)
```
curl -sO https://www.workerman.net/install-php-and-composer && sudo bash install-php-and-composer
```
> **注意**
> 以上命令适用于Linux/Mac系统，Windows系统需要手动安装

也可手动下载webman官方提供的[静态PHP](https://www.workerman.net/download)，解压即可使用。

## 1. 创建项目

```php
composer create-project workerman/webman:~2.0
```

> **提示**
> 如果报错可能是用了有问题的composer镜像代理，请执行 `composer config -g --unset repos.packagist` 取消代理。

## 2. 运行

进入webman目录   

#### windows用户
双击 `windows.bat` 或者运行 `php windows.php` 启动

> **提示**
> 如果有报错，很可能是有函数被禁用，参考[函数禁用检查](others/disable-function-check.md)解除禁用

#### linux用户
调试方式运行（用于开发调试，打印数据会显示在终端，终端关闭后webman服务也随之关闭）
 
```php
php start.php start
```

守护进程方式运行（用于正式环境，打印数据不会显示在终端，终端关闭后webman服务会持续运行）

```php
php start.php start -d
```

> **提示**
> 如果有报错，很可能是有函数被禁用，参考[函数禁用检查](others/disable-function-check.md)解除禁用

## 3.访问

浏览器访问 `http://ip地址:8787`




