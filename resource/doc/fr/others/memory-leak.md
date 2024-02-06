# À propos des fuites de mémoire
Webman est un framework à mémoire résidente, il est donc nécessaire de surveiller de près les fuites de mémoire. Cependant, les développeurs n'ont pas à s'inquiéter outre mesure, car les fuites de mémoire se produisent dans des conditions extrêmes et sont facilement évitables. Le développement avec webman est similaire à celui des frameworks traditionnels, sans nécessiter d'opérations supplémentaires pour la gestion de la mémoire.

> **Note**
> Le processus de surveillance intégré à webman surveillera l'utilisation de la mémoire par tous les processus. Si la mémoire utilisée par un processus est sur le point d'atteindre la valeur définie dans `memory_limit` du fichier php.ini, le processus sera redémarré en toute sécurité pour libérer de la mémoire, sans impact sur l'activité.

## Définition des fuites de mémoire
Avec l'augmentation continue des requêtes, la mémoire utilisée par webman augmente également **indéfiniment** (attention, c'est **indéfiniment**), atteignant parfois plusieurs centaines de Mo voire plus, ce qui constitue une fuite de mémoire. Si la mémoire augmente mais cesse d'augmenter par la suite, ce n'est pas une fuite de mémoire.

Il est tout à fait normal qu'un processus utilise quelques dizaines de Mo de mémoire. Lorsqu'un processus gère des requêtes extrêmement volumineuses ou maintient de nombreuses connexions, il est courant que la consommation de mémoire d'un processus unique atteigne parfois plus d'une centaine de Mo. Une partie de cette mémoire peut ne pas être entièrement restituée au système d'exploitation après son utilisation par PHP. Elle est conservée pour être réutilisée, ce qui peut entraîner une augmentation de la mémoire après le traitement d'une requête importante sans libération de mémoire. Cela est un phénomène normal. (L'utilisation de la méthode gc_mem_caches() peut libérer une partie de la mémoire libre)

## Comment les fuites de mémoire se produisent
**Les fuites de mémoire se produisent lorsque les deux conditions suivantes sont remplies :**
1. Il existe un tableau **à longue durée de vie** (notez qu'il s'agit d'un tableau **à longue durée de vie**, les tableaux normaux ne posent pas de problème)
2. Et que ce tableau **à longue durée de vie** se développe indéfiniment (les données commerciales sont insérées en quantité illimitée et ne sont jamais nettoyées)

Si les conditions 1 et 2 sont remplies en même temps (attention, c'est en même temps), une fuite de mémoire se produira. Dans le cas contraire ou si seulement l'une des conditions est remplie, il ne s'agira pas d'une fuite de mémoire.

## Tableaux à longue durée de vie
Dans webman, les tableaux à longue durée de vie incluent :
1. Les tableaux déclarés avec le mot-clé `static`
2. Les tableaux attributs des singletons
3. Les tableaux déclarés avec le mot-clé `global`

> **Remarque**
> Il est permis d'utiliser des données à longue durée de vie dans webman, mais il est nécessaire de garantir que les données contenues dans ces tableaux sont limitées, c'est-à-dire que le nombre d'éléments ne va pas croître indéfiniment.

Voici des exemples explicatifs.

#### Tableau `static` qui croît indéfiniment
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

Le tableau `$data` défini avec le mot-clé `static` est un tableau à longue durée de vie, et dans l'exemple, ce tableau croît indéfiniment avec les requêtes, ce qui entraîne une fuite de mémoire.

#### Tableau attribut de singleton qui croît indéfiniment
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

Code d'appel
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

`Cache::instance()` retourne un singleton Cache. Il s'agit bien d'une instance à longue durée de vie, même si son attribut `$data` n'utilise pas le mot-clé `static`. Ainsi, en continuant à ajouter des données avec différentes clés dans le tableau `$data`, la consommation de mémoire du programme augmente progressivement, ce qui provoque une fuite de mémoire.

> **Remarque**
> Si les clés ajoutées avec `Cache::instance()->set(key, value)` sont en nombre limité, il n'y aura pas de fuite de mémoire, car le tableau `$data` ne croît pas indéfiniment.

#### Tableau `global` qui croît indéfiniment
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
Un tableau défini avec le mot-clé `global` ne sera pas libéré une fois que la fonction ou la méthode est exécutée, il s'agit donc d'un tableau à longue durée de vie. Dans le code ci-dessus, une fuite de mémoire se produira à mesure que les requêtes continuent d'augmenter. De même, un tableau défini avec le mot-clé `static` à l'intérieur d'une fonction ou d'une méthode sera également un tableau à longue durée de vie, et en cas de croissance indéfinie du tableau, il pourrait causer une fuite de mémoire, par exemple :
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
Il est conseillé aux développeurs de ne pas se préoccuper particulièrement des fuites de mémoire, car elles sont très rares. Si, malheureusement, une fuite de mémoire se produit, nous pouvons la localiser en faisant des tests de charge. Même si les développeurs ne parviennent pas à trouver le point de fuite, le service de surveillance intégré à webman redémarrera de manière sécurisée les processus générant une fuite de mémoire en temps opportun, libérant ainsi de la mémoire.

Si vous souhaitez tout de même éviter autant que possible les fuites de mémoire, vous pouvez suivre les recommandations suivantes.
1. Évitez autant que possible l'utilisation des tableaux avec les mots-clés `global` et `static`, et si vous les utilisez, assurez-vous qu'ils ne croissent pas indéfiniment.
2. Pour les classes avec lesquelles vous n'êtes pas familier, évitez autant que possible l'utilisation de singletons, préférez l'initialisation avec le mot-clé `new`. Si vous avez besoin d'un singleton, vérifiez s'il possède des tableaux à croissance indéfinie dans ses attributs.
