# Ausführungsablauf

## Prozessstartablauf

Nach dem Ausführen von `php start.php start` erfolgt der folgende Ablauf:

1. Laden der Konfigurationen in config/
2. Konfiguration der Arbeitskraftbezogenen Einstellungen wie `pid_file`, `stdout_file`, `log_file`, `max_package_size`, usw.
3. Erstellung des Webman-Prozesses und Zuhören an einem Port (standardmäßig 8787)
4. Erstellung eines benutzerdefinierten Prozesses entsprechend der Konfiguration
5. Nach dem Start des Webman-Prozesses und des benutzerdefinierten Prozesses wird der folgende Logik ausgeführt (alles im onWorkerStart):
   ① Laden der Dateien, die in `config/autoload.php` eingestellt sind, wie z.B. `app/functions.php`
   ② Laden von in `config/middleware.php` (einschließlich `config/plugin/*/*/middleware.php`) eingestellten Middlewares
   ③ Ausführung der "start"-Methode der in `config/bootstrap.php` (einschließlich `config/plugin/*/*/bootstrap.php`) eingestellten Klassen zur Initialisierung von Modulen, so zum Beispiel die Initialisierung der Laravel-Datenbankverbindung
   ④ Einlesen der in `config/route.php` (einschließlich `config/plugin/*/*/route.php`) definierten Routen

## Behandlungsablauf von Anfragen
1. Prüfung, ob die Anfrage-URL einer statischen Datei im public-Verzeichnis entspricht; falls ja, Rückgabe der Datei und Beendigung der Anfrage. Andernfalls, weiter mit 2.
2. Überprüfung, ob die URL einer Route entspricht. Falls nicht, weiter mit 3. Falls ja, weiter mit 4.
3. Prüfung, ob die Standardroute deaktiviert ist; falls ja, Rückgabe von 404 und Beendigung der Anfrage. Andernfalls, weiter mit 4.
4. Suche nach den Middlewares des angefragten Controllers, Ausführung der Middleware-Voraktionen in Reihenfolge (Onion-Model im Anfragebereich), Ausführung der Geschäftslogik des Controllers, Ausführung der Middleware-Nachaktionen (Onion-Model im Antwortbereich) und Beendigung der Anfrage (Siehe auch das [Middleware-Zwiebelmodell](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B)).
