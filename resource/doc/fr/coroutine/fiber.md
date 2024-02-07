# Les Coroutines

> **Exigences en matière de coroutine**
> PHP >= 8.1, workerman >= 5.0, webman-framework >= 1.5, revolt/event-loop > 1.0.0
> Commande de mise à niveau de webman `composer require workerman/webman-framework ^1.5.0`
> Commande de mise à niveau de workerman `composer require workerman/workerman ^5.0.0`
> La coroutine Fiber nécessite l'installation de `composer require revolt/event-loop ^1.0.0`

# Exemple
### Réponse différée

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Dormir pendant 1,5 seconde
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` est similaire à la fonction `sleep()` native de PHP, à la différence que `Timer::sleep()` n'interrompt pas le processus.

### Envoyer une requête HTTP

> **Remarque**
> Nécessite l'installation de `composer require workerman/http-client ^2.0.0`

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Envoyer une requête asynchrone de manière synchrone
        return $response->getBody()->getContents();
    }
}
```
De la même manière, la requête `$client->get('http://example.com')` est non bloquante, ce qui peut être utilisé pour envoyer des requêtes HTTP de façon non bloquante dans webman, améliorant les performances de l'application.

Consultez également [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) pour plus d'informations.

### Ajout de la classe support\Context

La classe `support\Context` est utilisée pour stocker les données contextuelles de la requête. Lorsque la requête est terminée, les données contextuelles correspondantes sont automatiquement effacées. Autrement dit, la durée de vie des données contextuelles suit celle de la requête. `support\Context` prend en charge l'environnement de coroutine Fiber, Swoole et Swow.

### Coroutine Swoole

Après l'installation de l'extension Swoole (requise pour Swoole >= 5.0), activez les coroutines Swoole en configurant le fichier config/server.php comme suit:
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Consultez [le pilote d'événement workerman](https://www.workerman.net/doc/workerman/appendices/event.html) pour plus d'informations.

### Pollution de variables globales

Il est interdit de stocker des informations d'état **liées à la requête** dans des variables globales ou statiques en environnement de coroutine, car cela pourrait entraîner une pollution des variables globales, par exemple:

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

En définissant le nombre de processus à 1, lorsque nous envoyons deux demandes consécutives:
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
Nous nous attendons à ce que les résultats renvoyés par les deux requêtes soient respectivement `lilei` et `hanmeimei`, mais en réalité, les résultats renvoyés sont tous les deux `hanmeimei`.
Cela est dû au fait que la deuxième requête écrase la variable statique `$name`, et lorsque la première demande se termine de dormir, la variable statique `$name` est devenue `hanmeimei`.

**La méthode correcte consiste à utiliser le contexte pour stocker les données d'état de la requête**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Les variables locales ne causent pas de pollution des données**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Puisque `$name` est une variable locale, les coroutines ne peuvent pas accéder aux variables locales les unes des autres, donc l'utilisation de variables locales est sûre en environnement de coroutine.

# À propos des coroutines
Les coroutines ne sont pas une solution miracle, leur introduction signifie qu'il est nécessaire de faire attention à la pollution des variables globales/statiques et de définir le contexte. De plus, le débogage des bogues dans un environnement de coroutine est plus compliqué que dans la programmation bloquante.

Dans la pratique, la programmation bloquante avec webman est déjà assez rapide. En se basant sur les données de test de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) au cours des trois dernières années, webman en mode bloqué avec des opérations de base de données est près de deux fois plus performant que les frameworks web Go tels que gin, echo, et près de 40 fois plus performant que le framework Laravel traditionnel.
![](../../assets/img/benchemarks-go-sw.png?)


Lorsque la base de données et Redis se trouvent sur le réseau local, la performance de la programmation bloquante avec plusieurs processus est souvent supérieure à celle de la coroutine, car lorsque la base de données, Redis, etc., sont suffisamment rapides, le coût de la création, de la planification et de la destruction des coroutines peut être plus élevé que celui du changement de processus, donc l'introduction de coroutines ne permet pas d'améliorer significativement les performances.

# Quand utiliser les coroutines
Lorsqu'une application nécessite des accès lents, par exemple lorsqu'une application doit accéder à une API tierce, il est possible d'utiliser [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) pour effectuer des appels HTTP asynchrones de manière synchrone, améliorant ainsi la capacité de concurrence de l'application.
