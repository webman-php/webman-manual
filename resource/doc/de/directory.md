# Verzeichnisstruktur
```
.
├── app                           Anwendungsverzeichnis
│   ├── controller                Controller-Verzeichnis
│   ├── model                     Model-Verzeichnis
│   ├── view                      Ansichtsverzeichnis
│   ├── middleware                Middleware-Verzeichnis
│   │   └── StaticFile.php        Eigenes Static-Datei-Middleware
|   └── functions.php             Geschäftsspezifische Funktionen werden in dieser Datei geschrieben
|
├── config                        Konfigurationsverzeichnis
│   ├── app.php                   Anwendungs-Konfiguration
│   ├── autoload.php              Hier konfigurierte Dateien werden automatisch geladen
│   ├── bootstrap.php             Konfiguration für den Callback, der beim Starten des Prozesses onWorkerStart ausgeführt wird
│   ├── container.php             Container-Konfiguration
│   ├── dependence.php            Konfiguration der Container-Abhängigkeiten
│   ├── database.php              Datenbank-Konfiguration
│   ├── exception.php             Ausnahme-Konfiguration
│   ├── log.php                   Protokoll-Konfiguration
│   ├── middleware.php            Middleware-Konfiguration
│   ├── process.php               Konfiguration für benutzerdefinierte Prozesse
│   ├── redis.php                 Redis-Konfiguration
│   ├── route.php                 Routenkonfiguration
│   ├── server.php                Konfiguration für Server-Ports, Prozessanzahl usw.
│   ├── view.php                  Ansichtskonfiguration
│   ├── static.php                Konfiguration für statische Dateien und das Static-File-Middleware
│   ├── translation.php           Konfiguration für Mehrsprachigkeit
│   └── session.php               Sitzungs-Konfiguration
├── public                        Verzeichnis für statische Ressourcen
├── process                       Verzeichnis für benutzerdefinierte Prozesse
├── runtime                       Laufzeitverzeichnis der Anwendung, benötigt Schreibrechte
├── start.php                     Startdatei des Dienstes
├── vendor                        Verzeichnis für mit Composer installierte Drittanbieter-Bibliotheken
└── support                       Bibliotheksanpassung (einschließlich Drittanbieter-Bibliotheken)
    ├── Request.php               Anfrageklasse
    ├── Response.php              Antwortklasse
    ├── Plugin.php                Skript zum Installieren und Deinstallieren von Plugins
    ├── helpers.php               Hilfsfunktionen (Geschäftsspezifische Funktionen bitte in app/functions.php schreiben)
    └── bootstrap.php             Initialisierungsskript nach dem Starten des Prozesses
```
