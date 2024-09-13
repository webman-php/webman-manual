# 编程须知

## 操作系统
webman同时支持linux系统和windows系统下运行。但是由于workerman在windows下无法支持多进程设置以及守护进程，因此windows系统仅仅建议用于开发环境开发调试使用，正式环境请使用linux系统。

## 启动方式
#### linux系统
```
php start.php start
```
此命令为调试模式，一般在开发时使用，文件更新monitor进程会自动检测reload，打印数据时在终端显示，终端关闭后webman也会自动关闭。

```
php start.php start -d
```
此命令为守护进程模式，在正式环境使用，关闭终端后webman继续运行，打印数据终端无显示。
更新代码需要执行`php start.php reload` 或者 `php start.php restart -d`更新。

#### windows系统
执行`windows.bat`或者使用命令 `php windows.php` 启动，按ctrl c 停止。
windows系统不支持stop reload status reload connections等命令。
windows不支持守护进程模式。

## 常驻内存
webman是常驻内存的框架，一般来说，php文件载入后便以opcode的方式常驻内存，不会再次从磁盘读取(模版文件除外)。
所以正式环境业务代码或配置变更后需要执行`php start.php reload`才能生效。
如果是更改进程相关配置或者安装了新的composer包需要重启`php start.php restart`。

> 为了方便开发，webman自带一个monitor自定义进程用于监控业务文件更新，当有业务文件更新时会自动执行reload。
> 此功能只在workerman以调试模式运行(启动时不加`-d`)才启用。windows用户需要执行`windows.bat`或者`php windows.php`才能启用。

## 关于输出语句
在传统php-fpm项目里，使用`echo` `var_dump`等函数输出数据会直接显示在页面里，而在webman开发过程中(调试模式启动时)，这些输出往往显示在终端上，并不会显示在页面中(模版文件中的输出除外)。

## 不要执行`exit` `die`语句
执行die或者exit会使得进程退出并重启，导致当前请求无法被正确响应。

## 不要执行`pcntl_fork`函数
`pcntl_fork`用户创建一个进程，这在webman中是不允许的。
