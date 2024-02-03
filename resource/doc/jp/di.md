# 依存関係の自動インジェクション
webmanでは、依存関係の自動インジェクションはオプション機能であり、デフォルトで無効になっています。依存関係の自動インジェクションが必要な場合、[php-di](https://php-di.org/doc/getting-started.html)を使用することをお勧めします。以下は`php-di`を使用したwebmanの方法です。

## インストール
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

設定`config/container.php`を変更し、最終的な内容は次のようになります：
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php`は`PSR-11`規格に準拠するコンテナインスタンスを最終的に返します。`php-di`を使用したくない場合は、ここで`PSR-11`規格に準拠する他のコンテナインスタンスを作成して返すことができます。

## コンストラクターインジェクション
新しい`app/service/Mailer.php`（ディレクトリが存在しない場合は作成してください）の内容は次のとおりです：
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // メール送信コードを省略
    }
}
```

`app/controller/UserController.php`の内容は次のとおりです：

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
        $this->mailer->mail('hello@webman.com', 'こんにちは、ようこそ！');
        return response('ok');
    }
}
```
通常は次のコードが必要ですが、`app\controller\UserController`のインスタンス化が完了します：
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
 `php-di`を使用すると、開発者は`UserController`内の`Mailer`を手動でインスタンス化する必要はありません。Webmanが自動的に代わりに行います。 `Mailer`のインスタンス化中に他のクラスの依存関係がある場合、Webmanも自動的にインスタンス化およびインジェクションを行います。開発者は初期化作業を行う必要はありません。

> **注意**
> 依存関係の自動インジェクションはフレームワークまたは`php-di`によって作成されたインスタンスのみ完了し、手動で`new`されたインスタンスでは依存関係の自動インジェクションはできません。インジェクションが必要な場合は、`support\Container`インターフェースを使用して`new`ステートメントを置き換える必要があります。例えば：

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// newキーワードで作成されたインスタンスは依存性インジェクションできません
$user_service = new UserService;
// newキーワードで作成されたインスタンスは依存性インジェクションできません
$log_service = new LogService($path, $name);

// コンテナで作成されたインスタンスは依存性インジェクションできます
$user_service = Container::get(UserService::class);
// コンテナで作成されたインスタンスは依存性インジェクションできます
$log_service = Container::make(LogService::class, [$path, $name]);
```

## 注釈インジェクション
コンストラクター依存関係の自動インジェクションに加えて、注釈インジェクションも使用できます。前述の例を続けて、`app\controller\UserController`を次のように変更します：

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
        $this->mailer->mail('hello@webman.com', 'こんにちは、ようこそ！');
        return response('ok');
    }
}
```
この例では、`@Inject`注釈を使用してインジェクションし、`@var`注釈を使用してオブジェクトの型を宣言しています。この例はコンストラクターインジェクションと同じ効果がありますが、コードがより簡潔です。

> **注意**
> webman 1.4.6以前ではコントローラーパラメータのインジェクションはサポートされていません。以下のコードはwebman<=1.4.6で動作しません

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6以前はコントローラーパラメータのインジェクションはサポートされていません
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'こんにちは、ようこそ！');
        return response('ok');
    }
}
```

## カスタムコンストラクターインジェクション
時には、コンストラクターに渡されるパラメータがクラスのインスタンスではなく、文字列、数値、配列などのデータであることがあります。たとえば、MailerのコンストラクターにはSMTPサーバーのIPとポートを渡す必要があります：

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
        // メール送信コードを省略
    }
}
```

このような場合、前述のコンストラクター自動インジェクションを直接使用することはできません。なぜなら`php-di`は`$smtp_host`と`$smtp_port`の値を特定できないからです。このような場合は、カスタムインジェクションを試してみることができます。

`config/dependence.php`（ファイルが存在しない場合は作成してください）に次のコードを追加します：
```php
return [
    // ... 他の設定はここで無視しています
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
これにより、依存性注入が`app\service\Mailer`インスタンスを取得する必要がある場合、この設定で生成された`app\service\Mailer`インスタンスが自動的に使用されます。

`config/dependence.php`では、`new`を使用して`Mailer`クラスのインスタンスを初期化しています。この例では問題ありませんが、`Mailer`クラスが他のクラスに依存している場合や、`Mailer`クラス内で注釈インジェクションが使用されている場合、`new`で初期化すると依存関係の自動インジェクションが行われません。問題を解決するためには、カスタムインタフェースインジェクションを利用し、`Container::get(クラス名)`または`Container::make(クラス名、[コンストラクター引数])`メソッドを使用してクラスを初期化する必要があります。

## カスタムインタフェースインジェクション
実際のプロジェクトでは、具体的なクラスではなくインターフェースに焦点を当てたい場合があります。たとえば、`app\controller\UserController`には`app\service\Mailer`ではなく`app\service\MailerInterface`を導入したい場合です。

`MailerInterface`インターフェースを定義します。
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface`インターフェースの実装を定義します。
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
        // メール送信コードを省略
    }
}
```

具体的な実装ではなく、`MailerInterface`インターフェースを導入します。
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
        $this->mailer->mail('hello@webman.com', 'こんにちは、ようこそ！');
        return response('ok');
    }
}
```

`config/dependence.php`で`MailerInterface`インターフェースを以下のように定義します。
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

これにより、業務が`MailerInterface`インターフェースを使用する必要がある場合、`Mailer`の実装が自動的に使用されます。

> インターフェース指向の利点は、特定のコンポーネントを変更する必要がある場合、業務コードを変更する必要がないため、`config/dependence.php`の具体的な実装を変更するだけで済むことです。これはユニットテストに非常に役立ちます。

## その他のカスタムインジェクション
`config/dependence.php`では、クラスの依存関係だけでなく、文字列、数値、配列などの値を定義することもできます。

例えば、`config/dependence.php`で以下のように定義します：
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

この場合、`@Inject`を使用して`smtp_host`や`smtp_port`をクラスのプロパティに注入することができます。
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
        // メール送信コードを省略
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 192.168.1.11:25 が出力されます
    }
}
```

> 注意：`@Inject("key")` ではダブルクォーテーションを使用します

## 追加情報
[php-diマニュアル](https://php-di.org/doc/getting-started.html)を参照してください。
