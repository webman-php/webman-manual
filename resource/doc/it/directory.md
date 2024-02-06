# Struttura delle directory
```
.
├── app                           Directory dell'applicazione
│   ├── controller                Directory dei controller
│   ├── model                     Directory dei modelli
│   ├── view                      Directory delle viste
│   ├── middleware                Directory dei middleware
│   │   └── StaticFile.php        Middleware per file statici predefinito
|   └── functions.php             Le funzioni personalizzate del business vengono scritte in questo file
|
├── config                        Directory di configurazione
│   ├── app.php                   Configurazione dell'applicazione
│   ├── autoload.php              I file configurati qui verranno caricati automaticamente
│   ├── bootstrap.php             Configurazioni di callback eseguite durante l'avvio del processo in onWorkerStart
│   ├── container.php             Configurazione del contenitore
│   ├── dependence.php            Configurazione delle dipendenze del contenitore
│   ├── database.php              Configurazione del database
│   ├── exception.php             Configurazione delle eccezioni
│   ├── log.php                   Configurazione dei log
│   ├── middleware.php            Configurazione dei middleware
│   ├── process.php               Configurazione dei processi personalizzati
│   ├── redis.php                 Configurazione di Redis
│   ├── route.php                 Configurazione del routing
│   ├── server.php                Configurazione del server, porte, numero di processi, ecc.
│   ├── view.php                  Configurazione delle viste
│   ├── static.php                Attivazione dei file statici e configurazione dei middleware per i file statici
│   ├── translation.php           Configurazione della multilingua
│   └── session.php               Configurazione della sessione
├── public                        Directory delle risorse statiche
├── process                       Directory dei processi personalizzati
├── runtime                       Directory delle runtime dell'applicazione, necessita di permessi di scrittura
├── start.php                     File di avvio del servizio
├── vendor                        Directory delle librerie di terze parti installate con composer
└── support                       Adattamento della libreria (inclusa la libreria di terze parti)
    ├── Request.php               Classe di richiesta
    ├── Response.php              Classe di risposta
    ├── Plugin.php                Script di installazione e disinstallazione del plugin
    ├── helpers.php               Funzioni di supporto (si prega di scrivere le funzioni personalizzate del business in app/functions.php)
    └── bootstrap.php             Script di inizializzazione dopo l'avvio del processo
```
