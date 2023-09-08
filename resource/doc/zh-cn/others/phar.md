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

## 说明
* 运行webman.phar后会在webman.phar所在目录生成runtime目录，用于存放日志等临时文件。

* 如果你的项目里使用了.env文件，需要将.env文件放在webman.phar所在目录。

* 如果你的业务需要上传文件到public目录，也需要将public目录独立出来放在webman.phar所在目录，这时候需要配置`config/app.php`。
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
业务可以使用助手函数`public_path()`找到实际的public目录位置。

* webman.phar不支持在windows下开启自定义进程

