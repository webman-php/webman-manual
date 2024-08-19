# 打包

例如打包foo应用插件

* 设置`plugin/foo/config/app.php`里版本号(**重要**)
* 删除`plugin/foo`里不需要打包的文件，尤其是`plugin/foo/public`下测试上传功能的临时文件
* 如果你的项目包含数据库建表等操作，需要设置好`plugin/foo/install.sql`，参见[安装数据库部分](database.md#自动导入数据库)
* 如果你的项目有自己独立的数据库、Redis配置，需要先删除这些配置，这些配置应该是在首次访问应用时触发安装引导程序(需要自行实现)，让管理员手动填写并生成。
* 如果你的项目包含webman admin后台菜单，需要设置好 `plugin/foo/config/menu.php`，这样安装插件时会自动设置这些菜单。具体参见[webman-admin导入菜单](https://www.workerman.net/doc/webman-admin/app-development/menu.html)
* 恢复其它需要恢复原貌的文件
* 完成以上操作后进入`{主项目}/plugin/`目录
* 执行命令 `php webman app-plugin:zip foo` 会在plugin下生成一个foo.zip 文件

> **提示**
> 如果`app-plugin:zip`命令不存在，则需要安装或升级webman/console，命令为 `composer require webman/console ^1.3.10`

  **foo.zip为打包后的文件，参考下一章节[发布插件](publish.md)**
