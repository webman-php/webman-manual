# 目录结构

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

我们看到一个应用插件有着与webman相同的目录结构及配置文件，实际上开发体验与开发webman普通应用基本没有区别

## 命名空间
插件目录及命名遵循PSR4规范，因为插件都放置于plugin目录下，所以命名空间都以plugin开头，例如`plugin\foo\app\controller\UserController`。

## url访问
应用插件url地址路径都以`/app`开头，例如`plugin\foo\app\controller\UserController`url地址是 `http://127.0.0.1:8787/app/foo/user`。

## 静态文件
静态文件放置于`plugin/{插件}/public`下，例如访问`http://127.0.0.1:8787/app/foo/avatar.png`实际上是获取`plugin/foo/public/avatar.png`文件。
