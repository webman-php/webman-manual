# 打包

例如打包foo应用插件

* 设置`plugin/foo/config/app.php`里版本号(**重要**)
* 删除`plugin/foo`里不需要打包的文件，尤其是`plugin/foo/public`下测试上传功能的临时文件
* 删除数据库、Redis配置，如果你的项目有自己独立的数据库、Redis配置，这些配置应该是在首次访问应用时触发安装引导程序(需要自行实现)，让管理员手动填写并生成。
* 恢复其它需要恢复原貌的文件
* 完成以上操作后进入`{主项目}/plugin/`目录，使用命令 `zip -r foo.zip foo` 生成foo.zip
