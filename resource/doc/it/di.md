# Dipendenza di auto-iniezione

In webman, la dipendenza di auto-iniezione è una funzionalità opzionale e di default è disattivata. Se hai bisogno della dipendenza di auto-iniezione, ti consigliamo di utilizzare [php-di](https://php-di.org/doc/getting-started.html). Di seguito è riportato come webman è combinato con `php-di`.

## Installazione
``` 
compositore richiede psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
``` 
Modificare la configurazione `config/container.php`, il cui contenuto finale è il seguente: 
```php 
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dipendenza', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
``` 
> Il file `config/container.php` restituisce un'istanza del contenitore in conformità con lo standard `PSR-11`. Se non si desidera utilizzare `php-di`, è possibile creare e restituire un'altra istanza del contenitore in conformità con lo standard `PSR-11` qui.

## Iniezione del costruttore
Crea il file `app/service/Mailer.php` (se la cartella non esiste, creala) con il seguente contenuto: 
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
Il contenuto di `app/controller/UserController.php` è il seguente: 
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
Nelle normali circostanze, il codice seguente è necessario per istanziare `app\controller\UserController`: 
```php 
$mailer = new Mailer;
$user = new UserController($mailer);
``` 
Dopo aver utilizzato `php-di`, il programmatore non deve istanziare manualmente il `Mailer` all'interno del controller e webman lo farà automaticamente per te. Se durante il processo di istanziazione del `Mailer` ci sono altre dipendenze di classe, webman le istanzierà e le inietterà automaticamente. Il programmatore non ha bisogno di alcun lavoro iniziale.

> **Nota**
> Solo le istanze create dal framework o da `php-di` possono completare l'iniezione di dipendenze automatica. Le istanze create manualmente con `new` non possono completare l'iniezione di dipendenze. Per eseguire l'iniezione, è necessario utilizzare l'interfaccia `support\Container` al posto dello statement `new`, ad esempio:

```php 
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Le istanze create con la parola chiave 'new' non possono essere iniettate come dipendenze
$user_service = new UserService;
// Le istanze create con la parola chiave 'new' non possono essere iniettate come dipendenze
$log_service = new LogService($path, $name);

// Le istanze create con Container possono essere iniettate come dipendenze
$user_service = Container::get(UserService::class);
// Le istanze create con Container possono essere iniettate come dipendenze
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Iniezione dell'annotazione
Oltre all'iniezione del costruttore delle dipendenze automatiche, è possibile utilizzare anche l'iniezione dell'annotazione. Continuando con l'esempio precedente, il controller `app\controller\UserController` viene modificato come segue: 
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
In questo esempio, l'iniezione avviene tramite l'annotazione `@Inject` e il tipo dell'oggetto è dichiarato tramite l'annotazione `@var`. Questo esempio è equivalente all'iniezione del costruttore, ma il codice è più conciso.

> **Nota**
> Webman non supporta l'iniezione dei parametri del controller prima della versione 1.4.6, ad esempio il seguente codice non è supportato quando webman <= 1.4.6: 

```php 
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // Prima della versione 1.4.6, l'iniezione dei parametri del controller non è supportata
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Ciao e benvenuto!');
        return response('ok');
    }
}
``` 

## Iniezione personalizzata dei costruttori

A volte i parametri passati al costruttore possono non essere istanze della classe, ma stringhe, numeri, array e così via. Ad esempio, il costruttore del Mailer richiede l'indirizzo IP e la porta del server SMTP:
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
In questo caso, non è possibile utilizzare direttamente l'iniezione del costruttore come descritto in precedenza, poiché `php-di` non può determinare i valori di `$smtp_host` e `$smtp_port`. In questo caso, si può provare con l'iniezione personalizzata.

Nel file `config/dependence.php` (se il file non esiste, crealo) aggiungi il seguente codice: 
```php 
return [
// ... Altre configurazioni omesse qui

app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25);
];
``` 
In questo modo, quando la dipendenza ha bisogno di ottenere un'istanza di `app\service\Mailer`, userà automaticamente l'istanza di `app\service\Mailer` creata in questa configurazione.

Notiamo che in `config/dependence.php` si utilizza `new` per istanziare la classe `Mailer`, che non è un problema in questo esempio, ma immagina se la classe `Mailer` dipendesse da altre classi o se all'interno della classe `Mailer` fosse utilizzata l'iniezione dell'annotazione, l'inizializzazione con `new` non causerà l'iniezione di dipendenze automatica. La soluzione è utilizzare l'iniezione personalizzata dell'interfaccia, utilizzando il metodo `Container::get(NomeClasse)` o `Container::make(NomeClasse, [Parametri del costruttore])` per inizializzare la classe.
## Iniezione personalizzata dell'interfaccia
Nel contesto di un progetto reale, preferiamo programmare attraverso delle interfacce piuttosto che classi concrete. Ad esempio,invece di importare `app\service\Mailer`, dovremmo importare `app\service\MailerInterface` nella classe `app\controller\UserController`.

Definiamo l'interfaccia `MailerInterface`.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Definiamo l'implementazione dell'interfaccia `MailerInterface`.
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
        // Codice di invio email omesso
    }
}
```

Importiamo l'interfaccia `MailerInterface` anziché l'implementazione concreta.
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

`config/dependence.php` definisce l'implementazione dell'interfaccia `MailerInterface` come segue.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

In questo modo, quando il business necessita di utilizzare l'interfaccia `MailerInterface`, verrà automaticamente utilizzata l'implementazione `Mailer`.

> Il vantaggio della programmazione orientata all'interfaccia è che, se dobbiamo sostituire un componente, non è necessario modificare il codice di business, ma solo l'implementazione concreta in `config/dependence.php`. Questo è particolarmente utile anche per il testing unitario.

## Altre Iniezioni personalizzate
In `config/dependence.php`, oltre a definire le dipendenze delle classi, è possibile definire anche altri tipi di valori come stringhe, numeri, array, ecc.

Ad esempio, in `config/dependence.php` viene definito quanto segue:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

A questo punto possiamo utilizzare l'annotazione `@Inject` per iniettare `smtp_host` e `smtp_port` nelle proprietà della classe.
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

> Nota: `@Inject("key")` usa le virgolette.

## Ulteriori informazioni
Si prega di fare riferimento al [manuale di php-di](https://php-di.org/doc/getting-started.html)
