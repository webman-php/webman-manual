# Limiteur de débit

Limiteur de débit webman avec support de limitation par annotation.
Prend en charge les pilotes apcu, redis et memory.

## Dépôt source

https://github.com/webman-php/limiter

## Installation

```
composer require webman/limiter
```

## Utilisation

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Par défaut limitation par IP, fenêtre temporelle par défaut 1 seconde
        return 'Maximum 10 requêtes par IP par seconde';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, limitation par ID utilisateur, exige session('user.id') non vide
        return 'Maximum 100 recherches par utilisateur par 60 secondes';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: '1 seul e-mail par personne par minute')]
    public function sendMail(): string
    {
        // key: Limit::SID, limitation par session_id
        return 'E-mail envoyé avec succès';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Coupons du jour épuisés, réessayez demain')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Chaque utilisateur ne peut réclamer qu\'un coupon par jour')]
    public function coupon(): string
    {
        // key: 'coupon', clé personnalisée pour limitation globale, max 100 coupons par jour
        // Aussi limitation par ID utilisateur, chaque utilisateur un seul coupon par jour
        return 'Coupon envoyé avec succès';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Maximum 5 SMS par numéro par jour')]
    public function sendSms2(): string
    {
        // Quand key est variable: [classe, méthode_statique], ex. [UserController::class, 'getMobile'] utilise la valeur de retour de UserController::getMobile() comme clé
        return 'SMS envoyé avec succès';
    }

    /**
     * Clé personnalisée, obtenir le numéro de mobile, doit être une méthode statique
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Débit limité', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Exception par défaut en cas de dépassement: support\limiter\RateLimitException, modifiable via paramètre exception
        return 'ok';
    }

}
```

**Notes**

* Utilise l'algorithme à fenêtre fixe
* Fenêtre temporelle ttl par défaut: 1 seconde
* Définir la fenêtre via ttl, ex. `ttl:60` pour 60 secondes
* Dimension de limitation par défaut: IP (par défaut `127.0.0.1` non limité, voir configuration ci-dessous)
* Intégré: limitation IP, UID (exige `session('user.id')` non vide), SID (par `session_id`)
* Avec proxy nginx, passer l'en-tête `X-Forwarded-For` pour limitation IP, voir [proxy nginx](../others/nginx-proxy.md)
* Déclenche `support\limiter\RateLimitException` en cas de dépassement, classe d'exception personnalisée via `exception:xx`
* Message d'erreur par défaut en cas de dépassement: `Too Many Requests`, message personnalisé via `message:xx`
* Message d'erreur par défaut modifiable via [traduction](translation.md), référence Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

Parfois les développeurs veulent appeler le limiteur directement dans le code, voir l'exemple suivant:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile utilisé comme clé ici
        Limiter::check($mobile, 5, 24*60*60, 'Maximum 5 SMS par numéro par jour');
        return 'SMS envoyé avec succès';
    }
}
```

## Configuration

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Ces IP ne sont pas limitées (efficace uniquement quand key est Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Activer la limitation de débit
* **driver**: Une des valeurs `auto`, `apcu`, `memory`, `redis`; `auto` choisit automatiquement entre `apcu` (prioritaire) et `memory`
* **stores**: Configuration Redis, `connection` correspond à la clé dans `config/redis.php`
* **ip_whitelist**: Les IP en liste blanche ne sont pas limitées (efficace uniquement quand key est `Limit::IP`)

## Choix du driver

**memory**

* Introduction
  Aucune extension requise, meilleures performances.

* Limites
  Limitation uniquement pour le processus actuel, pas de partage entre processus, limitation en cluster non supportée.

* Cas d'usage
  Environnement de développement Windows; scénarios sans limitation stricte; défense contre attaques CC.

**apcu**

* Installation de l'extension
  Extension apcu requise, paramètres php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

Emplacement de php.ini via `php --ini`

* Introduction
  Excellentes performances, supporte le partage multi-processus.

* Limites
  Cluster non supporté

* Cas d'usage
  Tout environnement de développement; limitation serveur unique en production; cluster sans limitation stricte; défense contre attaques CC.

**redis**

* Dépendances
  Extension redis et composant Redis requis, installation:

```
composer require -W webman/redis illuminate/events
```

* Introduction
  Performances inférieures à apcu, supporte limitation précise serveur unique et cluster

* Cas d'usage
  Environnement de développement; serveur unique en production; environnement cluster
