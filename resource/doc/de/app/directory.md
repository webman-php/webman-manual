# Verzeichnisstruktur

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

Wir sehen eine Anwendungs-Plugin-Struktur mit der gleichen Verzeichnisstruktur und Konfigurationsdateien wie webman. Tatsächlich gibt es praktisch keinen Unterschied in der Entwicklungserfahrung im Vergleich zur normalen Anwendungsentwicklung mit webman. Die Plugin-Verzeichnisse und -Namen folgen dem PSR4-Standard. Da alle Plugins im plugin-Verzeichnis platziert sind, beginnt der Namespace mit plugin, zum Beispiel `plugin\foo\app\controller\UserController`.

## Über das API-Verzeichnis
Jedes Plugin hat ein API-Verzeichnis. Wenn Ihre Anwendung einige interne Schnittstellen für andere Anwendungen zur Verfügung stellt, müssen Sie die Schnittstellen im API-Verzeichnis ablegen. Beachten Sie, dass hier von Funktionsaufruf-Schnittstellen die Rede ist, nicht von Netzwerkschnittstellen. Zum Beispiel stellt das `E-Mail-Plugin` die Schnittstelle `Email::send()` in `plugin/email/api/Email.php` zur Verfügung, die von anderen Anwendungen zum Versenden von E-Mails aufgerufen werden kann. Darüber hinaus wird `plugin/email/api/Install.php` automatisch generiert und von webman-admin-Plugin-Marktplätzen aufgerufen, um Installations- oder Deinstallationsvorgänge auszuführen.
