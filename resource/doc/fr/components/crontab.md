# Composant de tâches planifiées Crontab

## workerman/crontab

### Description

`workerman/crontab` est similaire à crontab sur Linux, mais la différence est que `workerman/crontab` supporte la planification jusqu'à la seconde.

Description du temps :

```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ jour de la semaine (0 - 6) (dimanche=0)
|   |   |   |   +------ mois (1 - 12)
|   |   |   +-------- jour du mois (1 - 31)
|   |   +---------- heure (0 - 23)
|   +------------ minute (0 - 59)
+-------------- seconde (0-59)[peut être omis, si la première position est absente, la plus petite unité de temps est la minute]
```

### Adresse du projet

https://github.com/walkor/crontab

### Installation

```php
composer require workerman/crontab
```
  
### Utilisation

**Étape 1 : Créer un fichier de processus `process/Task.php`**

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
        
        // Exécuter à la première seconde de chaque minute
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Exécuter à 7h50 tous les jours, veuillez noter que la position de la seconde a été omise ici
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```
  
**Étape 2 : Configurer le fichier de processus pour démarrer avec webman**

Ouvrez le fichier de configuration `config/process.php` et ajoutez la configuration suivante :

```php
return [
    ....autres configurations, ici omises....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**Étape 3 : Redémarrer webman**

> Remarque : les tâches planifiées ne s'exécuteront pas immédiatement, toutes les tâches planifiées commenceront à être exécutées lorsque la prochaine minute commencera.

### Remarque
Crontab n'est pas asynchrone. Par exemple, un processus de tâche définit deux minuteries A et B, toutes deux exécutent une tâche toutes les secondes, mais si la tâche A prend 10 secondes, alors la tâche B doit attendre que la tâche A soit terminée avant de s'exécuter, ce qui entraîne un retard dans l'exécution de la tâche B.
Si la sensibilité à l'intervalle de temps est importante pour l'activité, il est nécessaire d'exécuter les tâches planifiées très sensibles dans des processus séparés pour éviter l'impact d'autres tâches planifiées. Par exemple, dans `config/process.php`, faites la configuration suivante :

```php
return [
    ....autres configurations, ici omises....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Placez les tâches planifiées sensibles au temps dans le fichier `process/Task1.php`, et les autres tâches planifiées dans le fichier `process/Task2.php`.

### Plus d'informations
Pour plus d'informations sur la configuration de `config/process.php`, veuillez consulter [Processus personnalisé](../process.md)
