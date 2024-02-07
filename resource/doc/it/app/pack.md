# Imballaggio

Ad esempio, confezionare l'applicazione plugin "foo"

- Impostare il numero di versione in `plugin/foo/config/app.php` (**importante**)
- Eliminare i file non necessari da `plugin/foo`, specialmente i file temporanei per il test del caricamento in `plugin/foo/public`
- Rimuovere le configurazioni del database e di Redis. Se il tuo progetto ha configurazioni di database e Redis indipendenti, queste configurazioni dovrebbero essere attivate tramite un programma di installazione guidato al primo accesso dell'applicazione (da implementare autonomamente), per consentire all'amministratore di compilare manualmente e generare le configurazioni.
- Ripristinare gli altri file che devono essere ripristinati alla forma originale
- Dopo aver completato le operazioni sopra, entrare nella cartella `{progetto principale}/plugin/` e utilizzare il comando `zip -r foo.zip foo` per generare foo.zip
