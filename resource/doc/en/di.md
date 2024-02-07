# Dependency Injection

Dependency injection is an optional feature in webman that is disabled by default. If you need dependency injection, it is recommended to use [php-di](https://php-di.org/doc/getting-started.html). The following is an example of how to use `php-di` with webman.

## Installation

```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modify the configuration `config/container.php`, and the final content should be as follows:

```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` should return an instance of a container that complies with the `PSR-11` specification. If you don't want to use `php-di`, you can create and return another container instance that complies with the `PSR-11` specification here.

## Constructor Injection

Create a new file `app/service/Mailer.php` with the following content:

```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Send mail code omitted
    }
}
```

The content of `app/controller/UserController.php` is as follows:

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
Normally, the following code is needed to instantiate `app\controller\UserController`:

```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
When using `php-di`, you don't need to manually instantiate `Mailer` in the controller. Webman will automatically handle it for you. If there are dependencies in the instantiation process of `Mailer`, webman will also automatically instantiate and inject them. You don't need any initialization work.

> **Note**
> Only instances created by the framework or `php-di` can complete dependency injection. Instances created manually with the `new` keyword cannot complete dependency injection. If you want to inject these instances, you need to use the `support\Container` interface instead of the `new` statement, for example:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Dependency injection is not supported for instances created with the new keyword
$user_service = new UserService;
// Dependency injection is not supported for instances created with the new keyword
$log_service = new LogService($path, $name);

// Dependency injection is supported for instances created with Container
$user_service = Container::get(UserService::class);
// Dependency injection is supported for instances created with Container
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Annotation Injection

In addition to constructor injection, we can also use annotation injection. Continuing with the previous example, `app\controller\UserController` is changed as follows:

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
In this example, injection is performed using the `@Inject` annotation, and the object type is declared using the `@var` annotation. This example has the same effect as constructor injection, but the code is more concise.

> **Note**
> Webman does not support controller parameter injection before version 1.4.6. For example, the following code is not supported when webman<=1.4.6:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Controller parameter injection is not supported before version 1.4.6
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## Custom Constructor Injection

Sometimes the parameters passed to the constructor may not be instances of a class, but rather strings, numbers, arrays, and other data. For example, the `Mailer` constructor requires the SMTP server IP and port:

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
        // Send mail code omitted
    }
}
```

In this case, constructor auto-injection cannot be used directly because `php-di` cannot determine the values of `$smtp_host` and `$smtp_port`. In this case, you can try custom injection.

Add the following code to `config/dependence.php` (create the file if it does not exist):

```php
return [
    // ... Other configurations here

    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25)
];
```

This way, when the dependency injection needs to get an instance of `app\service\Mailer`, it will automatically use the `app\service\Mailer` instance created in this configuration.

We can see that `config/dependence.php` uses `new` to instantiate the `Mailer` class, which is not a problem in this example. But imagine if the `Mailer` class has dependencies on other classes or uses annotation injection internally, using `new` initialization will not perform dependency injection. The solution is to use custom interface injection and initialize the class using the `Container::get(class name)` or `Container::make(class name, [constructor parameters])` methods instead of the `new` keyword.

## Custom Interface Injection

In real-world projects, it is preferable to program against interfaces rather than concrete classes. For example, `app\controller\UserController` should import `app\service\MailerInterface` instead of `app\service\Mailer`.

Define the `MailerInterface` interface.

```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Define an implementation of the `MailerInterface` interface.

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
        // Send mail code omitted
    }
}
```

Import `MailerInterface` instead of the concrete implementation.

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

Define the implementation of the `MailerInterface` interface in `config/dependence.php`.

```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

This way, when the business needs to use the `MailerInterface` interface, it will automatically use the `Mailer` implementation.

> The benefit of programming against interfaces is that when we need to replace a component, we don't need to change the business code, we only need to change the concrete implementation in `config/dependence.php`. This is also very useful for unit testing.
## Other custom injections
In addition to defining class dependencies, `config/dependence.php` can also define other values such as strings, numbers, arrays, etc.

For example, if `config/dependence.php` is defined as follows:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

We can use `@Inject` to inject `smtp_host` and `smtp_port` into class properties.
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
        // Send mail code omitted
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Will output 192.168.1.11:25
    }
}
```

> Note: The double quotes are used inside `@Inject("key")`.


## For more information
Please refer to the [php-di documentation](https://php-di.org/doc/getting-started.html)
