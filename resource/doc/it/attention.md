# Note di programmazione

## Sistema operativo
webman supporta simultaneamente l'esecuzione su sistemi Linux e Windows. Tuttavia, poiché workerman non supporta la configurazione multi-processo e il processo di gestione su Windows, si consiglia solo di utilizzare Windows per lo sviluppo e il debug dell'ambiente di sviluppo, mentre per l'ambiente di produzione si consiglia di utilizzare il sistema operativo Linux.

## Modalità di avvio
**Sistema Linux** si avvia con il comando `php start.php start` (modalità debug) `php start.php start -d` (modalità processo di gestione)
**Sistema Windows** si avvia eseguendo `windows.bat` oppure utilizzando il comando `php windows.php`, e si interrompe premendo ctrl c. Il sistema Windows non supporta i comandi stop, reload, status, connections, etc.

## Memoria residente
webman è un framework in memoria residente, in generale, una volta che i file PHP sono stati caricati in memoria, vengono riutilizzati e non vengono letti nuovamente dal disco (ad eccezione dei file di modello). Quindi, per rendere effettive le modifiche al codice di business o alla configurazione nell'ambiente di produzione, è necessario eseguire il comando `php start.php reload`. Se si modificano le impostazioni del processo o installando nuovi pacchetti composer, è necessario riavviare con `php start.php restart`.

> Per facilitare lo sviluppo, webman include un processo di monitoraggio personalizzato per rilevare gli aggiornamenti dei file di business. Quando vengono aggiornati file di business, viene eseguito automaticamente il reload. Questa funzionalità è disponibile solo quando workerman è in esecuzione in modalità debug (senza `-d` all'avvio). Gli utenti Windows devono eseguire `windows.bat` o `php windows.php` per utilizzare questa funzionalità.

## Riguardo alle istruzioni di output
Nei progetti tradizionali con php-fpm, l'utilizzo di funzioni come `echo` e `var_dump` per l'output dei dati viene visualizzato direttamente nella pagina, mentre in webman, tali output di solito vengono visualizzati nel terminale e non nella pagina (ad eccezione degli output dai file di modello).

## Evitare l'uso di istruzioni `exit` o `die`
L'esecuzione di `die` o `exit` causa la chiusura del processo e il riavvio, impedendo una corretta risposta alla richiesta corrente.

## Evitare l'uso delle funzioni `pcntl_fork`
L'uso di `pcntl_fork` per creare un processo non è consentito in webman.
