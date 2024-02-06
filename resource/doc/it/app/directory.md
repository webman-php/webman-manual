# Struttura della directory

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

Abbiamo visto che un'applicazione plugin ha la stessa struttura di directory e file di configurazione di webman. In realtà, l'esperienza di sviluppo di un'applicazione plugin è praticamente identica a quella dello sviluppo di un'applicazione normale webman.
La directory e i nomi delle cartelle dei plugin seguono la specifica PSR4, quindi lo spazio dei nomi inizia con "plugin", ad esempio `plugin\foo\app\controller\UserController`.

## Riguardo alla directory api
Ogni plugin ha una directory "api". Se la tua app fornisce delle interfacce interne per essere chiamate da altre app, è necessario inserire tali interfacce nella directory "api".
Si noti che qui per "interfacce" si intendono le interfacce di chiamata di funzione, non le interfacce di chiamata di rete.
Ad esempio, il "plugin di posta elettronica" fornisce un'interfaccia "Email::send()" in "plugin/email/api/Email.php" per inviare email da altre app.
Inoltre, "plugin/email/api/Install.php" è generato automaticamente per consentire al marketplace del plugin webman-admin di eseguire operazioni di installazione o disinstallazione.
