# Les Coroutines

> **Exigences des Coroutines**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Pour mettre à niveau webman, utilisez la commande `composer require workerman/webman-framework ^1.5.0`
> Pour mettre à niveau workerman, utilisez la commande `composer require workerman/workerman ^5.0.0`
> Les Coroutines Fiber nécessitent l'installation de `composer require revolt/event-loop ^1.0.0`

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
        // Attendre 1.5 secondes
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` est similaire à la fonction `sleep()` native de PHP, sauf que `Timer::sleep()` ne bloquera pas le processus.


### Faire une requête HTTP

> **Remarque**
> Vous devez installer `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // Effectuer une requête asynchrone de manière synchrone
        return $response->getBody()->getContents();
    }
}
```
De même, la requête `$client->get('http://example.com')` est non bloquante, ce qui peut être utilisé pour lancer une requête HTTP de manière non bloquante dans webman, améliorant ainsi les performances de l'application.

Pour plus d'informations, consultez [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Ajout de la classe support\Context

La classe `support\Context` est utilisée pour stocker des données de contexte de requête. Lorsque la requête est terminée, les données de contexte correspondantes sont automatiquement supprimées. Autrement dit, la durée de vie des données de contexte est liée à la durée de vie de la requête. `support\Context` prend en charge les environnements de coroutine Fiber, Swoole, et Swow.


### Coroutine Swoole
Après avoir installé l'extension Swoole (requise pour Swoole>=5.0), vous pouvez activer les coroutines Swoole en configurant `config/server.php`
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Pour en savoir plus, consultez [workerman/event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Pollution des variables globales

L'environnement de coroutine interdit le stockage des informations d'état **liées à la requête** dans des variables globales ou statiques, car cela pourrait entraîner une pollution des variables globales, par exemple

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

En définissant le nombre de processus sur 1, lorsque nous envoyons deux requêtes consécutives:
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
Nous nous attendons à ce que les résultats de ces deux requêtes soient respectivement `lilei` et `hanmeimei`, mais en réalité, les deux requêtes renvoient toutes les deux `hanmeimei`.
Cela est dû au fait que la deuxième requête a écrasé la variable statique `$name`, et lorsque la première requête termine son délai, la variable statique `$name` est déjà devenue `hanmeimei`.

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

**Les variables locales ne provoquent pas de pollution des données**
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
Étant donné que `$name` est une variable locale, les coroutines ne peuvent pas accéder aux variables locales les unes des autres, il est donc sécuritaire d'utiliser des variables locales dans les coroutines.


# À propos des Coroutines
Les coroutines ne sont pas une solution miracle, leur introduction signifie qu'il est nécessaire de faire attention à la pollution des variables globales et statiques, et de définir le contexte. De plus, déboguer des bugs dans un environnement de coroutine est un peu plus complexe que la programmation bloquante.

De toute façon, la programmation bloquante avec webman est déjà assez rapide. Selon les données de test de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) durant les trois dernières années, la programmation bloquante de webman avec des activités de base de données est presque deux fois plus performante que le framework web go, gin, echo, etc., et elle est presque 40 fois plus performante que le framework traditionnel Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

Lorsque la base de données, Redis, etc. sont tous situés sur un réseau interne, la performance de la programmation bloquante avec plusieurs processus est souvent supérieure à celle des coroutines. Cela est dû au fait que lorsque la base de données, Redis, etc. sont suffisamment rapides, le coût de création, de planification et de destruction des coroutines peut être plus élevé que le coût de basculement de processus, donc l'introduction de coroutines ne peut pas améliorer considérablement les performances.

# Quand utiliser les Coroutines
Lorsque les activités nécessitent des appels lents, comme l'accès à une interface tierce, vous pouvez utiliser [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) pour effectuer des appels HTTP asynchrones de manière synchrone, afin d'améliorer les capacités de concurrence de l'application.
