# Ausführungsprozess

## Prozessstartprozess

Nach dem Ausführen von `php start.php start` wird der Ausführungsprozess wie folgt durchgeführt:

1. Laden der Konfigurationen im Ordner config/
2. Einstellen der relevanten Konfigurationen für den Worker wie z. B. `pid_file`, `stdout_file`, `log_file`, `max_package_size`
3. Erstellen des webman-Prozesses und Lauschen an einem Port (standardmäßig 8787)
4. Erstellen von benutzerdefinierten Prozessen gemäß der Konfiguration
5. Nach dem Starten des webman-Prozesses und der benutzerdefinierten Prozesse wird folgende Logik ausgeführt (alle in onWorkerStart):
   ① Laden der in `config/autoload.php` festgelegten Dateien, wie z. B. `app/functions.php`
   ② Laden der in `config/middleware.php` (einschließlich `config/plugin/*/*/middleware.php`) festgelegten Middleware
   ③ Ausführen von `config/bootstrap.php` (einschließlich `config/plugin/*/*/bootstrap.php`), um die Startmethoden der festgelegten Klassen auszuführen, die zur Initialisierung von Modulen dienen, z. B. Laravel-Datenbankinitialisierungsverbindung
   ④ Laden der in `config/route.php` (einschließlich `config/plugin/*/*/route.php`) festgelegten Routen

## Anfrageverarbeitungsprozess
1. Überprüfen, ob die Anfrage-URL einer statischen Datei im Ordner public entspricht. Falls ja, die Datei zurückgeben (Anfrage beenden). Falls nein, zum Schritt 2 übergehen.
2. Anhand der URL überprüfen, ob eine bestimmte Route getroffen wurde. Falls nicht, zum Schritt 3 übergehen. Falls ja, zum Schritt 4 übergehen.
3. Überprüfen, ob die Standardroute deaktiviert ist. Falls ja, Fehler 404 zurückgeben (Anfrage beenden). Falls nein, zum Schritt 4 übergehen.
4. Suche nach der Middleware des entsprechenden Controllers für die Anfrage, führe die Voraktionen der Middleware in der festgelegten Reihenfolge aus (Onion-Modell der Anfragephase), führe die Geschäftslogik des Controllers aus, führe die Nachaktionen der Middleware aus (Onion-Modell der Antwortphase) und beende die Anfrage. (Siehe auch [Middleware Onion-Modell](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
