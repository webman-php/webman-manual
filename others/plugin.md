# 插件
> **注意**
> 插件需要webman>=1.2.0 并且 webman-framework>=1.2.0

webman从1.2开始支持插件功能，并支持插件的自动安装和卸载。

### 说明
当有多个项目时，我们会发现这些项目中有部分功能是通用的。例如跨域中间件、ARMS链路追踪中间件、自定义socket推送进程、监控后台、系统管理后台等、AOP切片。这些通用代码完全可以复用起来做成插件，这样其它开发者只需要`composer require 插件名字`，就能自动安装使用了。不需要使用时直接`composer remove 插件名字` 就可以自动卸载。

### 插件生成及发布流程

**安装`webman/console`命令行工具**

`composer require webman/console`

**创建插件**

假设创建的插件名字叫 `foo/admin` (名称也就是后面composer要发布的项目名，名称需要小写)
运行命令
`./webman plugin:create --name=foo/admin`

创建插件后会生成目录 `vendor/foo/admin` 用于存放插件相关文件 和 `config/plugin/foo/admin` 用于存放插件相关配置。

> 注意
> `config/plugin/foo/admin` 支持以下配置，app.php插件主配置，bootstrap.php 进程启动配置，route.php 路由配置，middleware.php 中间件配置，process.php 自定义进程配置，database.php数据库配置，redis.php redis配置，thinkorm.php thinkorm配置。配置格式与webman相同，这些配置会自动被webman识别合并到主配置当中。

**导出插件**

当我们开发完插件后，执行以下命令导出插件
`./webman plugin:export --name=foo/admin`
导出

> 说明
> 导出后会将config/plugin/foo/admin目录拷贝到vendor/foo/admin/src下，并在这个目录下自动生成一个Install.php，Install.php用于自动安装和自动卸载时执行一些操作。
> 安装默认操作是将 vendor/foo/admin/src 下的配置拷贝到当前项目config/plugin下
> 移除时默认操作是将 当前项目config/plugin 下的配置文件删除
> 你可以修改Install.php以便在安装和卸载插件时做一些自定义操作。


## 其它说明待补充...
