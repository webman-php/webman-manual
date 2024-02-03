# 目錄結構

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

我們可以看到應用插件具有與webman相同的目錄結構和配置文件，事實上，開發插件的經驗與開發webman普通應用基本上沒有區別。
插件目錄和命名遵循PSR4規範，因為插件都放置於plugin目錄下，所以命名空間都以plugin開頭，例如`plugin\foo\app\controller\UserController`。

## 關於 API 目錄
每個插件中都有一個api目錄，如果您的應用程序提供了一些內部接口給其他應用程序調用，需要將接口放在api目錄。
請注意，這裡所說的接口是函數調用的接口，而不是網絡調用的接口。
例如`郵件插件`在 `plugin/email/api/Email.php` 提供了一個`Email::send()`接口，用於供其他應用程序調用發送郵件。
另外，plugin/email/api/Install.php 是自動生成的，用於讓webman-admin插件市場調用執行安裝或卸載操作。