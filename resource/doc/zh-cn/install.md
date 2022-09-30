# 安装

webman 要求PHP >= 7.2

## composer安装

**1、去掉composer代理**
`composer config -g --unset repos.packagist`

> **说明**
> 有些composer代理镜像不全(如阿里云)，使用以上命令可删除composer代理

**2、创建项目**

`composer create-project workerman/webman`

**3、运行**  

进入webman目录   

debug方式运行(用于开发调试)
 
`php start.php start`

daemon方式运行(用于正式环境)

`php start.php start -d`

**windows用户用 双击windows.bat 或者运行 `php windows.php` 启动**

**4、访问**

浏览器访问 `http://ip地址:8787`


