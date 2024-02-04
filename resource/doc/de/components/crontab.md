# Crontab-Zeitplan Komponente

## Arbeitnehmer/Crontab

### Erklärung

`WorkerMan/Crontab` ist ähnlich wie der Crontab von Linux, aber `WorkerMan/Crontab` unterstützt auch sekundengenaue Zeitplanung.

Zeiterklärung:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ Wochentag (0 - 6) (Sonntag=0)
|   |   |   |   +------ Monat (1 - 12)
|   |   |   +-------- Tag des Monats (1 - 31)
|   |   +---------- Stunde (0 - 23)
|   +------------ min (0 - 59)
+-------------- sec (0-59)[Optional, wenn die 0-Stelle fehlt, ist die kleinste Zeitraumgröße eine Minute]
```

### Projektadresse

https://github.com/walkor/crontab
  
### Installation
 
```php
composer require workerman/crontab
```
  
### Verwendung

**Schritt 1: Erstellen Sie die Prozessdatei `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Jede Sekunde ausführen
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Alle 5 Sekunden ausführen
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Jede Minute ausführen
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Alle 5 Minuten ausführen
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Jede Minute zu Beginn ausführen
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Jeden Tag um 7:50 Uhr ausführen, beachten Sie, dass hier die Sekunde weggelassen wurde
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```
  
**Schritt 2: Konfigurieren Sie die Prozessdatei, um mit Webman zu starten**
  
Öffnen Sie die Konfigurationsdatei `config/process.php` und fügen Sie die folgende Konfiguration hinzu

```php
return [
    ....andere Konfigurationen, hier ausgelassen....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**Schritt 3: Starten Sie Webman neu**

> Hinweis: Zeitgesteuerte Aufgaben werden nicht sofort ausgeführt. Alle Zeitgesteuerten Aufgaben beginnen erst in der nächsten Minute.

### Erklärung
Crontab ist nicht asynchron. Wenn zum Beispiel in einem Aufgabenprozess zwei Timer A und B eingestellt sind, die beide alle Sekunde eine Aufgabe ausführen, und A 10 Sekunden dauert, dann muss B auf die Ausführung von A warten, was zu einer Verzögerung bei der Ausführung von B führt.
Wenn das Geschäft empfindlich auf Zeitintervalle reagiert, sollten sensible Zeitgesteuerte Aufgaben in einen separaten Prozess verschoben werden, um zu verhindern, dass sie von anderen Zeitgesteuerten Aufgaben beeinträchtigt werden. Zum Beispiel, in der Datei `config/process.php` fügen Sie die folgende Konfiguration hinzu:

```php
return [
    ....andere Konfigurationen, hier ausgelassen....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Platzieren Sie die zeitkritischen Zeitgesteuerten Aufgaben in `process/Task1.php` und die restlichen Zeitgesteuerten Aufgaben in `process/Task2.php`.

### Mehr
Für weitere Informationen zur Konfiguration von `config/process.php` siehe [Benutzerdefinierte Prozesse](../process.md)
