# 依赖注入
webman默认没开启自动依赖注入。如果你需要自动依赖注入，推荐使用[php-di](https://php-di.org/doc/getting-started.html)，以下是webman结合`php-di`的用法。

## 安装
```
composer require php-di/php-di doctrine/annotations
```

修改配置`config/container.php`，其最终内容如下：
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php`里最终目的是返回一个依赖注入容器实例，webman支持`PSR-11`规范的容器接口。如果你不想使用 `php-di` ，可以在这里返回一个其它符合`PSR-11`规范的容器实例。

## 构造方法注入
新建`app/service/Mailer.php`(如目录不存在请自行创建)内容如下：
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // 发送邮件省略
    }
}
```

`app\controller\User.php`内容如下：

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class User
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail($request->post('email'), 'Hello and welcome!');
        return response('ok');
    }
}
```
正常情况下，需要以下代码才能完成`app\controller\User`的实例化：
```php
$mailer = new Mailer;
$user = new User($mailer);
```
当使用`php-di`后，开发者无需手动实例化`Mailer`，`php-di`会自动帮你完成。如果在实例化`Mailer`过程中有其它类的依赖，`php-di`也会自动实例化并注入。

> 注意：必须是由`php-di`创建的实例才能完成自动依赖注入，手动`new`的实例无法完成自动依赖注入。`controller`是由webman通过`php-di`实例化的，所以直接支持自动依赖注入。如果其它类想使用自动依赖注入，可以调用`Container::get(类名)`或者`Container::make(类名, [构造函数参数])`来创建对应类的实例。

## 注解注入
除了自动依赖注入，我们还可以使用注解注入。继续上面的例子，`app\controller\User`更改成如下：
```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class User
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail($request->post('email'), 'Hello and welcome!');
        return response('ok');
    }
}
```
这个例子通过 `@Inject` 注解注入，并且由 `@var` 注解声明对象类型。这个例子和构造函数注入效果一样，但是代码更精简。

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
        echo "mail $email send $content\n";
        echo "{$this->smtpHost} {$this->smtpPort}\n";
        // 发送邮件省略
    }
}
```

这种情况无法直接使用构造函数自动注入，因为`php-di`无法确定`$smtp_host` `$smtp_port`的值是什么。这时候可以尝试自定义注入。

在`config/dependence.php`(文件不存在请自行创建)中加入如下代码：
```php
return [
    // ... 这里忽略了其它配置
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
这样当依赖注入需要获取`app\service\Mailer`实例时将自动使用这个配置中创建的`app\service\Mailer`实例。

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
        // 发送邮件省略
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 将输出 192.168.1.11:25
    }
}
```

## 更多内容
请参考[php-di手册](https://php-di.org/doc/getting-started.html)