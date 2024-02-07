# Sicurezza

## Utente in esecuzione
Si consiglia di impostare l'utente in esecuzione su un utente con privilegi più bassi, ad esempio lo stesso utente che esegue nginx. L'utente in esecuzione deve essere configurato nei campi `user` e `group` nel file `config/server.php`. Allo stesso modo, l'utente dei processi personalizzati è specificato tramite `user` e `group` nel file `config/process.php`. Si noti che il processo di monitoraggio non deve avere un utente in esecuzione poiché richiede privilegi elevati per funzionare correttamente.

## Convenzione dei controller
Nella directory `controller` o nelle sue sottodirectory è consentito posizionare solo file di controller, è proibito posizionare altri tipi di file. In caso contrario, senza l'opzione [suffix del controller](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) attivata, potrebbe verificarsi un accesso non autorizzato agli URL dei file di classe, con conseguenze imprevedibili. Ad esempio, `app/controller/model/User.php` potrebbe essere effettivamente una classe Model, ma erroneamente collocata nella directory `controller`. Senza l'opzione [suffix del controller](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) attivata, ciò potrebbe consentire agli utenti di accedere a qualsiasi metodo di `User.php` tramite URL come `/model/user/xxx`. Per evitare questo tipo di situazione, si consiglia vivamente di utilizzare il [suffix del controller](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) per identificare chiaramente i file dei controller.

## Filtraggio XSS
Per motivi di universalità, webman non esegue l'escape XSS delle richieste. webman consiglia vivamente di eseguire l'escape XSS durante il rendering e non prima dell'inserimento nel database. Inoltre, template come twig, blade, think-template eseguiranno automaticamente l'escape XSS, eliminando la necessità di farlo manualmente, il che è estremamente comodo.

> **Nota**
> Se esegui l'escape XSS prima dell'inserimento nel database, è molto probabile che si verifichino problemi di incompatibilità con alcuni plugin dell'applicazione.

## Prevenzione delle iniezioni SQL
Per prevenire le iniezioni SQL, si consiglia vivamente di utilizzare un ORM come [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) o [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), evitando di comporre manualmente query SQL.

## Proxy Nginx
Quando la tua applicazione deve essere esposta agli utenti esterni, è vivamente consigliato aggiungere un proxy Nginx prima di webman per filtrare alcune richieste HTTP non valide e migliorare la sicurezza complessiva. Per ulteriori dettagli, consulta [proxy Nginx](nginx-proxy.md).
