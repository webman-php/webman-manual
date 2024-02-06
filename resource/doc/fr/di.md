# Injection de dépendances automatique

Dans webman, l'injection de dépendances automatique est une fonctionnalité facultative et est désactivée par défaut. Si vous avez besoin de l'injection de dépendances automatique, il est recommandé d'utiliser [php-di](https://php-di.org/doc/getting-started.html). Voici comment webman s'intègre avec `php-di`.

## Installation
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Modifiez ensuite le fichier de configuration `config/container.php` comme suit :
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> Le fichier `config/container.php` doit retourner une instance de conteneur conforme à la spécification `PSR-11`. Si vous ne souhaitez pas utiliser `php-di`, vous pouvez créer et retourner ici une autre instance de conteneur conforme à la spécification `PSR-11`.

## Injection par constructeur
Créez un nouveau fichier `app/service/Mailer.php` (créez le répertoire s'il n'existe pas) avec le contenu suivant :
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Code d'envoi d'e-mail omis
    }
}
```

Le contenu du fichier `app/controller/UserController.php` est le suivant :
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
        $this->mailer->mail('hello@webman.com', 'Bonjour et bienvenue !');
        return response('ok');
    }
}
```
Normalement, les lignes de code suivantes sont nécessaires pour instancier `app\controller\UserController` :
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Avec l'utilisation de `php-di`, webman se chargera automatiquement de l'instanciation du `Mailer` dans le contrôleur. Si l'instanciation du `Mailer` implique des dépendances vers d'autres classes, webman les instanciera également et les injectera automatiquement. Les développeurs n'ont besoin d'effectuer aucun travail d'initialisation.

> **Remarque**
> Seules les instances créées par le framework ou `php-di` peuvent bénéficier de l'injection de dépendances automatique. Les instances créées manuellement avec `new` ne peuvent pas bénéficier de l'injection de dépendances automatique. Si vous devez injecter des dépendances, vous devez utiliser l'interface `support\Container` à la place de l'instruction `new`, par exemple :

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Les instances créées avec le mot clé new ne peuvent pas être injectées
$user_service = new UserService;
// Les instances créées avec le mot clé new ne peuvent pas être injectées
$log_service = new LogService($path, $name);

// Les instances créées avec le conteneur peuvent être injectées
$user_service = Container::get(UserService::class);
// Les instances créées avec le conteneur peuvent être injectées
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Injection par annotation
En plus de l'injection de dépendances par constructeur, il est également possible d'utiliser l'injection par annotation. Poursuivant l'exemple précédent, modifiez `app\controller\UserController` comme suit :
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
        $this->mailer->mail('hello@webman.com', 'Bonjour et bienvenue !');
        return response('ok');
    }
}
```
Dans cet exemple, l'injection est effectuée via l'annotation `@Inject`, et le type d'objet est déclaré avec l'annotation `@var`. Cet exemple offre le même résultat que l'injection par constructeur, mais avec un code plus concis.

> **Remarque**
> Avant la version 1.4.6, webman ne supportait pas l'injection de paramètres dans les contrôleurs. Par exemple, le code suivant n'est pas pris en charge par webman <=1.4.6 :

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // L'injection de paramètres dans les contrôleurs n'est pas prise en charge avant la version 1.4.6
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Bonjour et bienvenue !');
        return response('ok');
    }
}
```

## Injection personnalisée par constructeur

Il arrive que les paramètres passés au constructeur ne soient pas des instances de classe, mais des chaînes, des chiffres, des tableaux ou d'autres données. Par exemple, le constructeur de la classe Mailer nécessite l'adresse IP et le port du serveur SMTP :
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
        // Code d'envoi d'e-mail omis
    }
}
```
Dans ce cas, l'injection automatique par constructeur ne fonctionnerait pas directement, car `php-di` ne peut pas déterminer les valeurs de `$smtp_host` et `$smtp_port`. Vous pouvez alors essayer une injection personnalisée.

Ajoutez le contenu suivant au fichier `config/dependence.php` (créez le fichier s'il n'existe pas) :
```php
return [
    // ... Autres configurations omises

    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Ainsi, lorsque l'injection de dépendances nécessite une instance de `app\service\Mailer`, l'instance créée dans cette configuration sera automatiquement utilisée.

Nous notons que le fichier `config/dependence.php` utilise `new` pour instancier la classe `Mailer`. Dans cet exemple, il n'y a pas de problème, mais imaginez si la classe `Mailer` dépendait d'autres classes ou utilisait l'injection par annotation. Utiliser `new` pour l'initialisation empêcherait l'injection de dépendances automatique. La solution consiste à utiliser l'interface personnalisée pour l'injection, en utilisant les méthodes `Container::get(NomDeClasse)` ou `Container::make(NomDeClasse, [ParamètresDuConstructeur])` pour initialiser la classe.

## Injection personnalisée via interface

Dans un projet réel, il est préférable de programmer en utilisant des interfaces plutôt que des classes concrètes. Par exemple, dans `app\controller\UserController`, il est préférable d'injecter `app\service\MailerInterface` plutôt que `app\service\Mailer`.

Définissez l'interface `MailerInterface` comme suit :
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

Définissez l'implémentation de l'interface `MailerInterface` comme suit :
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
        // Code d'envoi d'e-mail omis
    }
}
```

Injectez l'interface `MailerInterface` plutôt que l'implémentation concrète. Modifiez `app\controller\UserController` comme suit :
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
        $this->mailer->mail('hello@webman.com', 'Bonjour et bienvenue !');
        return response('ok');
    }
}
```

Définissez l'implémentation de l'interface `MailerInterface` dans `config/dependence.php` comme suit :
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Ainsi, lorsqu'une dépendance nécessite l'interface `MailerInterface`, l'implémentation de `Mailer` sera automatiquement utilisée.

> L'avantage de la programmation orientée interface est que lorsque vous devez remplacer un composant, vous n'avez pas à modifier le code métier. Il suffit de modifier l'implémentation concrète dans `config/dependence.php`. Cela est particulièrement utile pour les tests unitaires.

## Autres injections personnalisées

Le fichier `config/dependence.php` peut définir non seulement les dépendances de classe, mais aussi d'autres valeurs telles que des chaînes, des chiffres, des tableaux, etc.

Par exemple, définissez les valeurs suivantes dans `config/dependence.php` :
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Ainsi, vous pouvez injecter `smtp_host` et `smtp_port` dans les attributs de classe avec `@Inject`.
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
        // Code d'envoi d'e-mail omis
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Affichera 192.168.1.11:25
    }
}
```

> Remarque : `@Inject("cle")` est spécifié entre guillemets

## Pour en savoir plus
Veuillez consulter le [manuel php-di](https://php-di.org/doc/getting-started.html)
