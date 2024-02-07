# Geschäftsinitialisierung

Manchmal müssen wir nach dem Start eines Prozesses einige Geschäftsinitialisierungen vornehmen. Diese Initialisierung wird nur einmal im Lebenszyklus des Prozesses ausgeführt, z. B. das Einrichten eines Timers nach dem Start des Prozesses oder das Initialisieren der Datenbankverbindung usw. Im Folgenden werden wir dies genauer erläutern.

## Prinzip
Gemäß den Erläuterungen im **[Ausführungsprozess](process.md)** lädt webman nach dem Start des Prozesses die in `config/bootstrap.php` (einschließlich `config/plugin/*/*/bootstrap.php`) festgelegten Klassen und führt die `start`-Methode der Klasse aus. Wir können Geschäftslogik in die `start`-Methode einfügen, um die geschäftlichen Initialisierungsvorgänge nach dem Start des Prozesses abzuschließen.

## Ablauf
Angenommen, wir möchten einen Timer einrichten, um den aktuellen Speicherverbrauch des Prozesses zu melden. Diese Klasse wird 'MemReport' genannt.

#### Befehl ausführen
Führen Sie den Befehl `php webman make:bootstrap MemReport` aus, um die Initialisierungsdatei `app/bootstrap/MemReport.php` zu generieren.

> **Hinweis**
> Wenn Ihr webman das `webman/console` nicht installiert hat, führen Sie den Befehl `composer require webman/console` zur Installation aus.

#### Initialisierungsdatei bearbeiten
Bearbeiten Sie `app/bootstrap/MemReport.php`. Der Inhalt sollte in etwa wie folgt aussehen:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Ist es eine Befehlszeilenumgebung?
        $is_console = !$worker;
        if ($is_console) {
            // Wenn Sie nicht möchten, dass diese Initialisierung in der Befehlszeilenumgebung ausgeführt wird, geben Sie hier einfach zurück.
            return;
        }
        
        // Alle 10 Sekunden ausführen
        \Workerman\Timer::add(10, function () {
            // Für eine einfachere Darstellung verwenden wir hier die Ausgabe anstelle des Meldungsvorgangs
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Hinweis**
> Wenn das Framework im Befehlszeilenmodus verwendet wird, wird auch die in `config/bootstrap.php` konfigurierte `start`-Methode ausgeführt. Wir können anhand von `worker` überprüfen, ob es sich um eine Befehlszeilenumgebung handelt, und so entscheiden, ob die Geschäftsinitialisierungslogik ausgeführt wird.

#### Konfiguration beim Start des Prozesses
Öffnen Sie `config/bootstrap.php` und fügen Sie die `MemReport`-Klasse zur Startsequenz hinzu.
```php
return [
    // ... Andere Konfigurationen werden hier ausgelassen...
    
    app\bootstrap\MemReport::class,
];
```

Damit haben wir einen Geschäftsinitialisierungsprozess abgeschlossen.

## Zusätzliche Hinweise
[Benutzerdefinierte Prozesse](../process.md) führen die in `config/bootstrap.php` konfigurierte `start`-Methode ebenfalls aus. Durch Überprüfen von `$worker->name` können wir feststellen, um welchen Prozess es sich handelt, und entscheiden, ob Ihr Geschäftsinitialisierungscode in diesem Prozess ausgeführt werden soll. Angenommen, wir müssen den `monitor`-Prozess nicht überwachen, dann könnte der Inhalt von `MemReport.php` wie folgt aussehen:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Ist es eine Befehlszeilenumgebung?
        $is_console = !$worker;
        if ($is_console) {
            // Wenn Sie nicht möchten, dass diese Initialisierung in der Befehlszeilenumgebung ausgeführt wird, geben Sie hier einfach zurück
            return;
        }
        
        // Der Monitorprozess führt den Timer nicht aus
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Alle 10 Sekunden ausführen
        \Workerman\Timer::add(10, function () {
            // Für eine einfachere Darstellung verwenden wir hier die Ausgabe anstelle des Meldungsvorgangs
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
