# Plugin di base

I plugin di base sono generalmente componenti comuni che vengono di solito installati tramite composer e il codice viene posizionato nella directory vendor. Durante l'installazione, è possibile copiare automaticamente alcune configurazioni personalizzate (middleware, processi, configurazioni del percorso, ecc.) nella directory `{progetto principale}/config/plugin`, e webman riconoscerà automaticamente queste configurazioni e le unirà alle configurazioni principali. In questo modo, i plugin possono intervenire in qualsiasi fase del ciclo di vita di webman.

Per ulteriori informazioni, consultare [Creazione di plugin di base](create.md)
