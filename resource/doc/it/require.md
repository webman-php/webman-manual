# Requisiti di sistema

## Sistema operativo Linux
Il sistema operativo Linux richiede le estensioni `posix` e `pcntl`, le quali sono integrate in PHP e di solito non richiedono un'installazione separata.

Per gli utenti di Baota, è sufficiente disattivare o rimuovere le funzioni che iniziano con `pnctl_` nella piattaforma Baota.

L'estensione `event` non è obbligatoria, ma è consigliabile installarla per migliorare le prestazioni.

## Sistema operativo Windows
Webman può essere eseguito su sistema Windows, ma poiché non è possibile configurare processi multipli, demoni, e altri aspetti, si consiglia di utilizzare Windows solamente come ambiente di sviluppo. Per l'ambiente di produzione, si prega di utilizzare il sistema operativo Linux.

Nota: Su sistema Windows non vi è alcuna dipendenza dall'estensione `posix` e `pcntl`.
