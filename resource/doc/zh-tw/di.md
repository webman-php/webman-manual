# 依賴自動注入
在webman裡依賴自動注入是可選功能，此功能預設是關閉的。如果你需要依賴自動注入，推薦使用[php-di](https://php-di.org/doc/getting-started.html)，以下是webman結合 `php-di` 的用法。

## 安裝
```composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

修改配置`config/container.php`，其最終內容如下：
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php`裡最終返回一個符合`PSR-11`規範的容器實例。如果你不想使用 `php-di` ，可以在這裡創建並返回一個其他符合`PSR-11`規範的容器實例。

## 構造函數注入
新建`app/service/Mailer.php`(如目錄不存在請自行創建)內容如下：
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // 發送郵件程式略
    }
}
```

`app/controller/UserController.php`內容如下：

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
正常情況下，需要以下程式碼才能完成`app\controller\UserController`的實例化：
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```當使用`php-di`後，開發者無需手動實例化控制器中的`Mailer`，webman會自動幫你完成。如果在實例化`Mailer`過程中有其它類的依賴，webman也會自動實例化並注入。開發者不需要任何的初始化工作。

> **注意**
> 必須是由框架或者`php-di`創建的實例才能完成依賴自動注入，手動`new`的實例無法完成依賴自動注入，如需注入，需要使用`support\Container`介面替換`new`語句，例如：

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new關鍵字創建的實例無法依賴注入
$user_service = new UserService;
// new關鍵字創建的實例無法依賴注入
$log_service = new LogService($path, $name);

// Container創建的實例可以依賴注入
$user_service = Container::get(UserService::class);
// Container創建的實例可以依賴注入
$log_service = Container::make(LogService::class, [$path, $name]);
```

## 註解注入
除了構造函數依賴自動注入，我們還可以使用註解注入。繼續上面的例子，`app\controller\UserController`更改成如下：
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
```這個例子通過 `@Inject` 註解注入，並且由 `@var` 註解聲明對象類型。這個例子和構造函數注入效果一樣，但是程式碼更精簡。

> **注意**
> webman在1.4.6版本之前不支持控制器參數注入，例如以下程式碼當webman<=1.4.6時是不支援的

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6版本之前不支持控制器參數注入
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## 自定義構造函數注入
有時候構造函數傳入的參數可能不是類的實例，而是字串、數字、陣列等數據。例如Mailer構造函數需要傳遞smtp伺服器ip和端口：
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
        // 發送郵件程式略
    }
}
```這種情況無法直接使用前面介紹的構造函數自動注入，因為`php-di`無法確定`$smtp_host` `$smtp_port`的值是什麼。這時候可以嘗試自定義注入。

在`config/dependence.php`(檔案不存在請自行創建)中加入如下程式碼：
```php
return [
    // ... 這裡忽略了其他配置
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```這樣當依賴注入需要獲取`app\service\Mailer`實例時將自動使用這個配置中創建的`app\service\Mailer`實例。

我們注意到，`config/dependence.php` 中使用了`new`來實例化`Mailer`類，這個在本示例沒有任何問題，但是想象下如果`Mailer`類依賴了其他類的話或者`Mailer`類內部使用了註解注入，使用`new`初始化將不會依賴自動注入。解決辦法是利用自定義介面注入，通過`Container::get(類名)` 或者 `Container::make(類名, [構造函數參數])`方法來初始化類。
## 自訂接口注入
在實際的專案中，我們更希望以接口為導向進行編碼，而不是具體的類別。例如在 `app\controller\UserController` 中，應該引入 `app\service\MailerInterface` 而不是 `app\service\Mailer`。

定義 `MailerInterface` 接口。
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

定義 `MailerInterface` 介面的實作。
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
        // 發送郵件程式碼省略
    }
}
```

引入 `MailerInterface` 介面而非具體實作。
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

`config/dependence.php` 將 `MailerInterface` 介面定義如下實作。
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

這樣當業務需要使用 `MailerInterface` 介面時，將自動使用 `Mailer` 實作。

> 面向接口編程的好處是，當我們需要更換某個組件時，不需要更改業務程式碼，只需要更改 `config/dependence.php` 中的具體實作即可。這在做單元測試也非常有用。

## 其他自訂注入
`config/dependence.php` 除了能定義類的相依性，也能定義其他值，例如字串、數字、陣列等。

例如 `config/dependence.php` 定義如下：
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

這時候我們可以透過 `@Inject` 將 `smtp_host` 和 `smtp_port` 注入到類別的屬性中。
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
        // 發送郵件程式碼省略
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 將輸出 192.168.1.11:25
    }
}
```

> 注意：`@Inject("key")` 裡面是雙引號

## 更多內容
請參考 [php-di手冊](https://php-di.org/doc/getting-started.html)
