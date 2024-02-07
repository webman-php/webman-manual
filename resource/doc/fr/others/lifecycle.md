# Cycle de vie

## Cycle de vie des processus
- Chaque processus a une longue durée de vie.
- Chaque processus fonctionne de manière indépendante et ne se perturbe pas mutuellement.
- Chaque processus peut traiter plusieurs requêtes au cours de sa durée de vie.
- Lorsqu'un processus reçoit une commande `stop`, `reload` ou `restart`, il se termine et met fin à son cycle de vie actuel.

> **Conseil**
> Chaque processus fonctionne de manière indépendante, ce qui signifie que chaque processus maintient ses propres ressources, variables et instances de classe, ce qui se traduit par une connexion de base de données distincte pour chaque processus. Certains singletons sont initialisés une fois par processus, ce qui signifie qu'ils seront initialisés plusieurs fois pour plusieurs processus.

## Cycle de vie de la requête
- Chaque requête génère un objet `$request`.
- L'objet `$request` est récupéré après le traitement de la requête.

## Cycle de vie du contrôleur
- Chaque contrôleur est instancié une seule fois par processus, mais plusieurs fois par plusieurs processus (à l'exception de la réutilisation du contrôleur, voir [Cycle de vie du contrôleur](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- L'instance du contrôleur est partagée entre plusieurs requêtes dans le processus actuel (à l'exception de la réutilisation du contrôleur).
- Le cycle de vie du contrôleur se termine lorsque le processus se termine (à l'exception de la réutilisation du contrôleur).

## À propos du cycle de vie des variables
Webman est basé sur PHP, il suit donc complètement le mécanisme de récupération des variables de PHP. Les variables temporaires créées dans la logique métier, y compris les instances de classe créées avec le mot-clé `new`, sont récupérées automatiquement à la fin d'une fonction ou d'une méthode, sans nécessiter de `unset` manuel. En d'autres termes, le développement avec Webman offre une expérience similaire à celle des frameworks traditionnels. Par exemple, dans l'exemple ci-dessous, l'instance `$foo` sera automatiquement libérée à la fin de l'exécution de la méthode `index` :
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Supposons qu'il y ait une classe Foo ici
        return response($foo->sayHello());
    }
}
```
Si vous souhaitez réutiliser une instance de classe, vous pouvez la stocker dans une propriété statique de classe ou dans une propriété d'objet à longue durée de vie (comme un contrôleur), ou utiliser la méthode `get` du conteneur pour initialiser l'instance de classe, par exemple :
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```
La méthode `Container::get()` est utilisée pour créer et stocker une instance de classe, de sorte que lorsqu'elle est appelée à nouveau avec les mêmes paramètres, elle renverra l'instance précédemment créée.

> **Remarque**
> `Container::get()` ne peut pas initialiser d'instances avec des paramètres de constructeur. `Container::make()` peut créer des instances avec des paramètres de constructeur, mais contrairement à `Container::get()`, `Container::make()` ne réutilisera pas l'instance, c'est-à-dire qu'à chaque appel avec les mêmes paramètres, `Container::make()` renverra toujours une nouvelle instance.

# À propos des fuites de mémoire
Dans la plupart des cas, notre code métier ne provoque pas de fuites de mémoire (très peu de retours d'utilisateurs signalent des fuites de mémoire), il suffit de faire attention à ne pas agrandir indéfiniment les tableaux de données de longue durée. Veuillez consulter le code ci-dessous :
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Propriété du tableau
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
Par défaut, le contrôleur a une longue durée de vie (à l'exception de la réutilisation du contrôleur), de même la propriété `$data` du contrôleur a également une longue durée de vie. Avec l'ajout continu d'éléments au tableau `$data` à travers la requête `foo/index`, les éléments du tableau `$data` augmentent progressivement, entraînant une fuite de mémoire.

Pour plus d'informations, veuillez consulter [Fuites de mémoire](./memory-leak.md)
