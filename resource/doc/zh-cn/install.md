# 安装

## composer安装


**1、创建项目**

`composer create-project workerman/webman`

**2、运行**  

进入webman目录   

debug方式运行(用于开发调试)
 
`php start.php start`

daemon方式运行(用于正式环境)

`php start.php start -d`

> **注意**
> webman从1.2.3版本开始专门为windows系统提供了启动脚本(需要为php配置好环境变量)，windows用户请双击windows.bat即可启动webman，或者运行 `php windows.php`启动webman。

**3、访问**

浏览器访问 `http://ip地址:8787`


