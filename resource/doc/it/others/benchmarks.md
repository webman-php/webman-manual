# Test di stress

## Quali sono i fattori che influenzano i risultati del test di stress?
* La latenza di rete dalla macchina di stress al server (si consiglia di effettuare il test nella rete locale o sulla stessa macchina)
* La larghezza di banda dalla macchina di stress al server (si consiglia di effettuare il test nella rete locale o sulla stessa macchina)
* Se è attivato l'HTTP keep-alive (si consiglia di attivarlo)
* Se il numero di connessioni simultanee è sufficiente (nel caso del test in rete esterna, è consigliabile aumentare le connessioni simultanee)
* Se il numero di processi server è adeguato (si consiglia di avere lo stesso numero di processi del numero di CPU per i servizi di helloworld, mentre per i servizi di database si consiglia di avere almeno quattro volte il numero di CPU)
* Le prestazioni del servizio stesso (ad esempio, se viene utilizzato un database esterno)

## Cosa significa HTTP keep-alive?
Il meccanismo HTTP Keep-Alive è una tecnologia utilizzata per inviare più richieste e risposte HTTP su una singola connessione TCP. Questo meccanismo ha un forte impatto sui risultati del test di prestazioni, e disabilitando il keep-alive potrebbe ridurre drasticamente la quantità di richieste al secondo (QPS). Attualmente, i browser sono configurati per attivare automaticamente il keep-alive, consentendo così di mantenere la connessione aperta dopo la prima richiesta e di riutilizzarla per le successive richieste, contribuendo ad aumentare le prestazioni. Si consiglia di attivare il keep-alive durante il test di stress.

## Come attivare l'HTTP keep-alive durante il test di stress?
Se si utilizza il programma ab per il test di stress, è necessario aggiungere il parametro -k, ad esempio `ab -n100000 -c200 -k http://127.0.0.1:8787/`. Per apipost, è necessario restituire l'intestazione gzip per abilitare il keep-alive (a causa di un bug in apipost, fare riferimento qui sotto). Di solito, altri programmi di test di stress lo attivano per impostazione predefinita.

## Perché i QPS sono così bassi durante il test di stress in rete esterna?
L'alta latenza della rete esterna può ridurre notevolmente i QPS, ed è un fenomeno normale. Ad esempio, testare la pagina di Baidu potrebbe produrre solo alcuni QPS. Si consiglia di effettuare il test nella rete locale o sulla stessa macchina per escludere l'influenza della latenza di rete. Se è essenziale effettuare test in rete esterna, è possibile aumentare il numero di connessioni simultanee per aumentare la capacità (a condizione che la larghezza di banda sia sufficiente).

## Perché le prestazioni diminuiscono dopo aver usato il proxy Nginx?
Nginx consuma risorse di sistema e la comunicazione tra Nginx e webman richiede anche una certa quantità di risorse. Tuttavia, poiché le risorse del sistema sono limitate e webman non può accedere a tutte le risorse del sistema, è normale che le prestazioni complessive del sistema possano diminuire. Per ridurre al minimo l'impatto sulle prestazioni causato dal proxy Nginx, si consiglia di considerare la disabilitazione del registro di Nginx (`access_log off;`), e di attivare il keep-alive tra Nginx e webman (vedi [proxy Nginx](nginx-proxy.md)). Inoltre, HTTPS consuma più risorse rispetto a HTTP a causa della necessità di eseguire l'handshake SSL/TLS, crittografia e decriptazione dei dati e un aumento delle dimensioni dei pacchetti che occupa più larghezza di banda, il che può portare a una diminuzione delle prestazioni. Se si effettua un test di stress su HTTPS con connessioni a breve termine (senza l'HTTP keep-alive attivo), ogni richiesta richiederà un ulteriore comunicazione per l'handshake SSL/TLS, riducendo notevolmente le prestazioni. Si consiglia di attivare l'HTTP keep-alive durante il test di stress su HTTPS.

## Come sapere se il sistema ha raggiunto il limite delle prestazioni?
In generale, quando la CPU raggiunge il 100%, significa che le prestazioni del sistema hanno raggiunto il limite. Se la CPU ha ancora risorse disponibili, significa che il limite non è stato raggiunto, e in questo caso è possibile aumentare il numero di connessioni simultanee per aumentare i QPS. Se aumentare le connessioni simultanee non aumenta i QPS, potrebbe essere un segnale che il numero di processi webman non è sufficiente, quindi è consigliabile aumentare il numero di processi webman. Se anche questo non aumenta le prestazioni, è consigliabile verificare se la larghezza di banda è sufficiente.

## Perché i risultati del test di stress mostrano che le prestazioni di webman sono inferiori rispetto al framework Gin di Go?
Il test di stress di [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) mostra che webman ha prestazioni superiori a Gin del 100% in tutti i parametri, inclusi test su testo puro, query al database, aggiornamenti al database, ecc. Se i risultati differiscono, potrebbe essere dovuto all'uso di ORM in webman che comporta una perdita significativa delle prestazioni. È possibile provare a confrontare webman con l'uso di PDO nativo e Gin con l'uso di SQL nativo.

## Quanto influirà sull'efficienza l'uso di ORM in webman?
Ecco un insieme di dati di test di stress:

**Ambiente**
Server Alibaba Cloud, 4 core, 4 GB, restituzione di un record JSON da una selezione casuale di 100.000 record.

**Se si utilizza PDO nativo**
Il QPS di webman è di 17.800.

**Se si utilizza Db::table() di Laravel**
Il QPS di webman scende a 9.400.

**Se si utilizza il Model di Laravel**
Il QPS di webman scende a 7.200.

I risultati di ThinkORM sono simili, con poche differenze. 

> **Nota**
> Anche se l'uso di ORM può comportare una perdita delle prestazioni, per la maggior parte delle attività è già sufficiente. Dovremmo trovare un equilibrio tra efficienza nello sviluppo, manutenibilità e prestazioni, anziché concentrarci esclusivamente sulle prestazioni.

## Perché il QPS durante il test di stress con apipost è così basso?
Il modulo di test di stress di apipost ha un bug che impedisce di mantenere il keep-alive se il server non restituisce l'intestazione gzip, causando una significativa diminuzione delle prestazioni. Per risolvere questo problema, è possibile comprimere i dati al momento della restituzione e aggiungere l'intestazione gzip, ad esempio:
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('ciao webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
Inoltre, apipost non è in grado di esercitare la pressione in modo soddisfacente in alcune condizioni, e questo si traduce in un QPS inferiore di circa il 50% rispetto ad ab, con lo stesso numero di connessioni simultanee. Si consiglia di utilizzare ab, wrk o altri software di test di pressione professionale anziché apipost.

## Impostare il numero appropriato di processi
webman di default avvia il numero di processi pari a quattro volte il numero di CPU. In effetti, per i servizi helloworld senza I/O di rete, il numero ottimale di processi per il test di stress è uguale al numero di core della CPU, per ridurre al minimo il tempo di commutazione dei processi. Se si tratta di servizi con I/O bloccanti come il database o Redis, il numero di processi può essere impostato da 3 a 8 volte il numero di core della CPU, poiché richiedono processi aggiuntivi per aumentare la concorrenza, e il costo della commutazione dei processi può essere considerato trascurabile rispetto all'I/O bloccante.

## Alcune linee guida per il test di stress

**Server cloud con 4 core e 4 GB, 16 processi, test in locale/rete locale**

| - | Attiva keep-alive | Disattiva keep-alive |
|--|-----|-----|
| hello world | da 80.000 a 160.000 QPS | da 10.000 a 30.000 QPS |
| Singola query al database | da 10.000 a 20.000 QPS | 10.000 QPS |

[**Dati del test di stress di terze parti da techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


## Esempi di comandi di test di stress

**ab**
```sh
# 100.000 richieste, 200 connessioni simultanee, attiva il keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 richieste, 200 connessioni simultanee, disattiva il keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```sh
# Test di stress con 200 connessioni simultanee per 10 secondi, attiva il keep-alive (default)
wrk -c 200 -d 10s http://example.com
```
