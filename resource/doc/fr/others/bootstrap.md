# Initialisation des activités

Parfois, nous avons besoin de faire certaines initialisations des activités après le démarrage du processus. Cette initialisation ne s'exécute qu'une fois pendant le cycle de vie du processus, par exemple, configurer une minuterie après le démarrage du processus ou initialiser la connexion à la base de données, etc. Nous allons expliquer cela ci-dessous.

## Principe
Selon les explications de **[flux d'exécution](process.md)**, après le démarrage du processus, webman chargera les classes définies dans `config/bootstrap.php` (y compris `config/plugin/*/*/bootstrap.php`) et exécutera la méthode start de ces classes. Nous pouvons ajouter du code d'activité dans la méthode start pour effectuer l'initialisation des activités après le démarrage du processus.

## Processus
Supposons que nous voulons créer une minuterie pour rapporter l'utilisation actuelle de la mémoire par le processus à intervalles réguliers, et nommons cette classe `MemReport`.

#### Exécuter la commande
Exécutez la commande `php webman make:bootstrap MemReport` pour générer le fichier d'initialisation `app/bootstrap/MemReport.php`.

> **Remarque**
> Si votre webman n'a pas installé `webman/console`, exécutez la commande `composer require webman/console` pour l'installer.

#### Éditer le fichier d'initialisation
Modifiez `app/bootstrap/MemReport.php` avec un contenu similaire à ce qui suit :
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Est-ce un environnement en ligne de commande ?
        $is_console = !$worker;
        if ($is_console) {
            // Si vous ne souhaitez pas exécuter cette initialisation dans un environnement en ligne de commande, retournez directement ici.
            return;
        }
        
        // Exécute toutes les 10 secondes
        \Workerman\Timer::add(10, function () {
            // Pour faciliter la démonstration, nous utilisons ici une sortie à la place du processus de rapport.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Remarque**
> Lors de l'utilisation de la ligne de commande, le cadre exécute également la méthode start configurée dans `config/bootstrap.php`. Nous pouvons déterminer si l'environnement est en ligne de commande en vérifiant si `$worker` est null, afin de décider si le code d'initialisation des activités doit être exécuté.

#### Configuration au démarrage du processus
Ouvrez `config/bootstrap.php` et ajoutez la classe `MemReport` aux éléments de démarrage.
```php
return [
    // ... les autres configurations sont omises ici ...
    
    app\bootstrap\MemReport::class,
];
```

Ainsi, nous avons achevé un processus d'initialisation des activités.

## Informations complémentaires
Après le démarrage du processus [customisé](../process.md), la méthode start configurée dans `config/bootstrap.php` sera également exécutée. Nous pouvons utiliser `$worker->name` pour vérifier quel processus est en cours et décider si votre code d'initialisation des activités doit être exécuté dans ce processus. Par exemple, si nous n'avons pas besoin de surveiller le processus monitor, alors le contenu de `MemReport.php` ressemblera à ceci :
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Est-ce un environnement en ligne de commande ?
        $is_console = !$worker;
        if ($is_console) {
            // Si vous ne souhaitez pas exécuter cette initialisation dans un environnement en ligne de commande, retournez directement ici.
            return;
        }
        
        // Le processus de surveillance ne déclenche pas la minuterie
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Exécute toutes les 10 secondes
        \Workerman\Timer::add(10, function () {
            // Pour faciliter la démonstration, nous utilisons ici une sortie à la place du processus de rapport.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
