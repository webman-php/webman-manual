# 依赖自动注入
在webman里依赖自动注入是可选功能，此功能默认关闭。如果你需要依赖自动注入，推荐使用[php-di](https://php-di.org/doc/getting-started.html)，以下是webman结合`php-di`的用法。

## 安装
```
composer require psr/container ^1.1.1 php-di/php-di ^6.3 doctrine/annotations ^1.14
```

修改配置`config/container.php`，其最终内容如下：
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php`里最终返回一个符合`PSR-11`规范的容器实例。如果你不想使用 `php-di` ，可以在这里创建并返回一个其它符合`PSR-11`规范的容器实例。

## 构造函数注入
新建`app/service/Mailer.php`(如目录不存在请自行创建)内容如下：
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // 发送邮件代码省略
    }
}
```

`app/controller/UserController.php`内容如下：

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
正常情况下，需要以下代码才能完成`app\controller\UserController`的实例化：
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
当使用`php-di`后，开发者无需手动实例化控制器中的`Mailer`，webman会自动帮你完成。如果在实例化`Mailer`过程中有其它类的依赖，webman也会自动实例化并注入。开发者不需要任何的初始化工作。

> **注意**
> 必须是由框架或者`php-di`创建的实例才能完成依赖自动注入，手动`new`的实例无法完成依赖自动注入，如需注入，需要使用`support\Container`接口替换`new`语句，例如：

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new关键字创建的实例无法依赖注入
$user_service = new UserService;
// new关键字创建的实例无法依赖注入
$log_service = new LogService($path, $name);

// Container创建的实例可以依赖注入
$user_service = Container::get(UserService::class);
// Container创建的实例可以依赖注入
$log_service = Container::make(LogService::class, [$path, $name]);
```

## 注解注入
除了构造函数依赖自动注入，我们还可以使用注解注入。继续上面的例子，`app\controller\UserController`更改成如下：
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
这个例子通过 `@Inject` 注解注入，并且由 `@var` 注解声明对象类型。这个例子和构造函数注入效果一样，但是代码更精简。

> **注意**
> webman在1.4.6版本之前不支持控制器参数注入，例如以下代码当webman<=1.4.6时是不支持的

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6版本之前不支持控制器参数注入
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## 自定义构造函数注入

有时候构造函数传入的参数可能不是类的实例，而是字符串、数字、数组等数据。例如Mailer构造函数需要传递smtp服务器ip和端口：
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
        // 发送邮件代码省略
    }
}
```

这种情况无法直接使用前面介绍的构造函数自动注入，因为`php-di`无法确定`$smtp_host` `$smtp_port`的值是什么。这时候可以尝试自定义注入。

在`config/dependence.php`(文件不存在请自行创建)中加入如下代码：
```php
return [
    // ... 这里忽略了其它配置
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
这样当依赖注入需要获取`app\service\Mailer`实例时将自动使用这个配置中创建的`app\service\Mailer`实例。

我们注意到，`config/dependence.php` 中使用了`new`来实例化`Mailer`类，这个在本示例没有任何问题，但是想象下如果`Mailer`类依赖了其它类的话或者`Mailer`类内部使用了注解注入，使用`new`初始化将不会依赖自动注入。解决办法是利用自定义接口注入，通过`Container::get(类名)` 或者 `Container::make(类名, [构造函数参数])`方法来初始化类。


## 自定义接口注入
在现实项目中，我们更希望面向接口编程，而不是具体的类。比如`app\controller\UserController`里应该引入`app\service\MailerInterface`而不是`app\service\Mailer`。

定义`MailerInterface`接口。
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

定义`MailerInterface`接口的实现。
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
        // 发送邮件代码省略
    }
}
```

引入`MailerInterface`接口而非具体实现。
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

`config/dependence.php` 将 `MailerInterface` 接口定义如下实现。
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

这样当业务需要使用`MailerInterface`接口时，将自动使用`Mailer`实现。

> 面向接口编程的好处是，当我们需要更换某个组件时，不需要更改业务代码，只需要更改`config/dependence.php`中的具体实现即可。这在做单元测试也非常有用。

## 其它自定义注入
`config/dependence.php`除了能定义类的依赖，也能定义其它值，例如字符串、数字、数组等。

例如`config/dependence.php`定义如下：
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

这时候我们可以通过`@Inject`将`smtp_host` `smtp_port` 注入到类的属性中。
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
        // 发送邮件代码省略
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 将输出 192.168.1.11:25
    }
}
```

> 注意：`@Inject("key")` 里面是双引号


## 更多内容
请参考[php-di手册](https://php-di.org/doc/getting-started.html)
