# Crontab-Zeitplanungskomponente

## Workerman/Crontab

### Erklärung

`Workerman/Crontab` ist ähnlich wie der Linux-Crontab, mit dem Unterschied, dass `Workerman/Crontab` die sekundengenaue Zeitplanung unterstützt.

Zeiterklärung:

```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ Wochentag (0 - 6) (Sonntag=0)
|   |   |   |   +------ Monat (1 - 12)
|   |   |   +-------- Tag im Monat (1 - 31)
|   |   +---------- Stunde (0 - 23)
|   +------------ Minute (0 - 59)
+-------------- Sekunde (0-59) [kann weggelassen werden, wenn 0 nicht vorhanden ist, ist minimaler Zeitabstand Minute]
```

### Projektadresse

https://github.com/walkor/crontab

### Installation

```php
composer require workerman/crontab
```

### Verwendung

**Schritt 1: Neues Prozessdokument "process/Task.php" erstellen**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {

        // Alle Sekunde ausführen
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
        
        // Jede Minute am Anfang ausführen
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Täglich um 7:50 Uhr ausführen, achten Sie darauf, dass hier die Sekunde weggelassen wurde
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Schritt 2: Konfigurieren Sie das Prozessdokument, das mit dem Webman gestartet wird**

Öffnen Sie die Konfigurationsdatei `config/process.php` und fügen Sie die folgende Konfiguration hinzu:

```php
return [
    ....andere Konfigurationen, hier ausgelassen....

    'task'  => [
        'handler'  => process\Task::class
    ],
];
```


**Schritt 3: Webman neu starten**

> Hinweis: Geplante Aufgaben werden nicht sofort ausgeführt. Alle geplanten Aufgaben beginnen erst in der nächsten Minute mit der Ausführung.

### Erklärung
Crontab ist nicht asynchron. Zum Beispiel, wenn ein Task-Prozess A und B zwei Zeitgeber setzt, die beide jede Sekunde eine Aufgabe ausführen, und A-Aufgabe 10 Sekunden dauert, muss B auf die Fertigstellung von A warten, bevor sie ausgeführt werden kann, was zu Verzögerungen bei der Ausführung von B führt.
Wenn das Geschäft empfindlich auf Zeitintervalle reagiert, sollten die sensitiven geplanten Aufgaben in einem separaten Prozess ausgeführt werden, um sicherzustellen, dass sie nicht von anderen geplanten Aufgaben beeinflusst werden. Zum Beispiel, konfigurieren Sie in `config/process.php` wie folgt:

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
Legen Sie die zeitkritischen geplanten Aufgaben in `process/Task1.php` fest und die anderen geplanten Aufgaben in `process/Task2.php`.

### Mehr
Für weitere Informationen zur Konfiguration von `config/process.php` siehe [Benutzerdefinierte Prozesse](../process.md).
