# Structure des répertoires

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

Nous voyons qu'un plugin d'application a la même structure de répertoire et les mêmes fichiers de configuration que webman. En réalité, l'expérience de développement d'un plugin est presque identique à celle du développement d'une application classique webman.
Le répertoire et le nom des plugins suivent la spécification PSR4. Comme tous les plugins sont placés dans le répertoire plugin, les espaces de noms commencent par plugin, par exemple `plugin\foo\app\controller\UserController`.

## À propos du répertoire api
Chaque plugin contient un répertoire api. Si votre application fournit des interfaces internes pour être appelées par d'autres applications, ces interfaces doivent être placées dans le répertoire api.
Il est important de noter que les interfaces mentionnées ici font référence aux appels de fonctions, et non aux appels réseau.
Par exemple, le `plugin d'envoi de courrier` fournit une interface `Email::send()` dans `plugin/email/api/Email.php` pour que d'autres applications puissent l'appeler pour envoyer des courriels.
De plus, `plugin/email/api/Install.php` est généré automatiquement pour permettre au marché des plugins webman-admin d'appeler l'installation ou la désinstallation.
