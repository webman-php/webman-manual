# Автоматическая внедрение зависимостей
В webman автоматическое внедрение зависимостей - необязательная функция, которая по умолчанию отключена. Если вам нужно автоматическое внедрение зависимостей, рекомендуется использовать [php-di](https://php-di.org/doc/getting-started.html). Ниже приведен способ использования `php-di` с webman.

## Установка
```shell
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Измените конфигурацию `config/container.php` следующим образом:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> Конечный результат `config/container.php` должен возвращать экземпляр контейнера, соответствующий спецификации `PSR-11`. Если вы не хотите использовать `php-di`, вы можете создать и вернуть другой экземпляр контейнера, соответствующий спецификации `PSR-11`, здесь.

## Внедрение через конструктор
Создайте новый файл `app/service/Mailer.php` (если каталог не существует, создайте его) со следующим содержимым:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Код для отправки письма опущен
    }
}
```

Содержимое файла `app/controller/UserController.php` следующее:

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
В обычной ситуации для создания экземпляра `app\controller\UserController` требуется следующий код:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
При использовании `php-di` разработчику не нужно вручную создавать экземпляры `Mailer` в контроллере, webman автоматически сделает это. Если при создании `Mailer` есть зависимости от других классов, webman также автоматически создаст и внедрит их. Разработчику не нужно выполнять никаких инициализационных работ.

> **Примечание**
> Для выполнения автоматического внедрения зависимостей необходимо, чтобы экземпляр был создан фреймворком или `php-di`. Ручное создание экземпляра с использованием `new` не поддерживается для автоматического внедрения зависимостей. Если необходимо внедрение, следует использовать интерфейс `support\Container` вместо оператора `new`, например:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Экземпляр, созданный с помощью оператора "new", не может быть внедрен
$user_service = new UserService;
// Экземпляр, созданный с помощью оператора "new", не может быть внедрен
$log_service = new LogService($path, $name);

// Экземпляр, созданный через Container, может быть внедрен
$user_service = Container::get(UserService::class);
// Экземпляр, созданный через Container, может быть внедрен
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Аннотационное внедрение
Помимо внедрения через конструктор, мы также можем использовать аннотационное внедрение. Продолжаем предыдущий пример, изменим `app\controller\UserController` следующим образом:
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
В этом примере происходит аннотационное внедрение через `@Inject` и тип объекта объявляется с помощью аннотации `@var`. Этот пример имеет такой же эффект, как и внедрение через конструктор, но код более компактен.

> **Примечание**
> До версии 1.4.6 webman не поддерживал внедрение параметров контроллера. Например, следующий код в webman<=1.4.6 является неподдерживаемым:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // До версии 1.4.6 не поддерживался внедрение параметров контроллера
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Привет и добро пожаловать!');
        return response('ok');
    }
}
```

## Пользовательское внедрение через конструктор
Иногда параметры, передаваемые в конструктор, могут быть не экземпляром класса, а строкой, числом, массивом и т. д. Например, конструктор класса Mailer требует передачи IP и порта SMTP:
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
        // Код для отправки письма опущен
    }
}
```
В этом случае не удается использовать автоматическое внедрение через конструктор, так как `php-di` не может определить значения `$smtp_host` и `$smtp_port`. В этом случае можно попробовать пользовательское внедрение.

Добавьте следующий код в `config/dependence.php` (если файла не существует, создайте его):
```php
return [
    // ... здесь были пропущены другие настройки
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Таким образом, при запросе на получение экземпляра `app\service\Mailer` в процессе внедрения будет использоваться созданный в этой конфигурации экземпляр `app\service\Mailer`.

Мы заметили, что в `config/dependence.php` происходит создание экземпляра `Mailer` с использованием `new`. В данном примере это не вызывает проблем, но представьте, что если `Mailer` зависит от других классов или использует аннотационное внедрение внутри себя, то инициализация с использованием `new` не приведет к автоматическому внедрению зависимостей. Решением этой проблемы будет использование пользовательского интерфейса внедрения, инициализация класса должна происходить с помощью методов `Container::get(имя_класса)` или `Container::make(имя_класса, [параметры_конструктора])`.
## Пользовательский внедренный интерфейс

В реальных проектах мы предпочитаем программировать на основе интерфейсов, а не конкретных классов. Например, в `app\controller\UserController` должен быть импортирован `app\service\MailerInterface`, а не `app\service\Mailer`.

Определение интерфейса `MailerInterface`.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Реализация интерфейса `MailerInterface`.
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

Импортирование интерфейса `MailerInterface`, а не конкретной реализации.
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
`config/dependence.php` определяет реализацию интерфейса `MailerInterface` следующим образом.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Таким образом, при необходимости использовать интерфейс `MailerInterface`, автоматически используется реализация `Mailer`.

> Преимущество программирования на основе интерфейсов заключается в том, что при необходимости замены компонента не требуется изменять бизнес-код, достаточно просто изменить конкретную реализацию в `config/dependence.php`. Это также очень полезно при написании модульных тестов.

## Другие пользовательские внедрения
В `config/dependence.php` помимо определения зависимостей классов, также можно определять другие значения, такие как строки, числа, массивы и т. д.

Например, `config/dependence.php` определяет следующее:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

В этом случае мы можем внедрить `smtp_host` и `smtp_port` в свойства класса с помощью `@Inject`.
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
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Выведет 192.168.1.11:25
    }
}
```
> Обратите внимание: в `@Inject("ключ")` используются двойные кавычки.

## Дополнительная информация
Пожалуйста, обратитесь к [руководству по php-di](https://php-di.org/doc/getting-started.html)
