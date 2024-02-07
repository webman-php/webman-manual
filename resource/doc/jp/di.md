# 依存の自動インジェクション
Webmanにおける依存の自動インジェクションはオプション機能であり、デフォルトでは無効になっています。依存の自動インジェクションが必要な場合、[php-di](https://php-di.org/doc/getting-started.html)を使用することをお勧めします。以下は`php-di`を使用したWebmanの方法です。

## インストール
```composer
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

`config/container.php`を変更し、最終的な内容は以下のようになります：
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php`は`PSR-11`に準拠したコンテナインスタンスを最終的に返します。`php-di`を使用したくない場合、ここで別の`PSR-11`に準拠したコンテナインスタンスを作成して返すことができます。

## コンストラクタインジェクション
`app/service/Mailer.php`（ディレクトリが存在しない場合は自分で作成してください）を作成し、以下の内容を追加します：
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // メール送信コードは省略されています
    }
}
```

`app/controller/UserController.php`の内容は次の通りです：

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
通常の場合、`app\controller\UserController`のインスタンス化には以下のコードが必要です：
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
`php-di`を使用すると、開発者は`UserController`内の`Mailer`を手動でインスタンス化する必要がありません。代わりに、Webmanが自動的に行います。また、`Mailer`のインスタンス化中に他のクラスの依存関係がある場合、Webmanはそれらも自動的にインスタンス化および注入します。開発者は初期化作業を行う必要はありません。

> **注意**
> 依存の自動インジェクションを完了するには、フレームワークまたは`php-di`が作成したインスタンスでなければなりません。手動で`new`したインスタンスでは依存の自動インジェクションを行うことはできず、インジェクションが必要な場合は`support\Container`インターフェースを使って`new`ステートメントを置き換える必要があります。例：

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// newキーワードで作成されたインスタンスでは依存のインジェクションができません
$user_service = new UserService;
// newキーワードで作成されたインスタンスでは依存のインジェクションができません
$log_service = new LogService($path, $name);

// Containerで作成されたインスタンスでは依存のインジェクションができます
$user_service = Container::get(UserService::class);
// Containerで作成されたインスタンスでは依存のインジェクションができます
$log_service = Container::make(LogService::class, [$path, $name]);
```

## 注釈のインジェクション
コンストラクタの依存性自動インジェクションに加えて、注釈を使用することもできます。前述の例を引き続き、`app\controller\UserController`を以下のように変更します：
```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
この例では、`@Inject`アノテーションを使用して注入し、`@var`アノテーションを使用してオブジェクトの型を宣言しています。この例はコンストラクタインジェクションと同じ効果を持ちますが、コードがより簡潔になります。

> **注意**
> Webmanは1.4.6バージョンまでコントローラーパラメーターのインジェクションをサポートしておらず、例えば次のようなコードはWebman<=1.4.6ではサポートされません：

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6バージョン以前はコントローラーパラメーターのインジェクションはサポートされません
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## カスタムコンストラクタインジェクション
時にはコンストラクタの引数がクラスのインスタンスではなく、文字列、数値、配列などのデータであることがあります。例えば、MailerのコンストラクタにはsmtpサーバーのIPとポートを渡す必要があります：
```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // メール送信コードは省略されています
    }
}
```
このような場合、前述のコンストラクタ自動インジェクションは直接使用できません。なぜなら、`php-di`は`$smtp_host` `$smtp_port`の値を特定することができないからです。この場合、カスタムインジェクションを試してみることができます。

`config/dependence.php`（ファイルが存在しない場合は作成してください）に以下のコードを追加します：
```php
return [
    // ... 他の設定はここで簡略化されています
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
このようにすると、依存性のインジェクションが`app\service\Mailer`インスタンスを取得する必要がある場合、この構成で作成された`app\service\Mailer`インスタンスが自動的に使用されます。

`config/dependence.php`では`new`を使用して`Mailer`クラスをインスタンス化していますが、この例では問題はありません。ただし、`Mailer`クラスが他のクラスに依存しているか、または`Mailer`クラス内で注釈インジェクションが使用されている場合は、`new`を使用して初期化すると依存の自動インジェクションが行われません。解決策は、カスタムインターフェースのインジェクションを利用し、`Container::get(クラス名)`または`Container::make(クラス名, [コンストラクタ引数])`メソッドを使用してクラスを初期化することです。
## カスタムインターフェースのインジェクション
実際のプロジェクトでは、具体的なクラスではなく、インターフェースに基づいてプログラミングしたいという希望があります。例えば、`app\controller\UserController`では、`app\service\Mailer`ではなく`app\service\MailerInterface`を参照すべきです。

`MailerInterface` インターフェースを定義します。
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface` インターフェースの実装を定義します。
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;
    
    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // メール送信コードは省略
    }
}
```

具体の実装ではなく`MailerInterface` インターフェースを参照します。
```php
<?php
namespace app\controller;

use support\Request;
use app\service\MailerInterface;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;
    
    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

`config/dependence.php`では `MailerInterface` インターフェースを以下のように定義します。
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

このようにして、`MailerInterface` インターフェースを使用する際には自動的に`Mailer`が実装されます。

> インターフェースプログラミングの利点は、特定のコンポーネントを変更する必要がある場合、ビジネスコードを変更する必要がなく、`config/dependence.php`で具体的な実装を変更するだけで済むことです。これは単体テストでも非常に有用です。

## その他のカスタムインジェクション
`config/dependence.php`はクラスの依存関係だけでなく、文字列、数値、配列などの他の値も定義できます。

例えば、`config/dependence.php`では以下のように定義されています。
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

この時、`@Inject` を使用して `smtp_host` と `smtp_port` をクラスのプロパティにインジェクトできます。
```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // メール送信コードは省略
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 192.168.1.11:25 と出力されます
    }
}
```

> 注意: `@Inject("key")` では二重引用符を使用します。

## その他のリソース
[php-diマニュアル](https://php-di.org/doc/getting-started.html)を参照してください。
