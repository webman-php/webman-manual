# File di configurazione

La configurazione del plugin è simile a quella di un normale progetto webman, tuttavia la configurazione del plugin di solito è valida solo per il plugin attuale e di solito non ha alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.app.controller_suffix` influisce solo sul suffisso del controller del plugin e non ha alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.app.controller_reuse` influisce solo su se il plugin riutilizza il controller e non ha alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.middleware` influisce solo sui middleware del plugin e non ha alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.view` influisce solo sulla visualizzazione utilizzata dal plugin e non ha alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.container` influisce solo sul contenitore utilizzato dal plugin e non ha alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.exception` influisce solo sulla classe di gestione delle eccezioni del plugin e non ha alcun impatto sul progetto principale.

Tuttavia, poiché il percorso è globale, la configurazione del percorso del plugin influisce anche globalmente.

## Ottenere la configurazione
Il metodo per ottenere la configurazione di un determinato plugin è `config('plugin.{plugin}.{configurazione_specifica}')`; ad esempio, per ottenere tutti i valori di `plugin/foo/config/app.php` il metodo è `config('plugin.foo.app')`.
Allo stesso modo, il progetto principale o altri plugin possono utilizzare `config('plugin.foo.xxx')` per ottenere la configurazione del plugin foo.

## Configurazioni non supportate
I plugin dell'applicazione non supportano le configurazioni server.php, session.php e non supportano le configurazioni `app.request_class`, `app.public_path`, `app.runtime_path`.
