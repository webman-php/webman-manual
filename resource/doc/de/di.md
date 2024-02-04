# Abhängigkeitsautomatische Injection

In webman ist die automatische Injection von Abhängigkeiten eine optionale Funktion, die standardmäßig deaktiviert ist. Wenn Sie die automatische Injection von Abhängigkeiten benötigen, wird empfohlen, [php-di](https://php-di.org/doc/getting-started.html) zu verwenden. Nachfolgend finden Sie die Verwendung von webman in Kombination mit `php-di`.

## Installation
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Ändern Sie die Konfiguration `config/container.php` wie folgt:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> In `config/container.php` wird letztendlich eine Instanz eines Containers zurückgegeben, die dem `PSR-11`-Standard entspricht. Wenn Sie `php-di` nicht verwenden möchten, können Sie hier eine andere Instanz erstellen und zurückgeben, die dem `PSR-11`-Standard entspricht.

## Konstruktor Injection
Erstellen Sie `app/service/Mailer.php` (wenn das Verzeichnis nicht vorhanden ist, erstellen Sie es bitte) mit dem folgenden Inhalt:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Versandcode für E-Mails ausgelassen
    }
}
```

Der Inhalt von `app/controller/UserController.php` lautet wie folgt:

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
        $this->mailer->mail('hello@webman.com', 'Hallo und willkommen!');
        return response('ok');
    }
}
```
In der Regel sind folgende Codes erforderlich, um die Instanziierung von `app\controller\UserController` abzuschließen:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Durch die Verwendung von `php-di` ist es jedoch nicht erforderlich, `Mailer` im Controller manuell zu instanziieren. Webman übernimmt das automatisch für Sie. Wenn bei der Instanziierung von `Mailer` andere Klassendependenzen vorhanden sind, instanziiert und injiziert webman diese ebenfalls automatisch. Der Entwickler muss keine Initialisierungsarbeiten durchführen.

> **Beachten**
> Nur Instanzen, die von Frameworks oder `php-di` erstellt wurden, können die automatische Abhängigkeitsinjektion abschließen. Instanzen, die manuell mit `new` erstellt wurden, können die automatische Abhängigkeitsinjektion nicht abschließen. Wenn eine Injektion erforderlich ist, verwenden Sie die `support\Container`-Schnittstelle anstelle der `new`-Anweisung, z.B.:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Instanzen, die mit dem Schlüsselwort 'new' erstellt wurden, können nicht abhängig gemacht werden
$user_service = new UserService;
// Instanzen, die mit dem Schlüsselwort 'new' erstellt wurden, können nicht abhängig gemacht werden
$log_service = new LogService($pfad, $name);

// Instanzen, die von Container erstellt wurden, können abhängig gemacht werden
$user_service = Container::get(UserService::class);
// Instanzen, die von Container erstellt wurden, können abhängig gemacht werden
$log_service = Container::make(LogService::class, [$pfad, $name]);
```

## Annotation Injection
Neben der Konstruktor-Dependency-Injection können wir auch die Annotation-Injection verwenden. Im obigen Beispiel wird der `app\controller\UserController` wie folgt geändert:
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
        $this->mailer->mail('hello@webman.com', 'Hallo und willkommen!');
        return response('ok');
    }
}
```
In diesem Beispiel wird die Injection über `@Inject` durchgeführt und der Objekttyp wird über `@var` deklariert. Dieses Beispiel erzielt das gleiche Ergebnis wie die Konstruktorinjektion, ist aber kompakter im Code.

> **Beachten**
> Webman unterstützte vor Version 1.4.6 keine Controller-Parameterinjektion. Beispielsweise wird der folgende Code nicht unterstützt, wenn webman<=1.4.6 ist:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Vor Version 1.4.6 wird die Controllerparameterinjektion nicht unterstützt
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hallo und willkommen!');
        return response('ok');
    }
}
```

## Benutzerdefinierte Konstruktorinjektion
Manchmal sind die Parameter, die an den Konstruktor übergeben werden, möglicherweise keine Klasseninstanzen, sondern Daten wie Zeichenfolgen, Zahlen, Arrays usw. Beispielsweise benötigt der Konstruktor von Mailer den SMTP-Server-IP und den Port:
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
        // Versandcode für E-Mails ausgelassen
    }
}
```
In diesem Fall kann die Konstruktorinjektion wie zuvor gezeigt nicht direkt verwendet werden, da `php-di` den Wert von `$smtp_host` und `$smtp_port` nicht kennt. In solchen Fällen kann eine benutzerdefinierte Injektion versucht werden.

Fügen Sie in `config/dependence.php` (erstellen Sie die Datei, wenn sie nicht existiert) den folgenden Code hinzu:
```php
return [
    // ... andere Konfigurationen hier ignoriert
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Wenn eine Instanz von `app\service\Mailer` benötigt wird, wird automatisch die in dieser Konfiguration erstellte Instanz von `app\service\Mailer` verwendet.

Wir beachten, dass `config/dependence.php` `new` zur Instanziierung der Klasse `Mailer` verwendet. Dies ist in diesem Beispiel kein Problem, aber stellen Sie sich vor, wenn die Klasse `Mailer` von anderen Klassen abhängig ist oder Annotation-Injection verwendet, wird die Initialisierung mit `new` die automatische Injektion der Abhängigkeiten nicht abschließen. Die Lösung besteht darin, die benutzerdefinierte Interface-Injection zu verwenden, um die Klasse mit den Methoden `Container::get(classname)` oder `Container::make(classname, [constructor_arg])` zu initialisieren.

## Benutzerdefinierte Interface-Injektion
In realen Projekten möchten wir eher mit Schnittstellen als mit konkreten Klassen arbeiten. Beispielsweise sollte `app\controller\UserController` die `app\service\MailerInterface` und nicht `app\service\Mailer` einbeziehen.

Definieren Sie das `MailerInterface`-Interface wie folgt:
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Implementieren Sie das `MailerInterface`-Interface wie folgt:
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
        // Versandcode für E-Mails ausgelassen
    }
}
```

Nun müssen Sie das `MailerInterface`-Interface anstelle der konkreten Implementierung einbeziehen.
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
        $this->mailer->mail('hello@webman.com', 'Hallo und willkommen!');
        return response('ok');
    }
}
```

Die `config/dependence.php` definiert die Implementierung des `MailerInterface`-Interfaces, wie folgt:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Daher wird automatisch die Implementierung von `Mailer` verwendet, wenn das Geschäft `MailerInterface`-Interface benötigt.

> Der Vorteil der Programmierung mit Schnittstellen besteht darin, dass, wenn eine Komponente ausgetauscht werden muss, keine Änderungen am Geschäftscode erforderlich sind. Es ist nur notwendig, die konkrete Implementierung in `config/dependence.php` zu ändern. Dies ist auch für Unit-Tests sehr nützlich.

## Andere benutzerdefinierte Injektionen
In `config/dependence.php` können neben der Definition der Abhängigkeiten von Klassen auch andere Werte wie Zeichenfolgen, Zahlen, Arrays usw. definiert werden.

Beispielsweise definiert `config/dependence.php` wie folgt:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Deshalb kann über `@Inject` `smtp_host` und `smtp_port` in die Klasseneigenschaften injiziert werden.
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
        // Versandcode für E-Mails ausgelassen
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // wird 192.168.1.11:25 ausgeben
    }
}
```

> Beachten Sie: Innerhalb von `@Inject("key")` befindet sich ein String.

## Weitere Informationen
Bitte beachten Sie das [Handbuch von php-di](https://php-di.org/doc/getting-started.html)
