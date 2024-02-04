# Verzeichnisstruktur
```
.
├── app                           Anwendungsverzeichnis
│   ├── controller                Controller-Verzeichnis
│   ├── model                     Model-Verzeichnis
│   ├── view                      Ansichtsverzeichnis
│   ├── middleware                Middleware-Verzeichnis
│   │   └── StaticFile.php        Mitgelieferter statischer Datei-Middleware
|   └── functions.php             Geschäftsdefinierte Funktionen werden in dieser Datei gespeichert
|
├── config                        Konfigurationsverzeichnis
│   ├── app.php                   Anwendungsconfig
│   ├── autoload.php              Hier konfigurierte Dateien werden automatisch geladen
│   ├── bootstrap.php             Konfiguration der Callbacks, die beim Starten des Prozesses in onWorkerStart ausgeführt werden
│   ├── container.php             Container-Konfiguration
│   ├── dependence.php            Container-Abhängigkeitskonfiguration
│   ├── database.php              Datenbankkonfiguration
│   ├── exception.php             Ausnahme-Konfiguration
│   ├── log.php                   Protokollkonfiguration
│   ├── middleware.php            Middleware-Konfiguration
│   ├── process.php               Benutzerdefinierte Prozesskonfiguration
│   ├── redis.php                 Redis-Konfiguration
│   ├── route.php                 Routenkonfiguration
│   ├── server.php                Serverkonfiguration wie Ports, Prozessanzahl usw.
│   ├── view.php                  Ansichtskonfiguration
│   ├── static.php                Konfiguration für statische Dateien und Middleware für statische Dateien
│   ├── translation.php           Mehrsprachenkonfiguration
│   └── session.php               Sitzungskonfiguration
├── public                        Verzeichnis für statische Ressourcen
├── process                       Verzeichnis für benutzerdefinierte Prozesse
├── runtime                       Laufzeitverzeichnis der Anwendung, das Schreibrechte benötigt
├── start.php                     Startdatei des Dienstes
├── vendor                        Verzeichnis für von Composer installierte Drittanbieter-Bibliotheken
└── support                       Bibliotheksanpassung (einschließlich Drittanbieter-Bibliotheken)
    ├── Request.php               Anfrageklasse
    ├── Response.php              Antwortklasse
    ├── Plugin.php                Skript zum Installieren und Deinstallieren von Plugins
    ├── helpers.php               Hilfsfunktionen (Geschäftsdefinierte Funktionen bitte in app/functions.php speichern)
    └── bootstrap.php             Skript zur Initialisierung nach dem Start des Prozesses
```
