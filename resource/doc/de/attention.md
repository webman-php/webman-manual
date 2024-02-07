# Programmierhinweise

## Betriebssystem
webman unterstützt sowohl Linux- als auch Windows-Betriebssysteme. Aufgrund der Tatsache, dass Workerman in Windows keine Unterstützung für die Einstellung von Mehrprozessen und Hintergrundprozessen bietet, wird die Verwendung von Windows-Systemen nur für die Entwicklungsumgebung zur Entwicklung und Fehlerbehebung empfohlen. In der Produktionsumgebung sollte ein Linux-System verwendet werden.

## Startmethode
**Linux-System**: Verwenden Sie den Befehl `php start.php start` (Debug-Modus) oder `php start.php start -d` (Hintergrundprozessmodus) zum Starten.
**Windows-System**: Führen Sie `windows.bat` aus oder verwenden Sie den Befehl `php windows.php` zum Starten, und drücken Sie Strg + C, um anzuhalten. Windows-Systeme unterstützen keine Befehle wie stop, reload, status, reload connections.

## Immer im Speicher aktiv
webman ist ein Framework im permanenten Speicher. Im Allgemeinen wird eine PHP-Datei nach dem Laden in den Speicher wiederverwendet und nicht erneut von der Festplatte gelesen (mit Ausnahme von Vorlagen-Dateien). Daher müssen Änderungen an Geschäftscode oder Konfigurationen in der Produktionsumgebung mit `php start.php reload` aktiviert werden. Wenn prozessbezogene Konfigurationen geändert oder neue Composer-Pakete installiert wurden, ist ein Neustart mit `php start.php restart` erforderlich.

> Zur Vereinfachung der Entwicklung enthält webman einen Monitor, einen benutzerdefinierten Prozess zur Überwachung von Aktualisierungen der Geschäftsdateien. Wenn Geschäftsdateien aktualisiert werden, wird automatisch ein Reload ausgeführt. Diese Funktion ist nur in Workerman im Debug-Modus (ohne `-d` beim Starten) verfügbar. Windows-Benutzer müssen `windows.bat` ausführen oder `php windows.php` verwenden, um dies zu aktivieren.

## Ausgabeanweisungen
In herkömmlichen PHP-FPM-Projekten werden Daten mit Befehlen wie `echo` und `var_dump` direkt auf der Seite angezeigt, während diese Ausgaben in webman häufig im Terminal angezeigt werden und nicht auf der Seite (mit Ausnahme von Ausgaben in Vorlagen).

## Vermeiden Sie die Verwendung von `exit` und `die` Anweisungen
Das Ausführen von `die` oder `exit` führt dazu, dass der Prozess beendet und neu gestartet wird, was dazu führt, dass die aktuelle Anfrage nicht richtig beantwortet wird.

## Vermeiden Sie die Verwendung der Funktion `pcntl_fork`
Die Verwendung von `pcntl_fork` zum Erstellen eines Prozesses ist in webman nicht zulässig.
