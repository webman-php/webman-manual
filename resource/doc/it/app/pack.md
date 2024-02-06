# Imballaggio

Ad esempio, imballare il plugin dell'applicazione "foo"

- Impostare il numero di versione in `plugin/foo/config/app.php` (**importante**)
- Eliminare i file non necessari da `plugin/foo`, specialmente i file temporanei per il test dell'upload in `plugin/foo/public`
- Rimuovere le configurazioni del database e di Redis. Se il tuo progetto ha configurazioni indipendenti per il database e per Redis, queste configurazioni dovrebbero essere inizializzate al primo accesso dell'applicazione attraverso un programma di installazione guidata (da implementare manualmente), in modo che gli amministratori possano inserire manualmente le informazioni richieste e generare le configurazioni.
- Ripristinare gli altri file che devono essere ripristinati alla loro versione originale.
- Dopo aver completato le operazioni sopra, entra nella directory `{main_project}/plugin/` e utilizza il comando `zip -r foo.zip foo` per generare foo.zip.
