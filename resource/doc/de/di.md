# Abhängigkeitsinjektion (DI)

In webman ist die automatische Abhängigkeitsinjektion optional und standardmäßig deaktiviert. Wenn Sie die automatische Abhängigkeitsinjektion benötigen, empfehlen wir die Verwendung von [php-di](https://php-di.org/doc/getting-started.html). Im Folgenden finden Sie die Verwendung von `php-di` in Verbindung mit webman.

## Installation
```php
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

> Die Datei `config/container.php` gibt letztendlich eine Instanz eines Containers zurück, die dem `PSR-11`-Standard entspricht. Wenn Sie `php-di` nicht verwenden möchten, können Sie hier eine andere Instanz erstellen und zurückgeben, die dem `PSR-11`-Standard entspricht.

## Konstruktorinjektion
Legen Sie den Inhalt von `app/service/Mailer.php` (erstellen Sie das Verzeichnis, wenn es nicht existiert) wie folgt fest:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Senden der E-Mail-Code ausgelassen
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
In der Regel benötigen Sie den folgenden Code, um die Instanziierung von `app\controller\UserController` abzuschließen:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Nach der Verwendung von `php-di` muss der Entwickler das `Mailer` im Controller nicht mehr manuell instanziieren, webman erledigt dies automatisch für Sie. Wenn bei der Instanziierung des `Mailer` zusätzliche Klassenabhängigkeiten vorhanden sind, werden diese ebenfalls automatisch instanziiert und eingefügt. Der Entwickler muss keine Initialisierungsarbeiten durchführen.

> **Achtung**
> Die automatische Abhängigkeitsinjektion ist nur möglich, wenn die Instanz von einem Framework oder `php-di` erstellt wird. Eine manuell erstellte Instanz mit dem `new`-Schlüsselwort kann keine automatische Abhängigkeitsinjektion durchführen. Wenn die Injektion erforderlich ist, verwenden Sie das `support/Container`-Interface anstelle der `new`-Anweisung, z. B.:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Eine mit dem Schlüsselwort "new" erstellte Instanz kann keine Abhängigkeiten injizieren
$user_service = new UserService;
// Eine mit dem Schlüsselwort "new" erstellte Instanz kann keine Abhängigkeiten injizieren
$log_service = new LogService($path, $name);

// Eine mit "Container" erstellte Instanz kann Abhängigkeiten injizieren
$user_service = Container::get(UserService::class);
// Eine mit "Container" erstellte Instanz kann Abhängigkeiten injizieren
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Annotationsinjektion
Neben der Konstruktorabhängigkeitsinjektion können wir auch die Annotationsinjektion verwenden. Setzen Sie das Beispiel von `app\controller\UserController` wie folgt fort:
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
In diesem Beispiel wird die Injektion durch die Verwendung des `@Inject`-Annotations-Kommentars und die Deklaration des Objekttyps mit dem `@var`-Kommentar erreicht. Diese Methode liefert das gleiche Ergebnis wie die Konstruktorinjektion, ist jedoch kürzer im Code.

> **Achtung**
> webman unterstützt bis zur Version 1.4.6 keine Controller-Parameterinjektion. Der folgende Code wird nicht unterstützt, wenn webman <= 1.4.6 verwendet wird:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Vor Version 1.4.6 wird die Controller-Parameterinjektion nicht unterstützt
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hallo und willkommen!');
        return response('ok');
    }
}
```

## Benutzerdefinierte Konstruktorinjektion
Manchmal sind die Parameter, die dem Konstruktor übergeben werden, keine Klasseninstanzen, sondern Daten wie Zeichenfolgen, Zahlen, Arrays usw. Zum Beispiel erfordert der Mailer-Konstruktor die Übermittlung der SMTP-Server-IP und des Ports:
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
        // Senden der E-Mail-Code ausgelassen
    }
}
```
In solchen Fällen kann die Konstruktorinjektion mit vorherigen Methoden nicht direkt verwendet werden, da `php-di` den Wert von `$smtp_host` und `$smtp_port` nicht kennt. In diesem Fall können Sie versuchen, eine benutzerdefinierte Injektion zu verwenden.

Fügen Sie in `config/dependence.php` (falls die Datei nicht existiert, erstellen Sie sie) den folgenden Code hinzu:
```php
return [
    // ... Andere Konfigurationen hier ausgelassen
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Wenn eine Instanz von `app\service\Mailer` benötigt wird, wird automatisch die in dieser Konfiguration erstellte Instanz von `app\service\Mailer` verwendet.

Wir beachten, dass in `config/dependence.php` das Schlüsselwort `new` zur Instanziierung der Mailer-Klasse verwendet wird. In diesem Beispiel gibt es kein Problem damit, aber stellen Sie sich vor, dass die `Mailer`-Klasse von anderen Klassen abhängt oder Annotationsinjektionen innerhalb der `Mailer`-Klasse verwendet werden. Die Verwendung von `new` zur Initialisierung verhindert die automatische Abhängigkeitsinjektion. Die Lösung besteht darin, benutzerdefinierte Schnittstelleninjektionen zu verwenden, indem Sie die Methoden `Container::get(ClassName)` oder `Container::make(ClassName, [ConstructorParameters])` zum Initialisieren der Klassen verwenden.

## Benutzerdefinierte Schnittstelleninjektion
In realen Projekten bevorzugen wir es, gegen eine Schnittstelle zu programmieren, anstatt gegen konkrete Klassen. Zum Beispiel sollte `app\controller\UserController` `app\service\MailerInterface` anstelle von `app\service\Mailer` importieren.

Definieren Sie das `MailerInterface`-Interface.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Definieren Sie die Implementierung des `MailerInterface`-Interfaces.
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
        // Senden der E-Mail-Code ausgelassen
    }
}
```

Importieren Sie das `MailerInterface`-Interface anstelle der konkreten Implementierung.
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

Definieren Sie in `config/dependence.php` die Implementierung des `MailerInterface`-Interfaces wie folgt.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```
So wird automatisch die Implementierung von `Mailer` verwendet, wenn das Business `MailerInterface` benötigt.

> Das Programmieren gegen Schnittstellen ist vorteilhaft, da bei Bedarf das Hinzufügen oder Austauschen einer Komponente kein Ändern des Businesscodes erfordert. Es reicht aus, die konkrete Implementierung in `config/dependence.php` zu ändern. Dies ist auch beim Unit-Testing sehr nützlich.
## Andere benutzerdefinierte Injektionen
In `config/dependence.php` können neben der Definition von Klassenabhängigkeiten auch andere Werte wie Zeichenfolgen, Zahlen, Arrays usw. definiert werden.

Zum Beispiel, in `config/dependence.php` definiert wie folgt:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Dann können wir `@Inject` verwenden, um `smtp_host` und `smtp_port` in die Klassenattribute einzufügen.
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
        // Code zum Senden von E-Mails ausgelassen
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // wird 192.168.1.11:25 ausgeben
    }
}
```

> Hinweis: In `@Inject("key")` befindet sich der Schlüssel in Anführungszeichen

## Weitere Informationen
Bitte beachten Sie das [php-di Handbuch](https://php-di.org/doc/getting-started.html)
