# Geschäftsinitialisierung

Manchmal müssen wir nach dem Start des Prozesses einige Geschäftsinitialisierungen durchführen, die nur einmal im Lebenszyklus des Prozesses ausgeführt werden, z. B. das Setzen eines Timers nach dem Start des Prozesses oder die Initialisierung der Datenbankverbindung. Im Folgenden wird dies erläutert.

## Prinzip
Gemäß den Erläuterungen im **[Ausführungsprozess](process.md)** lädt webman nach dem Start des Prozesses die in `config/bootstrap.php` (einschließlich `config/plugin/*/*/bootstrap.php`) festgelegten Klassen und führt die start-Methode der Klasse aus. In der start-Methode können wir Geschäftslogik hinzufügen, um die Initialisierung des Geschäfts nach dem Start des Prozesses abzuschließen.

## Prozess
Angenommen, wir möchten einen Timer erstellen, der den aktuellen Speicherverbrauch des Prozesses regelmäßig meldet. Diese Klasse wird als `MemReport` bezeichnet.

#### Befehl ausführen
Führen Sie den Befehl `php webman make:bootstrap MemReport` aus, um die Initialisierungsdatei `app/bootstrap/MemReport.php` zu generieren.

> **Hinweis**
> Wenn Ihr webman `webman/console` nicht installiert hat, führen Sie den Befehl `composer require webman/console` aus, um es zu installieren.

#### Initialisierungsdatei bearbeiten
Bearbeiten Sie `app/bootstrap/MemReport.php` mit einem Inhalt ähnlich wie folgt:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Ist es eine Befehlszeile?
        $is_console = !$worker;
        if ($is_console) {
            // Wenn Sie nicht möchten, dass diese Initialisierung in der Befehlszeile ausgeführt wird, geben Sie hier einfach zurück
            return;
        }
        
        // Alle 10 Sekunden ausführen
        \Workerman\Timer::add(10, function () {
            // Zur Veranschaulichung wird hier die Ausgabe anstelle des Meldevorgangs verwendet
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Hinweis**
> Das Framework führt auch beim Verwenden der Befehlszeile die in `config/bootstrap.php` konfigurierte start-Methode aus. Anhand der Nicht-Nullheit von `$worker` können wir feststellen, ob es sich um eine Befehlszeilenumgebung handelt, und dann entscheiden, ob der Geschäftsinitialisierungscode ausgeführt werden soll.

#### Konfiguration beim Start des Prozesses
Öffnen Sie `config/bootstrap.php` und fügen Sie die `MemReport`-Klasse zu den Startelementen hinzu.
```php
return [
    // ...andere Konfigurationen hier ausgelassen...
    
    app\bootstrap\MemReport::class,
];
```

Auf diese Weise haben wir den Prozess der Geschäftsinitialisierung abgeschlossen.

## Zusätzliche Erklärung
Nach dem Start des [benutzerdefinierten Prozesses](../process.md) wird auch die in `config/bootstrap.php` konfigurierte start-Methode ausgeführt. Anhand von `$worker->name` können wir feststellen, um welchen Prozess es sich handelt, und dann entscheiden, ob Ihr Geschäftsinitialisierungscode in diesem Prozess ausgeführt werden soll. Wenn wir beispielsweise den Überwachungsprozess nicht überwachen müssen, wäre der Inhalt von `MemReport.php` ähnlich wie folgt:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Ist es eine Befehlszeile?
        $is_console = !$worker;
        if ($is_console) {
            // Wenn Sie nicht möchten, dass diese Initialisierung in der Befehlszeile ausgeführt wird, geben Sie hier einfach zurück
            return;
        }
        
        // Der Überwachungsprozess führt den Timer nicht aus
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Alle 10 Sekunden ausführen
        \Workerman\Timer::add(10, function () {
            // Zur Veranschaulichung wird hier die Ausgabe anstelle des Meldevorgangs verwendet
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
