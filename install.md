# 安装

## composer安装

**1、设置阿里云composer代理**

由于国内访问composer比较慢，建议设置阿里云composer镜像，运行如下命令设置阿里云代理

`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`

**2、下载webman**

`git clone https://github.com/walkor/webman`


**3、执行安装**

进入webman目录，运行

`composer install`

**4、运行**

debug方式运行(用于开发环境)
 
`php start.php start`

daemon方式运行(用于正式环境)

`php start.php start -d`

**5、访问**

浏览器访问 `http://ip地址:8787`

