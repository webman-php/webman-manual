# Composant de tâches planifiées crontab

## workerman/crontab

### Description

`workerman/crontab` est similaire à crontab sous Linux, mais la différence est que `workerman/crontab` prend en charge la planification au niveau des secondes.

Explication des délais :

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ jour de la semaine (0 - 6) (dimanche=0)
|   |   |   |   +------ mois (1 - 12)
|   |   |   +-------- jour du mois (1 - 31)
|   |   +---------- heure (0 - 23)
|   +------------ min (0 - 59)
+-------------- sec (0-59)[Optionnel, s'il n'y a pas de 0, la plus petite unité de temps est la minute]
```

### Adresse du projet

https://github.com/walkor/crontab

### Installation

```php
composer require workerman/crontab
```

### Utilisation

**Étape 1 : Créez un fichier de processus `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Exécuter toutes les secondes
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Exécuter toutes les 5 secondes
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Exécuter toutes les minutes
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Exécuter toutes les 5 minutes
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Exécuter la première seconde de chaque minute
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Exécuter à 7h50 tous les jours, notez qu'ici la unité de seconde est omise
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Étape 2 : Configurez le fichier de processus avec le démarrage de webman**

Ouvrez le fichier de configuration `config/process.php` et ajoutez la configuration suivante

```php
return [
    ....autres configurations, omises ici....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Étape 3 : Redémarrez webman**

> Remarque : les tâches planifiées ne s'exécutent pas immédiatement, elles commencent à être exécutées à la prochaine minute.

### Remarque
crontab n'est pas asynchrone. Par exemple, un processus tâche configure deux minuteries A et B qui s'exécutent toutes les secondes. Si la tâche A prend 10 secondes, la tâche B devra attendre que A soit terminée avant de s'exécuter, ce qui entraîne un retard dans l'exécution de B.
Si la sensibilité au temps est critique pour l'activité, il est nécessaire d'exécuter les tâches planifiées sensibles au temps dans un processus séparé pour éviter toute influence des autres tâches planifiées. Par exemple, dans `config/process.php`, effectuez la configuration suivante

```php
return [
    ....autres configurations, omises ici....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```

Placez les tâches planifiées sensibles au temps dans `process/Task1.php` et les autres tâches planifiées dans `process/Task2.php`.

### En savoir plus
Pour plus d'informations sur la configuration de `config/process.php`, veuillez consulter [Processus personnalisés](../process.md)
