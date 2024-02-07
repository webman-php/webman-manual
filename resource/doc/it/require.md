# Requisiti di sistema

## Sistema Linux
Il sistema Linux richiede le estensioni `posix` e `pcntl`, le quali sono incorporate in PHP e di solito non richiedono un'installazione separata.

Se sei un utente di Baota, è sufficiente disabilitare o rimuovere le funzioni che iniziano con `pnctl_` in Baota.

L'estensione `event` non è obbligatoria, ma è consigliabile installarla per migliorare le prestazioni.

## Sistema Windows
Webman può funzionare su sistemi Windows, ma a causa delle limitazioni nell'impostazione di processi multipli e processi in background, si consiglia di usare Windows solo come ambiente di sviluppo e di usare i sistemi Linux per l'ambiente di produzione.

Nota: Il sistema Windows non dipende dalle estensioni `posix` e `pcntl`.
