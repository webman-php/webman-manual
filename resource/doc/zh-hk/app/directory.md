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
    ├── public
    └── api
```

我们看到一个应用插件有着与webman相同的目录结构及配置文件，实际上开发体验与开发webman普通应用基本没有区别
插件目录及命名遵循PSR4规范，因为插件都放置于plugin目录下，所以命名空间都以plugin开头，例如`plugin\foo\app\controller\UserController`。

## 关于 api 目录
每个插件里有一个api目录，如果你的应用提供了一些内部接口给其它应用调用，需要将接口放在api目录。
注意，这里所说的接口是函数调用的接口，非网络调用的接口。
例如`邮件插件`在 `plugin/email/api/Email.php` 提供了一个`Email::send()`接口，用于给其它应用调用发邮件。
另外 plugin/email/api/Install.php 是自动生成的，用来让webman-admin插件市场调用执行安装或卸载操作。
