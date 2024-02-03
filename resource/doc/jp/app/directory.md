# ディレクトリ構造

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

私たちは、アプリケーションプラグインがwebmanと同じディレクトリ構造と設定ファイルを持っていることを見ています。実際に、プラグイン開発の体験は、通常のwebmanアプリケーションの開発とほとんど変わりません。
プラグインディレクトリおよび名前付けはPSR4仕様に従っており、プラグインはすべてpluginディレクトリに配置されているため、名前空間はすべてpluginで始まります。例えば、`plugin\foo\app\controller\UserController`のようです。

## apiディレクトリについて
各プラグインにはapiディレクトリがあり、アプリケーションが他のアプリケーションから呼び出される内部APIを提供する場合、インターフェースをapiディレクトリに配置する必要があります。
ここで言うインターフェースとは、ネットワーク呼び出しではなく、関数呼び出しのインターフェースです。
例えば、「メールプラグイン」は `plugin/email/api/Email.php` に `Email::send()` インターフェースを提供し、他のアプリケーションがメールを送信するために呼び出すことができます。
また、`plugin/email/api/Install.php` は自動生成されており、webman-adminプラグインマーケットがインストールまたはアンインストール操作を実行するために呼び出すために使用されます。
