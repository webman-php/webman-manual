# Verzeichnisstruktur

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

Wir sehen eine Plugin-Anwendung mit der gleichen Verzeichnisstruktur und Konfigurationsdateien wie webman. Tatsächlich gibt es kaum einen Unterschied bei der Entwicklungserfahrung im Vergleich zur Entwicklung einer Standardanwendung in webman.
Die Verzeichnisstruktur und Namensgebung des Plugins entsprechen dem PSR4-Standard. Da die Plugins alle im plugin-Verzeichnis liegen, beginnen die Namensräume alle mit "plugin", zum Beispiel `plugin\foo\app\controller\UserController`.

## Über das Verzeichnis "api"
Jedes Plugin enthält ein Verzeichnis "api". Wenn Ihre Anwendung einige interne Schnittstellen für andere Anwendungen bereitstellt, müssen Sie die Schnittstellen im api-Verzeichnis ablegen.
Zu beachten ist, dass hier von Funktionsaufruf-Schnittstellen die Rede ist, nicht von Netzwerkschnittstellen.
Zum Beispiel stellt das "E-Mail-Plugin" in `plugin/email/api/Email.php` eine Schnittstelle `Email::send()` bereit, die von anderen Anwendungen zum Versenden von E-Mails aufgerufen werden kann.
Außerdem ist `plugin/email/api/Install.php` automatisch generiert und dient dazu, dass der webman-admin Plugin-Marktplatz Installations- oder Deinstallationsoperationen aufrufen kann.
