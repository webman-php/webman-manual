# 创建应用插件

## 唯一标识

每个插件都有一个唯一的应用标识，开发者开发之前需要想好标识，并且检测标识没有被占用。
检测地址[应用标识检测](https://www.workerman.net/app/check)

## 创建

执行`composer require webman/console` 安装webman命令行

使用命令`php webman app-plugin:create {插件标识}`可以在本地创建一个应用插件

例如 `php webman app-plugin:create foo`

重启webman

访问 `http://127.0.0.1:8787/app/foo` 如果有返回内容说明创建成功。
