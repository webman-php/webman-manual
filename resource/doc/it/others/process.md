# Processo di avvio

Il processo di avvio dopo l'esecuzione di php start.php start è il seguente:

1. Caricamento delle configurazioni nella directory config/
2. Impostazione delle configurazioni relative a Worker come `pid_file`, `stdout_file`, `log_file`, `max_package_size`, etc.
3. Creazione del processo webman e ascolto della porta (predefinita 8787)
4. Creazione di processi personalizzati in base alle configurazioni
5. Dopo l'avvio dei processi webman e personalizzati, viene eseguita la logica seguente (tutto eseguito in onWorkerStart):
   ① Caricamento dei file impostati in `config/autoload.php`, come `app/functions.php`
   ② Caricamento dei middleware impostati in `config/middleware.php` (inclusi i file `config/plugin/*/*/middleware.php`)
   ③ Esecuzione della classe start definita in `config/bootstrap.php` (inclusi i file `config/plugin/*/*/bootstrap.php`) per inizializzare alcuni moduli, come l'inizializzazione della connessione al database Laravel
   ④ Caricamento delle rotte definite in `config/route.php` (inclusi i file `config/plugin/*/*/route.php`)

# Processo di gestione delle richieste

1. Verifica se l'URL della richiesta corrisponde a un file statico nella directory public; in caso affermativo, restituiscilo (fine della richiesta). In caso contrario, procedi con il punto 2.
2. Verifica se l'URL corrisponde a una delle rotte definite; se non corrisponde, passa al punto 3. Se corrisponde, passa al punto 4.
3. Verifica se le rotte predefinite sono disabilitate; in caso affermativo, restituisci il codice 404 (fine della richiesta). In caso contrario, passa al punto 4.
4. Trova i middleware corrispondenti al controller richiesto e esegui le operazioni preliminari del middleware in ordine (fase di richiesta del modello a strati di cipolla), esegui la logica del business del controller, esegui le operazioni successive al middleware (fase di risposta del modello a strati di cipolla) e termina la richiesta. (Vedi il [modello a strati di cipolla dei middleware](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
