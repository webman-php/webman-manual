# Programmieranleitung

## Betriebssystem
webman unterstützt gleichzeitig Linux- und Windows-Betriebssysteme. Da Workerman unter Windows jedoch keine Mehrprozessunterstützung und keine Daemon-Unterstützung hat, wird die Verwendung von Windows-Systemen nur für die Entwicklungsumgebung oder zum Debuggen empfohlen. Für den Produktivbetrieb sollte ein Linux-System verwendet werden.

## Startmethode
**Unter Linux** wird der Befehl `php start.php start` (im Debug-Modus) oder `php start.php start -d` (im Daemon-Modus) verwendet, um webman zu starten.
**Unter Windows** kann entweder die Datei `windows.bat` ausgeführt oder der Befehl `php windows.php` verwendet werden, um webman zu starten. Zum Beenden wird `Strg+C` verwendet. Unter Windows werden Befehle wie stop, reload, status oder connections nicht unterstützt.

## In-memory-Betrieb
webman ist ein In-Memory-Framework. Im Allgemeinen werden PHP-Dateien nach dem Laden in den Speicher wiederverwendet und nicht erneut von der Festplatte gelesen, mit Ausnahme von Vorlagen-Dateien. Daher müssen im Produktivbetrieb nach der Änderung des Geschäftslogikcodes oder der Konfiguration `php start.php reload` ausgeführt werden, um die Änderungen wirksam zu machen. Wenn Prozesskonfigurationen geändert oder neue Composer-Pakete installiert wurden, muss `php start.php restart` ausgeführt werden.

> Für eine erleichterte Entwicklung enthält webman einen eigenen Monitor-Prozess, der die Aktualisierung von Geschäftsdateien überwacht. Bei einer Aktualisierung der Geschäftsdateien wird automatisch ein Reload ausgeführt. Diese Funktion ist nur verfügbar, wenn Workerman im Debug-Modus (ohne `-d` beim Start) ausgeführt wird. Windows-Benutzer müssen `windows.bat` oder `php windows.php` ausführen, um diese Funktion zu nutzen.

## Ausgabehinweise
In herkömmlichen PHP-FPM-Projekten werden Daten durch Funktionen wie `echo` und `var_dump` direkt auf der Seite angezeigt. In webman werden diese Ausgaben jedoch in der Regel im Terminal und nicht auf der Seite angezeigt (mit Ausnahme von Ausgaben in Vorlagen-Dateien).

## Vermeiden Sie die Verwendung von `exit` oder `die`
Das Ausführen von `die` oder `exit` führt dazu, dass der Prozess beendet und neu gestartet wird, was dazu führt, dass die aktuelle Anfrage nicht ordnungsgemäß beantwortet wird.

## Vermeiden Sie die Verwendung der Funktion `pcntl_fork`
Das Verwenden von `pcntl_fork` zum Erstellen eines Prozesses ist in webman nicht erlaubt.
