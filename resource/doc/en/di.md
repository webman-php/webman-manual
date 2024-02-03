# Dependency Auto-injection

In webman, dependency auto-injection is an optional feature and is disabled by default. If you want to enable dependency auto-injection, it is recommended to use [php-di](https://php-di.org/doc/getting-started.html). Below is the usage of webman combined with `php-di`.

## Installation
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modify the configuration `config/container.php`, and its final content will be as follows:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```
> The `config/container.php` should ultimately return a container instance that complies with the `PSR-11` specification. If you do not want to use `php-di`, you can create and return another container instance that complies with the `PSR-11` specification here.

## Constructor Injection
Create a new `app/service/Mailer.php` (create the directory if it does not exist) with the following content:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Sending mail code omitted
    }
}
```

The `app/controller/UserController.php` will have the following content:
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
Normally, the following code is required to instantiate `app\controller\UserController`:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
When using `php-di`, developers do not need to manually instantiate the `Mailer` in the controller; webman will automatically do it for you. If there are other class dependencies during the instantiation of `Mailer`, webman will also automatically instantiate and inject them. Developers do not need any initialization work.

> **Note:**
> Only instances created by the framework or `php-di` can complete dependency auto-injection. Manually created instances using `new` cannot achieve dependency auto-injection. If injection is needed, the `support\Container` interface should be used to replace the `new` statement, for example:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Instances created with the new keyword cannot be dependency injected
$user_service = new UserService;
// Instances created with the new keyword cannot be dependency injected
$log_service = new LogService($path, $name);

// Instances created with Container can be dependency injected
$user_service = Container::get(UserService::class);
// Instances created with Container can be dependency injected
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Annotation Injection
In addition to constructor dependency auto-injection, we can also use annotation injection. Continuing with the previous example, modify `app\controller\UserController` as follows:
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
In this example, the injection is done through `@Inject` annotation, and the object type is declared using `@var` annotation. The effect of annotation injection is the same as constructor injection, but the code is more concise.

> **Note:**
> Before version 1.4.6, webman did not support controller parameter injection. For example, the following code is not supported when `webman<=1.4.6`:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Not supported before version 1.4.6
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## Custom Constructor Injection

Sometimes, the parameters passed to the constructor may not be class instances, but rather strings, numbers, arrays, and so on. For example, the Mailer constructor needs to pass the SMTP server IP and port:
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
        // Sending mail code omitted
    }
}
```
In this case, it is not possible to use the aforementioned constructor auto-injection, because `php-di` cannot determine the values of `$smtp_host` and `$smtp_port`. In such cases, custom injection can be attempted.

Add the following code to `config/dependence.php` (create the file if it does not exist):
```php
return [
    // ... Other configurations are omitted
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
This means that when dependency injection needs to obtain an instance of `app\service\Mailer`, it will automatically use the instance of `app\service\Mailer` created in this configuration.

We notice that `config/dependence.php` uses `new` to instantiate the `Mailer` class, there is no problem in this example, but imagine if the `Mailer` class has dependencies on other classes or uses annotation injection internally, using `new` initialization will not achieve dependency auto-injection. The solution is to use custom interface injection, by using the `Container::get(class name)` or `Container::make(class name, [constructor parameters])` method to initialize the class.

## Custom Interface Injection
In real projects, we prefer to program against interfaces rather than concrete classes. For example, in `app\controller\UserController`, it should include `app\service\MailerInterface` instead of `app\service\Mailer`.

Define the `MailerInterface` interface.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Define the implementation of the `MailerInterface` interface.
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
        // Sending mail code omitted
    }
}
```

Include the `MailerInterface` interface rather than the specific implementation.
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

Define the implementation of the `MailerInterface` interface in `config/dependence.php` as follows.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

This means that when the business needs to use the `MailerInterface` interface, it will automatically use the `Mailer` implementation.

> The benefit of programming against interfaces is that when we need to replace a component, no changes are required in the business code, only the specific implementation in `config/dependence.php` needs to be modified. This is also very useful for unit testing.

## Other Custom Injection
In `config/dependence.php`, apart from defining class dependencies, other values such as strings, numbers, arrays, etc., can be defined.

For example, if `config/dependence.php` is defined as follows:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

In this case, we can inject `smtp_host` and `smtp_port` into class properties using `@Inject`.
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
        // Sending mail code omitted
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Will output 192.168.1.11:25
    }
}
```

> Note: The content inside `@Inject("key")` is in double quotes.

## More Information
Please refer to the [php-di manual](https://php-di.org/doc/getting-started.html) for more details.