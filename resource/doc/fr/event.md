# webman Bibliothèque d'événements webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Les événements ont l'avantage par rapport aux middlewares d'être plus précisément positionnés (ou d'avoir une granularité plus fine) et conviennent mieux à certaines extensions de scénarios commerciaux. Par exemple, nous rencontrons souvent des situations où un certain nombre d'opérations doivent être effectuées après l'inscription ou la connexion de l'utilisateur. Le système d'événements permet de réaliser l'extension de l'opération de connexion sans affecter le code existant, ce qui réduit la couplage du système et diminue la probabilité d'erreurs.

## Adresse du projet

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dépendances

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installation

```shell script
composer require tinywan/webman-event
```
## Configuration

Le fichier de configuration des événements `config/event.php` contient le contenu suivant :

```php
return [
    // Écouteur d'événements
    'listener'    => [],

    // Abonné d'événements
    'subscriber' => [],
];
```
### Configuration du démarrage du processus

Ouvrez le fichier `config/bootstrap.php` et ajoutez la configuration suivante :

```php
return [
    // Les autres configurations sont omises ici ...
    webman\event\EventManager::class,
];
```
## Démarrage rapide

### Définition de l'événement

Classe d'événement `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Nom de l'événement, identifiant unique de l'événement

    /** @var array */
    public array $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        return $this->log;
    }
}
```

### Écouter l'événement

```php
return [
    // Écouteur d'événements
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Abonner à l'événement

Classe d'abonnement `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Description de la méthode
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Déclencher l'événement
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Quelques logiques commerciales spécifiques
        var_dump($event->handle());
    }
}
```

Abonnement à l'événement
```php
return [
    // Abonné à l'événement
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Déclencheur d'événements

Déclencher l'événement `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Message d'erreur',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Résultat de l'exécution

![Résultat de l'impression](./trigger.png)

## Licence

Ce projet est sous licence [Apache 2.0](LICENSE).
