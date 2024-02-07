# File di configurazione

La configurazione dei plugin segue lo stesso schema di un progetto webman normale, tuttavia la configurazione di un plugin di solito riguarda solo il plugin corrente e non ha generalmente alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.app.controller_suffix` influisce solo sul suffisso del controller del plugin, senza alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.app.controller_reuse` influisce solo sulla riutilizzo dei controller del plugin, senza alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.middleware` influisce solo sui middleware del plugin, senza alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.view` influisce solo sulla vista utilizzata dal plugin, senza alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.container` influisce solo sul container utilizzato dal plugin, senza alcun impatto sul progetto principale.
Ad esempio, il valore di `plugin.foo.exception` influisce solo sulla classe di gestione delle eccezioni del plugin, senza alcun impatto sul progetto principale.

Tuttavia, poiché i percorsi di routing sono globali, la configurazione dei percorsi del plugin influisce anche globalmente.

## Ottenere la configurazione
Il metodo per ottenere la configurazione di un plugin specifico è `config('plugin.{plugin}.{configurazione_specifica}')`, ad esempio per ottenere tutte le configurazioni di `plugin/foo/config/app.php` il metodo è `config('plugin.foo.app')`.
Allo stesso modo, il progetto principale o altri plugin possono utilizzare `config('plugin.foo.xxx')` per ottenere la configurazione del plugin foo.

## Configurazioni non supportate
Le applicazioni plugin non supportano le configurazioni server.php e session.php, non supportano le configurazioni `app.request_class`, `app.public_path`, `app.runtime_path`.
