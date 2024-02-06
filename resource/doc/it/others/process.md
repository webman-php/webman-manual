# Flusso di esecuzione

## Flusso di avvio del processo

Dopo l'esecuzione di php start.php start, il flusso di esecuzione è il seguente:

1. Caricamento delle configurazioni presenti in config/
2. Impostazione delle configurazioni relative a Worker come `pid_file`, `stdout_file`, `log_file`, `max_package_size`, ecc.
3. Creazione del processo webman e attesa sulla porta (per default 8787)
4. Creazione dei processi personalizzati in base alle configurazioni
5. Dopo l'avvio del processo webman e dei processi personalizzati, viene eseguita la seguente logica (tutto ciò avviene nell'evento onWorkerStart):
  ① Caricamento dei file impostati in `config/autoload.php`, come ad esempio `app/functions.php`
  ② Caricamento dei middleware definiti in `config/middleware.php` (compresi quelli in `config/plugin/*/*/middleware.php`)
  ③ Esecuzione del metodo di avvio della classe definito in `config/bootstrap.php` (compreso quello in `config/plugin/*/*/bootstrap.php`), utilizzato per inizializzare determinati moduli, come ad esempio l'inizializzazione della connessione al database di Laravel
  ④ Caricamento delle definizioni delle rotte presenti in `config/route.php` (compreso quelle in `config/plugin/*/*/route.php`)

## Flusso di gestione delle richieste
1. Controllo se l'URL della richiesta corrisponde a un file statico presente in public; in caso affermativo, viene restituito il file (fine della richiesta). In caso contrario, si passa al punto 2.
2. In base all'URL della richiesta, si verifica se corrisponde a una determinata rotta. Se non corrisponde, si passa al punto 3; altrimenti, si passa al punto 4.
3. Controllo se la rotta predefinita è disabilitata; in caso affermativo, viene restituito il codice di errore 404 (fine della richiesta). In caso contrario, si passa al punto 4.
4. Trovare e eseguire i middleware del controller corrispondente alla richiesta, eseguire le operazioni preliminari dei middleware in ordine (fase di richiesta del modello ad anello), eseguire la logica di business del controller, eseguire le operazioni posticipate dei middleware (fase di risposta del modello ad anello) e terminare la richiesta. (Vedi anche il [modello ad anello dei middleware](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
