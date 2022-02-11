# phar打包

phar (“Php ARchive”) 是PHP里类似于JAR的一种打包文件，你可以利用phar将你的webman项目打包成一个phar文件，方便部署。

**这里非常感谢 [fuzqing](https://github.com/fuzqing) 的PR.**

> **注意**
> 此特性只支持linux系统
> 此特性需要webman>=1.2.4 webman-framework>=1.2.4 webman/console>=1.0.5

## 安装命令行工具
`composer require webman/console`

## 打包
`./webman phar:pack`
会在bulid目录生成一个`webman.phar`文件。

> 打包相关配置在 `config/plugin/webman/console/app.php` 中

## 启动停止相关命令
**启动**
`php webman.phar start` 或 `php webman.phar start -d`

**停止**
`php webman.phar stop`

**查看状态**
`php webman.phar status`

**查看连接状态**
`php webman.phar connections`

**重启**
`php webman.phar restart` 或 `php webman.phar restart -d`

