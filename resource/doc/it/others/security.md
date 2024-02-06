# Sicurezza

## Utente in esecuzione
Si consiglia di impostare l'utente in esecuzione come un utente con privilegi più bassi, ad esempio lo stesso utente che esegue nginx. L'utente in esecuzione viene impostato in `config/server.php` nei campi `user` e `group`. Un utente personalizzato per i processi è specificato attraverso `user` e `group` in `config/process.php`. È importante notare che il processo di monitoraggio non deve avere un utente in esecuzione, in quanto ha bisogno di autorizzazioni elevate per funzionare correttamente.

## Normativa sui controller
La cartella `controller` o le sue sottocartelle possono solo contenere file di controller, è vietato inserire altri tipi di file, in caso contrario, senza l'opzione [suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) del controller attivata, è possibile che i file di classe vengano accessibili tramite URL in modo non autorizzato, causando conseguenze impreviste. Ad esempio, `app/controller/model/User.php` potrebbe essere effettivamente una classe Model, ma è stata erroneamente inserita nella cartella `controller`, senza l'opzione [suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) del controller attivata, ciò potrebbe consentire agli utenti di accedere a qualsiasi metodo di `User.php` attraverso URL come `/model/user/xxx`. Per evitare completamente questa situazione, è fortemente consigliato utilizzare il [suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) del controller per specificare chiaramente quali file sono file di controller.

## Filtraggio XSS
Per motivi di compatibilità, webman non esegue l'escaping degli XSS sulle richieste. Webman consiglia vivamente di eseguire l'escaping degli XSS durante il rendering e non prima dell'ingresso nel database. Inoltre, i template come twig, blade, think-tmplate eseguiranno automaticamente l'escaping degli XSS, quindi non è necessario farlo manualmente, è molto conveniente.

> **Suggerimento**
> Se esegui l'escaping degli XSS prima di inserire nel database, è molto probabile che si verifichino problemi di incompatibilità con alcuni plugin.

## Prevenzione delle SQL injection
Per prevenire le SQL injection, si consiglia vivamente di utilizzare ORM, come [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html), [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), e di evitare il più possibile di comporre manualmente le query SQL.

## Proxy nginx
Quando la tua applicazione deve essere esposta agli utenti esterni, è vivamente consigliato aggiungere un proxy nginx davanti a webman in modo da filtrare alcune richieste HTTP non valide, migliorando la sicurezza. Per ulteriori informazioni, consulta [nginx proxy](nginx-proxy.md).
