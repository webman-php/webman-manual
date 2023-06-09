# 安装

应用插件安装有两种方式：

## 在插件市场安装
进入 [官方管理后台webman-admin](https://www.workerman.net/plugin/82) 的应用插件页点击安装按钮安装对应的应用插件。  

## 源码包安装
从应用市场下载应用插件压缩包，解压并将解压目录上传到`{主项目}/plugin/`目录下(如plugin目录不存在需要手动创建)，执行 `php webman app-plugin:install 插件名`完成安装。

例如下载的压缩包名称未ai.zip，解压到 `{主项目}/plugin/ai`，执行`php webman app-plugin:install ai` 完成安装。


# 卸载

同样应用插件卸载也有两种方式：

## 在插件市场卸载
进入 [官方管理后台webman-admin](https://www.workerman.net/plugin/82) 的应用插件页点击卸载按钮卸载对应的应用插件。  

## 源码包安装
执行 `php webman app-plugin:uninstall 插件名`完成卸载，执行完后手动删除`{主项目}/plugin/`目录下对应的插件目录
