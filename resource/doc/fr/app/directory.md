# Structure du répertoire

```plaintext
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

Nous pouvons observer une structure de répertoire et des fichiers de configuration identiques à ceux de webman dans le répertoire du plugin. En réalité, l'expérience de développement d'un plugin est pratiquement identique à celle du développement d'une application webman normale. Les répertoires et les noms de fichiers de plugin suivent la spécification PSR-4, et puisque les plugins sont tous placés dans le répertoire plugin, les espaces de noms commencent par plugin, par exemple `plugin\foo\app\controller\UserController`.

## À propos du répertoire api
Chaque plugin contient un répertoire api. Si votre application fournit des interfaces internes pour être appelées par d'autres applications, vous devez placer ces interfaces dans le répertoire api. Il convient de noter que ces interfaces font référence aux appels de fonctions, et non aux appels réseau. Par exemple, le plugin "email" fournit une interface "Email::send()" dans le fichier `plugin/email/api/Email.php` pour envoyer des e-mails à partir d'autres applications. De plus, `plugin/email/api/Install.php` est généré automatiquement et est utilisé par le marché des plugins webman-admin pour exécuter des opérations d'installation ou de désinstallation.
