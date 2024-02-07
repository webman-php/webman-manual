# Struttura del progetto
```
.
├── app                           Cartella delle applicazioni
│   ├── controller                Cartella dei controller
│   ├── model                     Cartella dei modelli
│   ├── view                      Cartella delle viste
│   ├── middleware                Cartella dei middleware
│   │   └── StaticFile.php        Middleware per file statici predefinito
|   └── functions.php             Le funzioni personalizzate dell'applicazione vanno in questo file
|
├── config                        Cartella di configurazione
│   ├── app.php                   Configurazione dell'applicazione
│   ├── autoload.php              I file configurati qui verranno caricati automaticamente
│   ├── bootstrap.php             Configurazioni dei callback da eseguire all'avvio del processo onWorkerStart
│   ├── container.php             Configurazione del contenitore
│   ├── dependence.php            Configurazione delle dipendenze del contenitore
│   ├── database.php              Configurazione del database
│   ├── exception.php             Configurazione delle eccezioni
│   ├── log.php                   Configurazione dei log
│   ├── middleware.php            Configurazione dei middleware
│   ├── process.php               Configurazione dei processi personalizzati
│   ├── redis.php                 Configurazione di redis
│   ├── route.php                 Configurazione del routing
│   ├── server.php                Configurazione del server, porte, numeri di processo, ecc.
│   ├── view.php                  Configurazione delle viste
│   ├── static.php                Configurazione dei file statici e dello switch dei file statici
│   ├── translation.php           Configurazione delle lingue
│   └── session.php               Configurazione della sessione
├── public                        Cartella delle risorse statiche
├── process                       Cartella dei processi personalizzati
├── runtime                       Cartella di runtime dell'applicazione, necessita dei permessi di scrittura
├── start.php                     File di avvio del servizio
├── vendor                        Cartella delle librerie di terze parti installate tramite composer
└── support                       Adattatori di librerie (comprensivi delle librerie di terze parti)
    ├── Request.php               Classe di richiesta
    ├── Response.php              Classe di risposta
    ├── Plugin.php                Script di installazione e disinstallazione del plugin
    ├── helpers.php               Funzioni di supporto (le funzioni personalizzate dell'applicazione vanno nel file app/functions.php)
    └── bootstrap.php             Script di inizializzazione dopo l'avvio del processo
```
