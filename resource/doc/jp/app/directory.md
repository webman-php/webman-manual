# ディレクトリ構造

```plaintext
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

私たちは、アプリケーションプラグインがwebmanと同じディレクトリ構造と設定ファイルを持っていることに気づきました。実際、プラグインの開発体験はwebmanの通常のアプリケーションの開発とほとんど違いがありません。
プラグインのディレクトリと命名はPSR4規格に従います。プラグインはpluginディレクトリに配置されているため、名前空間はすべてpluginで始まります。例えば、`plugin\foo\app\controller\UserController`のようなものです。

## apiディレクトリについて
各プラグインにはapiディレクトリがあり、アプリケーションが他のアプリケーションから呼び出されるいくつかの内部インターフェースを提供する場合は、そのインターフェースをapiディレクトリに配置する必要があります。
ここで言及しているインターフェースは、関数呼び出しのインターフェースであり、ネットワーク呼び出しのインターフェースではありません。
たとえば、`Emailプラグイン`は `plugin/email/api/Email.php` に `Email::send()`インターフェースを提供しており、他のアプリケーションからメールを送信するために呼び出されます。
また、plugin/email/api/Install.php は自動生成されたもので、webman-adminプラグインマーケットからのインストールまたはアンインストール操作を実行するために使用されます。
