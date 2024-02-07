# Bibliothèque d'événements webman webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Les avantages des événements par rapport aux middlewares sont que les événements permettent un ciblage plus précis (ou une granularité plus fine) que les middlewares, et conviennent mieux à certaines extensions de scénarios métier. Par exemple, nous rencontrons souvent des opérations à effectuer après l'inscription ou la connexion de l'utilisateur. Grâce au système d'événements, il est possible d'étendre les opérations de connexion sans altérer le code existant, ce qui réduit le couplage du système et diminue la probabilité de bugs.

## Adresse du projet

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dépendance

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installation

```shell script
composer require tinywan/webman-event
```
## Configuration

Le fichier de configuration des événements `config/event.php` contient le contenu suivant:

```php
return [
    // Écouteurs d'événements
    'listener'    => [],

    // Abonnés aux événements
    'subscriber' => [],
];
```
### Configuration du démarrage des processus

Ouvrez `config/bootstrap.php` et ajoutez la configuration suivante:

```php
return [
    // Other configurations here...
    webman\event\EventManager::class,
];
```
## Démarrage rapide

### Définition des événements

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

### Ecoute de l'événement
```php
return [
    // Écouteurs d'événements
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Abonnement à l'événement
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
     * @desc: Déclenchement de l'événement
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Quelques logiques métier spécifiques
        var_dump($event->handle());
    }
}
```

Abonnement à l'événement
```php
return [
    // Abonnés aux événements
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Déclencheur d'événement

Déclencher l'événement `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Message d'erreur',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Résultat de l'exécution

![Résultat d'impression](./trigger.png)

## Licence

Ce projet est sous licence [Apache 2.0](LICENSE).
