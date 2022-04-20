# 插件生成及发布流程

## 原理
1、以跨域插件为例，插件分为三部分，一个是跨域中间件程序文件，一个是中间件配置文件middleware.php，还有一个是通过命令自动生成的Install.php。
2、我们使用命令将三个文件打包并发布到composer。
3、当用户使用composer安装跨域插件时，插件中的Install.php会将跨域中间件程序文件以及配置文件拷贝到正确的位置，让webman加载。实现了跨域中间件文件自动配置生效。
4、当用户使用composer删除该插件时，Install.php会删除相应的跨域中间件程序文件和配置文件。实现插件自动卸载。

## 规范
1、插件名由两部分组成，`厂商`和`插件名`，例如 `webman/push`，这个与composer包名对应。
2、插件配置文件统一放在 `config/plugin/厂商/插件名/` 下(console命令会自动创建配置目录)。如果插件不需要配置，则需要删除自动创建的配置目录。
3、插件配置目录仅支持 app.php插件主配置，bootstrap.php 进程启动配置，route.php 路由配置，middleware.php 中间件配置，process.php 自定义进程配置，database.php数据库配置，redis.php redis配置，thinkorm.php thinkorm配置。这些配置会自动被webman识别。
4、插件使用以下方法获取配置`config('plugin.厂商.插件名.配置文件.具体配置项');`，例如`config('plugin.webman.push.app.app_key')`
5、插件如果有自己的数据库配置，则通过以下方式访问。`illuminate/database`为`Db::connection('plugin.厂商.插件名.具体的连接')`，`thinkrom`为`Db::connct('plugin.厂商.插件名.具体的连接')`
6、如果插件需要在`app/`目录下放置业务文件，需要确保不与用户项目以及其它插件冲突。
7、插件应该尽量避免向主项目拷贝文件或目录，例如跨域插件除了配置文件需要拷贝到主项目，中间件文件应该放在`vendor/webman/cros/src`下，不必拷贝到主项目。
8、插件命名空间建议使用大写，例如 Webman/Console。

## 示例

**安装`webman/console`命令行**

`composer require webman/console`

**创建插件**

假设创建的插件名字叫 `foo/admin` (名称也就是后面composer要发布的项目名，名称需要小写)
运行命令
`./webman plugin:create --name=foo/admin`

创建插件后会生成目录 `vendor/foo/admin` 用于存放插件相关文件 和 `config/plugin/foo/admin` 用于存放插件相关配置。

> 注意
> `config/plugin/foo/admin` 支持以下配置，app.php插件主配置，bootstrap.php 进程启动配置，route.php 路由配置，middleware.php 中间件配置，process.php 自定义进程配置，database.php数据库配置，redis.php redis配置，thinkorm.php thinkorm配置。配置格式与webman相同，这些配置会自动被webman识别合并到配置当中。
使用时以 `plugin` 为前缀访问，例如 config('plugin.foo.admin.app');


**导出插件**

当我们开发完插件后，执行以下命令导出插件
`./webman plugin:export --name=foo/admin`
导出

> 说明
> 导出后会将config/plugin/foo/admin目录拷贝到vendor/foo/admin/src下，同时自动生成一个Install.php，Install.php用于自动安装和自动卸载时执行一些操作。
> 安装默认操作是将 vendor/foo/admin/src 下的配置拷贝到当前项目config/plugin下
> 移除时默认操作是将 当前项目config/plugin 下的配置文件删除
> 你可以修改Install.php以便在安装和卸载插件时做一些自定义操作。



