# Nota per i programmatori

## Sistema operativo
webman supporta l'esecuzione su sistemi Linux e Windows. Tuttavia, poiché workerman non supporta la configurazione multi-processo e il processo demone in Windows, si consiglia di utilizzare Windows solo per lo sviluppo e il debug in ambienti di sviluppo e di utilizzare il sistema Linux in ambienti di produzione.

## Modalità di avvio
Su **sistemi Linux**, avviare con il comando `php start.php start` (modalità di debug) o `php start.php start -d` (modalità demone).
Su **sistemi Windows**, eseguire `windows.bat` o utilizzare il comando `php windows.php` per avviare, e premere ctrl+c per interrompere. Il sistema Windows non supporta i comandi stop, reload, status, reload connections, etc.

## Memoria residente
webman è un framework a memoria residente; di solito, una volta che i file php sono stati caricati in memoria, vengono riutilizzati e non vengono più letti dal disco rigido (ad eccezione dei file di template). Quindi, le modifiche al codice di produzione o alla configurazione richiedono l'esecuzione di `php start.php reload` per essere effettive. Se si modificano le configurazioni relative al processo o si installano nuovi pacchetti composer, è necessario riavviare con `php start.php restart`.

> Per facilitare lo sviluppo, webman include un monitor come processo personalizzato per monitorare le modifiche ai file di business e eseguire automaticamente il ricaricamento quando vengono aggiornati. Questa funzionalità è disponibile solo quando workerman viene eseguito in modalità di debug (senza l'opzione `-d` durante l'avvio). Gli utenti Windows devono eseguire `windows.bat` o `php windows.php` per abilitare questa funzionalità.

## Output delle istruzioni
Nel progetto tradizionale php-fpm, l'uso di funzioni come `echo` o `var_dump` per l'output dei dati li visualizza direttamente nella pagina web, mentre in webman, tali output vengono di solito visualizzati nel terminale e non nella pagina web (ad eccezione dell'output nei file di template).

## Evitare di utilizzare istruzioni `exit` o `die`
Eseguire `die` o `exit` comporterà la chiusura e il riavvio del processo, impedendo al richiesta corrente di essere elaborata correttamente.

## Evitare di utilizzare la funzione `pcntl_fork`
L'uso di `pcntl_fork` per creare un processo non è consentito in webman.
