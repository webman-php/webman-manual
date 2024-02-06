# Автоматическое внедрение зависимостей
В webman автоматическое внедрение зависимостей является необязательной функцией и по умолчанию отключено. Если вам нужно автоматическое внедрение зависимостей, рекомендуется использовать [php-di](https://php-di.org/doc/getting-started.html). Ниже приведен пример использования `php-di` с webman.

## Установка
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Измените конфигурацию в `config/container.php`, чтобы в итоге она выглядела следующим образом:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` должен возвращать экземпляр контейнера, соответствующий спецификации `PSR-11`. Если вы не хотите использовать `php-di`, вы можете создать и вернуть другой экземпляр контейнера, соответствующий спецификации `PSR-11`.

## Внедрение через конструктор
Создайте новый файл `app/service/Mailer.php` (если каталог не существует, создайте его) со следующим содержимым:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Пропущен код отправки электронной почты
    }
}
```

Содержимое файла `app/controller/UserController.php`:
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
        $this->mailer->mail('hello@webman.com', 'Привет и добро пожаловать!');
        return response('ok');
    }
}
```
В обычных случаях для создания экземпляра `app\controller\UserController` требуется следующий код:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
После использования `php-di` разработчику больше не нужно вручную создавать экземпляры `Mailer`, webman автоматически выполнит это. При необходимости webman также автоматически создаст и внедрит зависимости при создании экземпляра `Mailer`. Разработчику не нужно выполнять никаких начальных действий.

> **Обратите внимание:**
> Автоматическое внедрение зависимостей можно выполнить только для экземпляров, созданных фреймворком или `php-di`. Экземпляры, созданные вручную с использованием `new`, не могут быть автоматически внедрены. Если необходимо внедрение, следует использовать интерфейс `support\Container` вместо оператора `new`:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Экземпляры, созданные с использованием оператора new, не могут быть внедрены
$user_service = new UserService;
// Экземпляры, созданные с использованием оператора new, не могут быть внедрены
$log_service = new LogService($path, $name);

// Экземпляры, созданные с помощью Container, могут быть внедрены
$user_service = Container::get(UserService::class);
// Экземпляры, созданные с помощью Container, могут быть внедрены
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Внедрение с помощью аннотаций
Помимо внедрения через конструктор, мы также можем использовать внедрение через аннотации. Для продолжения предыдущего примера, `app\controller\UserController` изменяется следующим образом:
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
        $this->mailer->mail('hello@webman.com', 'Привет и добро пожаловать!');
        return response('ok');
    }
}
```
В этом примере используется внедрение через аннотации с объявлением типа объекта через `@var` аннотацию. Этот пример демонстрирует, что внедрение через аннотации имеет такой же эффект, как внедрение через конструктор, но с более компактным кодом.

> **Обратите внимание:**
> До версии 1.4.6 webman не поддерживал внедрение параметров контроллера, как показано в следующем примере, если webman <= 1.4.6:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // До версии 1.4.6 webman не поддерживает внедрение параметров контроллера
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Привет и добро пожаловать!');
        return response('ok');
    }
}
```

## Пользовательское внедрение через конструктор
Иногда параметры, передаваемые в конструктор, могут быть не экземплярами класса, а строками, числами, массивами и т.д. Например, конструктор `Mailer` требует передачи адреса и порта SMTP:
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
        // Пропущен код отправки электронной почты
    }
}
```
В этом случае нельзя использовать автоматическое внедрение через конструктор, поскольку `php-di` не может определить значения `$smtp_host` и `$smtp_port`. В этом случае можно попробовать пользовательское внедрение.

Добавьте следующий код в `config/dependence.php` (если файла не существует, создайте его):
```php
return [
    // ... другие настройки
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Таким образом, при необходимости внедрения зависимостей будет автоматически использоваться экземпляр `app\service\Mailer`, созданный в этой конфигурации.

Обратите внимание, что в файле `config/dependence.php` используется оператор `new` для создания экземпляра класса `Mailer`. В данном примере это не вызывает проблем, но представьте, если `Mailer` зависит от других классов или использует аннотации для внедрения, то создание экземпляра с помощью `new` не приведет к автоматическому внедрению зависимостей. Решением будет использование пользовательского внедрения с помощью методов `Container::get(ClassName)` или `Container::make(ClassName, [constructorParams])`.

## Пользоватское внедрение через интерфейс
Намного целесообразнее программировать на основе интерфейсов, а не конкретных классов. Например, в `app\controller\UserController` должен использоваться интерфейс `app\service\MailerInterface`, а не конкретная реализация.

Определите интерфейс `MailerInterface`:
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Определите реализацию интерфейса `MailerInterface`:
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
        // Пропущен код отправки электронной почты
    }
}
```

Используйте `MailerInterface` вместо конкретной реализации:
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
        $this->mailer->mail('hello@webman.com', 'Привет и добро пожаловать!');
        return response('ok');
    }
}
```

Определите реализацию интерфейса `MailerInterface` следующим образом в `config/dependence.php`:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Таким образом, при необходимости использования интерфейса `MailerInterface` будет автоматически использоваться реализация `Mailer`.

> Преимущество программирования на основе интерфейсов заключается в том, что при необходимости замены компоненты не требуется изменять код бизнес-логики, достаточно просто изменить конкретную реализацию в `config/dependence.php`. Это также очень полезно при написании модульных тестов.

## Другие пользоватские внедрения
Файл `config/dependence.php` также может содержать другие значения, такие как строки, числа, массивы и т.д.

Например, в `config/dependence.php` можно определить следующее:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Теперь мы можем использовать аннотацию `@Inject` для внедрения `smtp_host` и `smtp_port` в свойства класса:
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
        // Пропущен код отправки электронной почты
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Будет выведено 192.168.1.11:25
    }
}
```

> Обратите внимание: в аннотации `@Inject("key")` используются двойные кавычки.

## Дополнительная информация
Пожалуйста, обратитесь к [руководству по php-di](https://php-di.org/doc/getting-started.html) для получения дополнительной информации.
