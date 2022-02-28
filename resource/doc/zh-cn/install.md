# 安装

## composer安装

**1、设置阿里云composer代理**

由于国内访问composer比较慢，建议设置阿里云composer镜像，运行如下命令设置阿里云代理

`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`

**2、创建项目**

`composer create-project workerman/webman`

**3、运行**  

进入webman目录   

debug方式运行(用于开发调试)
 
`php start.php start`

daemon方式运行(用于正式环境)

`php start.php start -d`

> **注意**
> webman从1.2.3版本开始专门为windows系统提供了启动脚本(需要为php配置好环境变量)，windows用户请双击windows.bat即可启动webman，或者运行 `php windows.php`启动webman。

**4、访问**

浏览器访问 `http://ip地址:8787`


