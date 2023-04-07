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
插件目录及命名遵循PSR4规范，因为插件都放置于plugin目录下，所以命名空间都以plugin开头，例如`plugin\foo\app\controller\UserController`。