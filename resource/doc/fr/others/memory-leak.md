# À propos des fuites de mémoire
Webman est un framework résident en mémoire, donc nous devons être un peu attentifs aux fuites de mémoire. Cependant, les développeurs n'ont pas besoin de s'inquiéter outre mesure, car les fuites de mémoire se produisent dans des conditions extrêmes et sont facilement évitables. Le développement avec webman offre une expérience similaire à celle des frameworks traditionnels, sans nécessiter d'opérations de gestion de la mémoire superflues.

> **Remarque**
> Le processus de surveillance intégré de webman vérifie l'utilisation de la mémoire de tous les processus. Si la mémoire utilisée par un processus est sur le point d'atteindre la valeur définie dans `memory_limit` de php.ini, le processus correspondant redémarrera automatiquement en toute sécurité, libérant ainsi la mémoire, sans impact sur les opérations commerciales.

## Définition des fuites de mémoire
Avec l'augmentation continue des requêtes, la mémoire utilisée par webman augmente également **indéfiniment** (notez bien, **indéfiniment**), atteignant plusieurs centaines de mégaoctets, voire plus, ce qui constitue une fuite de mémoire.
Si la mémoire augmente mais ne continue pas à augmenter par la suite, elle n'est pas considérée comme une fuite de mémoire.

Il est normal qu'un processus utilise plusieurs dizaines de mégaoctets de mémoire. Lorsqu'un processus gère des requêtes très volumineuses ou maintient un grand nombre de connexions, il est courant que la mémoire utilisée par un seul processus atteigne plusieurs centaines de mégaoctets. Cette utilisation de mémoire n'est peut-être pas entièrement retournée au système d'exploitation par PHP, mais est conservée pour être réutilisée. Par conséquent, il peut arriver que la mémoire utilisée augmente après le traitement d'une grosse requête sans être libérée, ce qui est un phénomène normal. (Appeler la méthode gc_mem_caches() peut libérer une partie de la mémoire inutilisée)

## Comment se produit une fuite de mémoire
**Pour qu'une fuite de mémoire se produise, les deux conditions suivantes doivent être remplies :**
1. Il existe un tableau à **longue durée de vie** (notez qu'il s'agit d'un tableau à **longue durée de vie**, les tableaux normaux ne posent pas de problème)
2. Et ce tableau à **longue durée de vie** se développe indéfiniment (le système insère sans fin des données dans ce tableau sans jamais les nettoyer)

Si les conditions 1 et 2 sont **simultanément remplies** (notez bien qu'il s'agit d'un **simultanément**), une fuite de mémoire se produira. Dans le cas contraire, si ces conditions ne sont pas remplies ou si l'une seulement est remplie, il ne s'agit pas d'une fuite de mémoire.

## Tableaux à longue durée de vie
Dans webman, les tableaux à longue durée de vie incluent :
1. Les tableaux avec le mot-clé `static`
2. Les propriétés de tableau des singletons
3. Les tableaux avec le mot-clé `global`

> **Remarque**
> Webman autorise l'utilisation de données à longue durée de vie, mais il est nécessaire de s'assurer que les données à l'intérieur de ces données sont limitées, c'est-à-dire que le nombre d'éléments ne va pas augmenter indéfiniment.

Voici des exemples explicatifs :

#### Tableau `static` à expansion infinie
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

Le tableau `$data` défini avec le mot-clé `static` est un tableau à longue durée de vie, et dans l'exemple, le tableau `$data` continue de se développer avec chaque requête, ce qui provoque une fuite de mémoire.

#### Propriété de tableau de singleton à expansion infinie
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

Code d'appel :
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

L'appel `Cache::instance()` renvoie un singleton de Cache, qui est une instance de classe à longue durée de vie. Bien que sa propriété `$data` n'utilise pas le mot-clé `static`, en raison de la longue durée de vie de la classe elle-même, `$data` est également un tableau à longue durée de vie. À mesure que de nouvelles clés de données sont ajoutées au tableau `$data`, la mémoire utilisée par le programme augmente, ce qui provoque une fuite de mémoire.

> **Remarque**
> Si les clés ajoutées avec `Cache::instance()->set(key, value)` sont d'une quantité limitée, il n'y aura pas de fuite de mémoire, car le tableau `$data` n'augmente pas indéfiniment.

#### Tableau `global` à expansion infinie
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
Les tableaux définis avec le mot-clé global ne sont pas libérés une fois la fonction ou la méthode terminée, ce sont donc des tableaux à longue durée de vie. Le code ci-dessus, avec l'augmentation continue des requêtes, entraînera une fuite de mémoire. De même, les tableaux définis à l'intérieur d'une fonction ou d'une méthode avec le mot-clé `static` seront également des tableaux à longue durée de vie, et s'ils se développent indéfiniment, cela pourrait entraîner une fuite de mémoire, par exemple :
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## Recommandations
Il est recommandé aux développeurs de ne pas se concentrer particulièrement sur les fuites de mémoire, car elles sont très rares. En cas de malheur, nous pouvons trouver quel segment de code provoque la fuite en effectuant des tests de charge, ce qui nous permet de localiser le problème. Même si les développeurs ne trouvent pas le point de fuite, le service de surveillance intégré à webman redémarrera de manière sécurisée les processus présentant une fuite de mémoire, libérant ainsi la mémoire.

Si vous souhaitez éviter autant que possible les fuites de mémoire, voici quelques conseils :
1. Évitez autant que possible d'utiliser des tableaux avec les mots-clés `global` et `static`. Si vous les utilisez, assurez-vous qu'ils ne se développent pas indéfiniment.
2. Pour les classes que vous ne connaissez pas bien, évitez autant que possible d'utiliser des singletons et préférez l'initialisation avec le mot-clé `new`. Si vous avez besoin d'un singleton, assurez-vous qu'il n'y a pas de propriété de tableau à expansion infinie.
