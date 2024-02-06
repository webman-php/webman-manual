# Cycle de vie

## Cycle de vie du processus
- Chaque processus a une longue durée de vie.
- Chaque processus fonctionne indépendamment et sans interférence mutuelle.
- Chaque processus peut traiter plusieurs requêtes au cours de sa durée de vie.
- Lorsqu'un processus reçoit une commande `stop`, `reload` ou `restart`, il s'arrête et met fin à son cycle de vie actuel.

> **Note**
> Chaque processus est indépendant et sans interférence mutuelle, ce qui signifie que chaque processus maintient ses propres ressources, variables, instances de classes, etc. En d'autres termes, chaque processus a sa propre connexion à la base de données, et certaines instances uniques sont initialisées pour chaque processus, ce qui signifie qu'elles seront initialisées plusieurs fois pour plusieurs processus.

## Cycle de vie de la requête
- Chaque requête génère un objet `$request`.
- L'objet `$request` est récupéré après le traitement de la requête.

## Cycle de vie du contrôleur
- Chaque contrôleur est instancié une seule fois par processus, et plusieurs fois par plusieurs processus (à l'exception de la désactivation du contrôleur réutilisé, voir [Cycle de vie du contrôleur](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- Les instances de contrôleur sont partagées entre plusieurs requêtes dans le processus actuel (à l'exception de la désactivation du contrôleur réutilisé).
- Le cycle de vie du contrôleur se termine lorsque le processus se termine (à l'exception de la désactivation du contrôleur réutilisé).

## Concernant le cycle de vie des variables
Webman est basé sur PHP, donc il respecte entièrement le mécanisme de récupération des variables de PHP. Les variables temporaires générées dans la logique métier, notamment les instances de classes créées avec le mot-clé `new`, sont automatiquement récupérées à la fin de la fonction ou de la méthode, et n'ont pas besoin d'être libérées manuellement avec `unset`. En d'autres termes, le développement avec webman offre une expérience similaire au développement avec des frameworks traditionnels. Par exemple, l'instance `$foo` dans l'exemple ci-dessous sera automatiquement libérée à la fin de l'exécution de la méthode index :
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
Si vous souhaitez réutiliser une instance de classe, vous pouvez la stocker dans une propriété statique de la classe ou dans une propriété d'objet à longue durée de vie (comme un contrôleur), ou utiliser la méthode `get` du conteneur pour initialiser l'instance de la classe, par exemple :
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
La méthode `Container::get()` est utilisée pour créer et stocker une instance de classe, et lorsque vous appelez à nouveau la méthode avec les mêmes paramètres, elle renverra l'instance précédemment créée.

> **Remarque**
> `Container::get()` ne peut initialiser des instances sans paramètres de constructeur. `Container::make()` peut créer des instances avec des paramètres de constructeur, mais contrairement à `Container::get()`, `Container::make()` ne réutilisera pas l'instance, c'est-à-dire que même avec les mêmes paramètres, `Container::make()` renverra toujours une nouvelle instance.

# Concernant les fuites de mémoire
Dans la plupart des cas, notre code métier n'entraînera pas de fuites de mémoire (très peu d'utilisateurs ont signalé des fuites de mémoire). Il suffit de faire attention à ne pas faire croître indéfiniment les données d'array à longue durée de vie. Considérez le code ci-dessous :
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Propriété de tableau
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
Par défaut, le contrôleur a une longue durée de vie (à l'exception de la désactivation de la réutilisation du contrôleur), de même, la propriété `$data` du contrôleur a également une longue durée de vie. Avec l'augmentation continue des éléments du tableau `$data` via la requête `foo/index`, cela entraînera une fuite de mémoire.

Pour plus d'informations, veuillez consulter [Fuites de mémoire](./memory-leak.md).
