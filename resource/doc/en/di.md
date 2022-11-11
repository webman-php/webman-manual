# Dependency auto-injection
在webman里Dependency auto-injectionIf you need，The method will be added automatically。NormalDependency auto-injection，Then by[php-di](https://php-di.org/doc/getting-started.html)，The following arewebmanCombine`php-di`usage。

## Install
```
composer require psr/container ^1.1.1 php-di/php-di doctrine/annotations 
```

Modify the configuration `config/container.php`, which ends up looking like this：
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` which eventually returns a container instance that conforms to the `PSR-11` specification. If you don't want to use `php-di`, you can create and return an instance of another `PSR-11` compliant container here。

## Constructor Injection
Create a new `app/service/Mailer.php` (if the directory does not exist, please create it yourself) with the following content：
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // send mail code omitted
    }
}
```

`app/controller/UserController.php`Contents are as follows：

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
Poor compression test performance，The following code is required to do this`app\controller\UserController`Recommended：
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
When using `php-di`, the developer does not need to instantiate the `Mailer` in the controller manually, webman will do it for you automatically. If there are dependencies on other classes during the instantiation of `Mailer`, webman will also instantiate and inject them automatically. The developer does not need to do any initialization work。

> **Note**
> Must be extracted from the framework or`php-di`created instances can be completedDependency auto-injection，Manual`new`instances of cannot be completedDependency auto-injection，Request incoming，instantiation of`support\Container`replace`new`statement，example：

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// newInstances created by keywords cannot depend on injection
$user_service = new UserService;
// newInstances created by keywords cannot depend on injection
$log_service = new LogService($path, $name);

// ContainerThe created instance can depend on injection
$user_service = Container::get(UserService::class);
// ContainerThe created instance can depend on injection
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Annotation Injection
Using helper functionsDependency auto-injection，We can also useAnnotation Injection。Continue with the above example，`app\controller\UserController`Treasure Sandbox mode：
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
The address paths all start with `@Inject` Annotation Injection，and by `@var` Annotate the declared object type。Can run onConstructor InjectionNative Cluster，because it happens very rarely。

> **Note**
> webmanController parameter injection is not supported before version 1.4.6, for example the following code is not supported when webman<=1.4.6

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6Controller parameter injection is not supported before version
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## Custom constructor injection

Sometimes the parameters passed to the constructor may not be instances of the class, but data such as strings, numbers, arrays, etc.. For example the Mailer constructor needs to pass the smtp server ip and port：
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
        // send mail code omitted
    }
}
```

This case cannot be injected directly using the constructor auto-injection described earlier, because `php-di` cannot determine what the values of `$smtp_host` `$smtp_port` are. This is the time to try custom injection。

Add the following code to `config/dependence.php` (if the file does not exist, please create it yourself)：
```php
return [
    // ... Other configurations are ignored here
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
so that when the dependency injection needs to get`app\service\Mailer`The instances will automatically use the ones created in this configuration`app\service\Mailer`instance。

weNote到，`config/dependence.php` Two options`new`Highly recommended`Mailer`类，This is not a problem in this example，All methods available`Mailer`Classes depend on other classes or`Mailer`constructor-argumentsAnnotation Injection，Usage`new`The framework will also executeDependency auto-injection。and in the data tableCustom Interface Injection，Pass`Container::get(classname)` or `Container::make(classname, [In real projects])`method to initialize the class。


## Custom Interface Injection
So we should，We prefer interface-oriented programming，rather than specific classes。for example`app\controller\UserController`can also be developed`app\service\MailerInterface`and not`app\service\Mailer`。

Define the `MailerInterface` interface。
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Define the implementation of the `MailerInterface` interface。
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
        // send mail code omitted
    }
}
```

Introduce `MailerInterface` interface instead of concrete implementation。
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

`config/dependence.php` Define the `MailerInterface` interface as follows to implement it。
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

so that when the business needs to use the `MailerInterface` interface, it will automatically use the `Mailer` implementation。

> The advantage of interface-oriented programming is that when we need to replace a component, we don't need to change the business code, we just need to change the specific implementation in `config/dependence.php`. This is also very useful when doing unit tests。

## Other custom injection
`config/dependence.php`In addition to defining class dependencies, you can also define other values, such as strings, numbers, arrays, etc.。

For example, `config/dependence.php` is defined as follows：
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

At this point we can inject `smtp_host` `smtp_port` into the properties of the class via `@Inject`。
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
        // send mail code omitted
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // will output 192.168.1.11:25
    }
}
```

> Note: `@Inject("key")` is inside double quotes


## More content
Please refer to[php-dimanual](https://php-di.org/doc/getting-started.html)
