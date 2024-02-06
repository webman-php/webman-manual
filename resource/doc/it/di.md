# Iniezione automatica delle dipendenze
Nel framework webman, l'iniezione automatica delle dipendenze è una funzionalità opzionale che di default è disattivata. Se hai bisogno dell'iniezione automatica delle dipendenze, è consigliato utilizzare [php-di](https://php-di.org/doc/getting-started.html). Di seguito è riportato l'uso di webman in combinazione con `php-di`.

## Installazione
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modifica la configurazione `config/container.php`, che avrà il seguente contenuto finale:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` deve restituire un'istanza di un contenitore conforme allo standard `PSR-11`. Se non si desidera utilizzare `php-di`, è possibile creare e restituire un'altra istanza di contenitore conforme allo standard `PSR-11`.

## Iniezione nel costruttore
Crea il file `app/service/Mailer.php` (se la cartella non esiste, createla manualmente) con il seguente contenuto:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Codice di invio email omesso
    }
}
```

Il file `app/controller/UserController.php` avrà il seguente contenuto:
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
        $this->mailer->mail('hello@webman.com', 'Ciao e benvenuto!');
        return response('ok');
    }
}
```
Normalmente, è necessario il seguente codice per istanziare `app\controller\UserController`:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Con l'uso di `php-di`, l'istanziazione manuale di `Mailer` nel controller sarà gestita automaticamente da webman. Se durante il processo di istanziazione di `Mailer` ci sono altre dipendenze di classi, webman provvederà a istanziarle automaticamente e ad iniettarle. Non è richiesto alcun lavoro di inizializzazione da parte dello sviluppatore.

> **Nota**
> Solo le istanze create dal framework o da `php-di` saranno completati con l'iniezione automatica delle dipendenze. Le istanze create manualmente con `new` non potranno beneficiare dell'iniezione automatica delle dipendenze. Se è necessaria l'iniezione, è necessario utilizzare l'interfaccia del `support\Container` al posto della dichiarazione `new`, ad esempio:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Le istanze create con la parola chiave `new` non possono beneficiare dell'iniezione delle dipendenze
$user_service = new UserService;
// Le istanze create con la parola chiave `new` non possono beneficiare dell'iniezione delle dipendenze
$log_service = new LogService($path, $name);

// Le istanze create con Container possono beneficiare dell'iniezione delle dipendenze
$user_service = Container::get(UserService::class);
// Le istanze create con Container possono beneficiare dell'iniezione delle dipendenze
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Iniezione tramite annotazioni
Oltre all'iniezione delle dipendenze nel costruttore, è possibile utilizzare l'iniezione tramite annotazioni. Proseguendo con l'esempio precedente, il file `app\controller\UserController` avrà il seguente aspetto:
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
        $this->mailer->mail('hello@webman.com', 'Ciao e benvenuto!');
        return response('ok');
    }
}
```
In questo esempio, l'iniezione avviene tramite l'annotazione `@Inject` e il tipo dell'oggetto è dichiarato tramite l'annotazione `@var`. Questo metodo è equivalente all'iniezione tramite costruttore, ma con un codice più compatto.

> **Nota**
> Prima della versione 1.4.6, webman non supportava l'iniezione dei parametri del controller, quindi il seguente codice non era supportato quando webman <=1.4.6:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Non supportato fino alla versione 1.4.6
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Ciao e benvenuto!');
        return response('ok');
    }
}
```

## Iniezione personalizzata nel costruttore
A volte i parametri passati al costruttore potrebbero non essere istanze di classe, ma stringhe, numeri, array e così via. Ad esempio, il costruttore di Mailer richiede l'indirizzo IP e la porta del server SMTP:
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
        // Codice di invio email omesso
    }
}
```
In questo caso, non è possibile utilizzare l'iniezione automatica delle dipendenze nel costruttore come illustrato in precedenza, poiché `php-di` non può determinare i valori di `$smtp_host` e `$smtp_port`. Si può tentare un'approccio personalizzato all'iniezione.

All'interno di `config/dependence.php` (creare il file se non esiste), aggiungere il seguente codice:
```php
return [
    // ... Altre configurazioni ignorate

    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
In questo modo, quando l'iniezione delle dipendenze richiede un'istanza di `app\service\Mailer`, verrà automaticamente utilizzata l'istanza di `app\service\Mailer` creata in questa configurazione.

Notiamo che in `config/dependence.php` è stato utilizzato `new` per istanziare la classe `Mailer`. In questo esempio non ci sono problemi, ma immaginiamo se la classe `Mailer` avesse dipendenze su altre classi o se la classe `Mailer` utilizzasse l'iniezione tramite annotazioni interne, l'uso di `new` per l'inizializzazione non avrebbe permesso l'iniezione automatica delle dipendenze. La soluzione è utilizzare un'interfaccia personalizzata per l'iniezione, utilizzando il metodo `Container::get(NomeClasse)` o `Container::make(NomeClasse, [ParametriCostruttore])` per inizializzare la classe.

## Iniezione tramite interfaccia personalizzata
Nei progetti reali, è preferibile programmare in base a un'interfaccia anziché a una classe specifica. Ad esempio, in `app\controller\UserController` sarebbe meglio utilizzare `app\service\MailerInterface` anziché `app\service\Mailer`.

Definire l'interfaccia `MailerInterface`.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Definire una classe che implementi l'interfaccia `MailerInterface`.
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    // Codice omesso
}
```

Includere l'interfaccia `MailerInterface` anziché l'implementazione specifica.
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
        $this->mailer->mail('hello@webman.com', 'Ciao e benvenuto!');
        return response('ok');
    }
}
```

Nel file `config/dependence.php`, definire l'implementazione dell'interfaccia `MailerInterface` come segue.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

In questo modo, quando il business richiede l'uso di `MailerInterface`, verrà automaticamente utilizzata l'implementazione di `Mailer`.

> Il vantaggio della programmazione orientata all'interfaccia è che, se si desidera sostituire un componente, non è necessario modificare il codice di business, ma sarà sufficiente modificare l'implementazione specifica all'interno di `config/dependence.php`. Questo è particolarmente utile anche per i test unitari.

## Altri tipi di iniezione personalizzata
Il file `config/dependence.php` non serve solo a definire le dipendenze delle classi, ma è possibile definire anche altri valori come stringhe, numeri, array e altro ancora.

Ad esempio, definire quanto segue nel file `config/dependence.php`:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

In questo modo, è possibile iniettare `smtp_host` e `smtp_port` come attributi di classe utilizzando `@Inject`.
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
        // Codice di invio email omesso
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Stamperà 192.168.1.11:25
    }
}
```

> Nota: All'interno di `@Inject("key")`, vengono utilizzate le virgolette doppie.

## Per ulteriori informazioni
Si prega di fare riferimento al [manuale di php-di](https://php-di.org/doc/getting-started.ht)
