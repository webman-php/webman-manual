# Struttura della directory

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

Abbiamo una struttura delle directory e dei file di configurazione simile a webman all'interno di una directory del plugin. In realtà, l'esperienza di sviluppo di un plugin è praticamente indistinguibile dallo sviluppo di un'applicazione webman normale.
Il nome e la directory del plugin seguono la specifica PSR4, in quanto i plugin sono tutti posizionati nella directory "plugin", quindi lo spazio dei nomi inizia con "plugin\", ad esempio `plugin\foo\app\controller\UserController`.

## Riguardo alla directory api
All'interno di ciascun plugin esiste una directory api. Se la tua applicazione fornisce delle interfacce interne per essere chiamate da altre applicazioni, è necessario posizionare le interfacce nella directory api.
Si noti che qui le interfacce si riferiscono alle chiamate di funzione, non alle chiamate di rete.
Ad esempio, il plugin "email" fornisce un'interfaccia `Email::send()` nel file `plugin/email/api/Email.php`, utilizzata per inviare email da altre applicazioni.
Inoltre, il file `plugin/email/api/Install.php` viene generato automaticamente ed è utilizzato per consentire al mercato dei plugin webman-admin di eseguire operazioni di installazione o disinstallazione.
