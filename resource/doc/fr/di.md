# Injection de dépendances automatique

Dans webman, l'injection de dépendances automatique est une fonctionnalité facultative qui est désactivée par défaut. Si vous avez besoin de l'injection de dépendances automatique, il est recommandé d'utiliser [php-di](https://php-di.org/doc/getting-started.html). Voici comment webman utilise `php-di`.

## Installation
```composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14```

Modifiez la configuration `config/container.php` pour qu'elle ait le contenu final suivant :
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> La configuration `config/container.php` doit finalement retourner une instance de conteneur conforme à la spécification `PSR-11`. Si vous ne souhaitez pas utiliser `php-di`, vous pouvez créer et retourner une autre instance de conteneur conforme à la spécification `PSR-11` ici.

## Injection par constructeur
Créez un nouveau fichier `app/service/Mailer.php` (créez-le vous-même s'il n'existe pas) avec le contenu suivant :
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Code d'envoi du courrier électronique omis
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
Dans des circonstances normales, le code suivant est nécessaire pour instancier `app\controller\UserController` :
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Lorsque `php-di` est utilisé, le développeur n'a pas besoin d'instancier manuellement `Mailer` dans le contrôleur, webman le fera automatiquement. Si le processus d'instanciation de `Mailer` nécessite d'autres dépendances, webman les instanciera et les injectera automatiquement également. Aucun travail d'initialisation n'est nécessaire pour le développeur.

> **Remarque**
> Seules les instances créées par le framework ou `php-di` peuvent bénéficier de l'injection de dépendances automatique. Les instances créées manuellement avec `new` ne peuvent pas bénéficier de l'injection de dépendances automatique. Pour injecter des dépendances, utilisez l'interface `support\Container` à la place de l'instruction `new`, par exemple :

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// Les instances créées avec le mot-clé new ne bénéficient pas de l'injection de dépendances
$user_service = new UserService;
// Les instances créées avec le mot-clé new ne bénéficient pas de l'injection de dépendances
$log_service = new LogService($path, $name);

// Les instances créées avec le conteneur peuvent bénéficier de l'injection de dépendances
$user_service = Container::get(UserService::class);
// Les instances créées avec le conteneur peuvent bénéficier de l'injection de dépendances
$log_service = Container::make(LogService::class, [$path, $name]);
```
## Autres injections personnalisées
En plus de pouvoir définir les dépendances de classes, le fichier `config/dependence.php` permet également de définir d'autres valeurs telles que des chaînes de caractères, des nombres, des tableaux, etc.

Par exemple, si le fichier `config/dependence.php` est défini comme suit :
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

À ce moment-là, nous pouvons injecter les propriétés `smtp_host` et `smtp_port` dans la classe à l'aide de `@Inject`.
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
        // Code d'envoi d'e-mails omis
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Affichera 192.168.1.11:25
    }
}
```

> Remarque : dans `@Inject("key")`, les guillemets sont nécessaires.

## Pour en savoir plus
Veuillez consulter le [manuel de php-di](https://php-di.org/doc/getting-started.html)
