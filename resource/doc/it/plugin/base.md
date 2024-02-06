# Plugin di base

I plugin di base sono generalmente dei componenti comuni che vengono installati tramite composer e il cui codice viene posizionato nella cartella vendor. Durante l'installazione, è possibile copiare automaticamente alcune configurazioni personalizzate (come middleware, processi, route, ecc.) nella directory `{main_project}config/plugin`, affinché webman possa automaticamente riconoscere tali configurazioni e unirle alla configurazione principale. In questo modo, i plugin possono intervenire in qualsiasi fase del ciclo di vita di webman.

Per ulteriori informazioni, vedi [Creazione di plugin di base](create.md)
