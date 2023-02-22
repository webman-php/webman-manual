# phar打包

phar是PHP里类似于JAR的一种打包文件，你可以利用phar将你的webman项目打包成单个phar文件，方便部署。

**这里非常感谢 [fuzqing](https://github.com/fuzqing) 的PR.**

> **注意**
> 需要关闭`php.ini`的phar配置选项，既设置 `phar.readonly = 0`

## 安装命令行工具
`composer require webman/console`

## 配置设置
打开 `config/plugin/webman/console/app.php` 文件，设置 `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`，用户打包时排除一些无用的目录及文件，避免打包体积过大

## 打包
在webman项目根目录执行命令 `php webman phar:pack`
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

